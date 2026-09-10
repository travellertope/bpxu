'use client';

import { useState, useEffect, useCallback } from 'react';

interface Campaign {
    id: number;
    subject: string;
    body: string;
    audience: string;
    audience_label: string;
    status: 'draft' | 'sending' | 'sent' | 'failed';
    recipient_count: number;
    sent_count: number;
    failed_count: number;
    created_at: string;
    updated_at: string;
    sent_at: string | null;
}

interface AudienceSegment {
    key: string;
    label: string;
    count: number;
}

interface Settings {
    has_api_key: boolean;
    api_key_preview: string;
    from_email: string;
    from_name: string;
}

const STATUS_BADGE: Record<Campaign['status'], string> = {
    draft: 'badge-gray',
    sending: 'badge-purple',
    sent: 'badge-green',
    failed: 'badge-red',
};

export default function NewsletterAdmin() {
    const [campaigns, setCampaigns] = useState<Campaign[]>([]);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [segments, setSegments] = useState<AudienceSegment[]>([]);
    const [settings, setSettings] = useState<Settings | null>(null);

    const [editing, setEditing] = useState<Campaign | null>(null);
    const [subject, setSubject] = useState('');
    const [body, setBody] = useState('');
    const [audience, setAudience] = useState('all');
    const [saving, setSaving] = useState(false);
    const [formError, setFormError] = useState('');
    const [formSuccess, setFormSuccess] = useState('');

    const [testEmail, setTestEmail] = useState('');
    const [sendingTest, setSendingTest] = useState(false);
    const [sending, setSending] = useState(false);
    const [confirmSend, setConfirmSend] = useState<Campaign | null>(null);

    const [showSettings, setShowSettings] = useState(false);
    const [apiKeyInput, setApiKeyInput] = useState('');
    const [fromEmailInput, setFromEmailInput] = useState('');
    const [fromNameInput, setFromNameInput] = useState('');
    const [savingSettings, setSavingSettings] = useState(false);
    const [settingsMessage, setSettingsMessage] = useState('');

    const fetchCampaigns = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const res = await fetch('/api/paired/admin/newsletter/campaigns?per_page=50');
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load campaigns.');
            setCampaigns(data.campaigns || []);
            setTotal(data.total || 0);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load campaigns.');
        } finally {
            setLoading(false);
        }
    }, []);

    const fetchAudienceCounts = useCallback(async () => {
        try {
            const res = await fetch('/api/paired/admin/newsletter/audience-counts');
            const data = await res.json();
            if (res.ok) setSegments(data.segments || []);
        } catch { /* non-fatal */ }
    }, []);

    const fetchSettings = useCallback(async () => {
        try {
            const res = await fetch('/api/paired/admin/newsletter/settings');
            const data = await res.json();
            if (res.ok) {
                setSettings(data);
                setFromEmailInput(data.from_email || '');
                setFromNameInput(data.from_name || '');
            }
        } catch { /* non-fatal */ }
    }, []);

    useEffect(() => {
        fetchCampaigns();
        fetchAudienceCounts();
        fetchSettings();
    }, [fetchCampaigns, fetchAudienceCounts, fetchSettings]);

    function openNew() {
        setEditing(null);
        setSubject('');
        setBody('');
        setAudience('all');
        setTestEmail('');
        setFormError('');
        setFormSuccess('');
    }

    function openEdit(c: Campaign) {
        setEditing(c);
        setSubject(c.subject);
        setBody(c.body);
        setAudience(c.audience);
        setTestEmail('');
        setFormError('');
        setFormSuccess('');
    }

    async function handleSaveDraft() {
        setSaving(true);
        setFormError('');
        setFormSuccess('');
        try {
            const url = editing
                ? `/api/paired/admin/newsletter/campaigns/${editing.id}`
                : '/api/paired/admin/newsletter/campaigns';
            const res = await fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ subject, body, audience }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || data.message || 'Failed to save draft.');
            setEditing(data.campaign);
            setFormSuccess('Draft saved.');
            await fetchCampaigns();
            setTimeout(() => setFormSuccess(''), 3000);
        } catch (e) {
            setFormError(e instanceof Error ? e.message : 'Failed to save draft.');
        } finally {
            setSaving(false);
        }
    }

    async function handleDelete(c: Campaign) {
        if (!confirm(`Delete the draft "${c.subject || '(untitled)'}"? This cannot be undone.`)) return;
        try {
            const res = await fetch(`/api/paired/admin/newsletter/campaigns/${c.id}`, { method: 'DELETE' });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to delete.');
            if (editing?.id === c.id) openNew();
            await fetchCampaigns();
        } catch (e) {
            alert(e instanceof Error ? e.message : 'Failed to delete.');
        }
    }

    async function handleSendTest() {
        if (!editing) return;
        setSendingTest(true);
        setFormError('');
        setFormSuccess('');
        try {
            const res = await fetch(`/api/paired/admin/newsletter/campaigns/${editing.id}/test`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(testEmail ? { email: testEmail } : {}),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || data.message || 'Failed to send test.');
            setFormSuccess(`Test email sent to ${data.sent_to}.`);
            setTimeout(() => setFormSuccess(''), 4000);
        } catch (e) {
            setFormError(e instanceof Error ? e.message : 'Failed to send test.');
        } finally {
            setSendingTest(false);
        }
    }

    async function handleSendNow(c: Campaign) {
        setSending(true);
        setConfirmSend(null);
        try {
            const res = await fetch(`/api/paired/admin/newsletter/campaigns/${c.id}/send`, { method: 'POST' });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || data.message || 'Failed to send campaign.');
            if (editing?.id === c.id) setEditing(data.campaign);
            await fetchCampaigns();
        } catch (e) {
            alert(e instanceof Error ? e.message : 'Failed to send campaign.');
        } finally {
            setSending(false);
        }
    }

    async function handleSaveSettings() {
        setSavingSettings(true);
        setSettingsMessage('');
        try {
            const res = await fetch('/api/paired/admin/newsletter/settings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    ...(apiKeyInput ? { api_key: apiKeyInput } : {}),
                    from_email: fromEmailInput,
                    from_name: fromNameInput,
                }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to save settings.');
            setSettings(data);
            setApiKeyInput('');
            setSettingsMessage('Settings saved.');
            setTimeout(() => setSettingsMessage(''), 3000);
        } catch (e) {
            setSettingsMessage(e instanceof Error ? e.message : 'Failed to save settings.');
        } finally {
            setSavingSettings(false);
        }
    }

    const selectedSegment = segments.find(s => s.key === audience);
    const canEdit = !editing || editing.status === 'draft';

    return (
        <div className="space-y-5">
            {/* Settings banner */}
            <div className="card card-p flex items-center justify-between flex-wrap gap-3">
                <div>
                    <p className="text-sm font-semibold">SendGrid</p>
                    <p className="text-xs text-text-3">
                        {settings?.has_api_key
                            ? `Connected (key ending ${settings.api_key_preview.slice(-4)}) · Sending as ${settings.from_name} <${settings.from_email}>`
                            : 'No SendGrid API key configured — campaigns cannot be sent yet.'}
                    </p>
                </div>
                <button className="btn btn-outline btn-sm" onClick={() => setShowSettings(v => !v)}>
                    {showSettings ? 'Close' : 'Settings'}
                </button>
            </div>

            {showSettings && (
                <div className="card card-p space-y-3" style={{ maxWidth: 520 }}>
                    <div>
                        <label htmlFor="nl-api-key" className="field-label mb-1 block">SendGrid API Key</label>
                        <input
                            id="nl-api-key"
                            type="password"
                            className="field-input"
                            placeholder={settings?.has_api_key ? 'Leave blank to keep current key' : 'SG.xxxxxxxx...'}
                            value={apiKeyInput}
                            onChange={e => setApiKeyInput(e.target.value)}
                        />
                    </div>
                    <div>
                        <label htmlFor="nl-from-email" className="field-label mb-1 block">From Email</label>
                        <input
                            id="nl-from-email"
                            type="email"
                            className="field-input"
                            value={fromEmailInput}
                            onChange={e => setFromEmailInput(e.target.value)}
                        />
                    </div>
                    <div>
                        <label htmlFor="nl-from-name" className="field-label mb-1 block">From Name</label>
                        <input
                            id="nl-from-name"
                            type="text"
                            className="field-input"
                            value={fromNameInput}
                            onChange={e => setFromNameInput(e.target.value)}
                        />
                    </div>
                    {settingsMessage && <p className="text-xs" style={{ color: settingsMessage.includes('saved') ? 'var(--ok)' : 'var(--err)' }}>{settingsMessage}</p>}
                    <button onClick={handleSaveSettings} disabled={savingSettings} className="btn btn-purple btn-sm">
                        {savingSettings ? 'Saving...' : 'Save Settings'}
                    </button>
                </div>
            )}

            <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
                {/* Composer */}
                <div className="card card-p space-y-4">
                    <div className="flex items-center justify-between">
                        <h2 className="text-lg font-bold">{editing ? 'Edit Campaign' : 'New Campaign'}</h2>
                        {editing && (
                            <div className="flex items-center gap-2">
                                <span className={`badge ${STATUS_BADGE[editing.status]}`}>{editing.status}</span>
                                <button className="btn btn-ghost btn-sm text-xs" onClick={openNew}>New</button>
                            </div>
                        )}
                    </div>

                    {formError && <div className="alert alert-red">{formError}</div>}
                    {formSuccess && <div className="alert alert-green">{formSuccess}</div>}

                    <div>
                        <label htmlFor="nl-subject" className="field-label mb-1 block">Subject Line</label>
                        <input
                            id="nl-subject"
                            type="text"
                            className="field-input"
                            value={subject}
                            disabled={!canEdit}
                            onChange={e => setSubject(e.target.value)}
                            placeholder="This month's BPU newsletter"
                        />
                    </div>

                    <div>
                        <label htmlFor="nl-audience" className="field-label mb-1 block">Audience</label>
                        <select
                            id="nl-audience"
                            className="field-input"
                            value={audience}
                            disabled={!canEdit}
                            onChange={e => setAudience(e.target.value)}
                        >
                            {segments.length === 0 && <option value="all">All Members (Free + Pro)</option>}
                            {segments.map(s => (
                                <option key={s.key} value={s.key}>{s.label} ({s.count})</option>
                            ))}
                        </select>
                        {selectedSegment && (
                            <p className="text-xs text-text-3 mt-1">Will reach approximately {selectedSegment.count} recipient{selectedSegment.count !== 1 ? 's' : ''}.</p>
                        )}
                    </div>

                    <div>
                        <label htmlFor="nl-body" className="field-label mb-1 block">Email Body</label>
                        <p className="text-xs text-text-3 mb-1">Use <code>{'{{name}}'}</code> to personalise. An unsubscribe link is added automatically.</p>
                        <textarea
                            id="nl-body"
                            className="field-input"
                            rows={12}
                            value={body}
                            disabled={!canEdit}
                            onChange={e => setBody(e.target.value)}
                            style={{ fontFamily: 'monospace', fontSize: '0.8rem', lineHeight: 1.6 }}
                            placeholder={"Hi {{name}},\r\n\r\nHere's what's new at BPU this month..."}
                        />
                    </div>

                    {canEdit && (
                        <div className="flex flex-wrap gap-2">
                            <button onClick={handleSaveDraft} disabled={saving || !subject || !body} className="btn btn-outline">
                                {saving ? 'Saving...' : 'Save Draft'}
                            </button>
                            {editing && (
                                <button
                                    onClick={() => setConfirmSend(editing)}
                                    disabled={sending || !settings?.has_api_key}
                                    className="btn btn-purple"
                                >
                                    {sending ? 'Sending...' : 'Send Now'}
                                </button>
                            )}
                        </div>
                    )}

                    {editing && canEdit && (
                        <div className="pt-3" style={{ borderTop: '1px solid var(--border)' }}>
                            <label htmlFor="nl-test-email" className="field-label mb-1 block">Send a test</label>
                            <div className="flex gap-2">
                                <input
                                    id="nl-test-email"
                                    type="email"
                                    className="field-input flex-1"
                                    placeholder="you@example.com (defaults to your account email)"
                                    value={testEmail}
                                    onChange={e => setTestEmail(e.target.value)}
                                />
                                <button onClick={handleSendTest} disabled={sendingTest || !settings?.has_api_key} className="btn btn-outline btn-sm">
                                    {sendingTest ? 'Sending...' : 'Send Test'}
                                </button>
                            </div>
                        </div>
                    )}

                    {!canEdit && editing && (
                        <p className="text-xs text-text-3">
                            {editing.status === 'sending' && `Sending... ${editing.sent_count}/${editing.recipient_count} delivered so far.`}
                            {editing.status === 'sent' && `Sent to ${editing.sent_count} of ${editing.recipient_count} recipients${editing.failed_count ? ` (${editing.failed_count} failed)` : ''} on ${editing.sent_at ? new Date(editing.sent_at).toLocaleString('en-GB') : ''}.`}
                            {editing.status === 'failed' && 'This campaign failed to send. Check your SendGrid API key and try again.'}
                        </p>
                    )}
                </div>

                {/* Campaign list */}
                <div className="space-y-3">
                    <div className="flex items-center justify-between">
                        <h2 className="text-lg font-bold">Campaigns</h2>
                        <button className="btn btn-purple btn-sm" onClick={openNew}>+ New Campaign</button>
                    </div>

                    {loading ? (
                        <div className="text-center text-sm text-text-2 py-12">Loading campaigns...</div>
                    ) : error ? (
                        <div className="card card-p text-center text-sm py-10" style={{ color: 'var(--err)' }}>{error}</div>
                    ) : campaigns.length === 0 ? (
                        <div className="card card-p text-center py-10">
                            <p className="font-semibold text-text-2">No campaigns yet</p>
                            <p className="text-sm text-text-3 mt-1">Create your first newsletter to get started.</p>
                        </div>
                    ) : (
                        <>
                            <p className="text-sm text-text-3">{total} campaign{total !== 1 ? 's' : ''}</p>
                            <div className="space-y-2">
                                {campaigns.map(c => (
                                    <div
                                        key={c.id}
                                        className="card card-p"
                                        style={{ cursor: 'pointer', border: editing?.id === c.id ? '2px solid var(--purple)' : undefined }}
                                        onClick={() => openEdit(c)}
                                    >
                                        <div className="flex items-start justify-between gap-2">
                                            <div className="min-w-0">
                                                <p className="text-sm font-semibold truncate">{c.subject || '(untitled)'}</p>
                                                <p className="text-xs text-text-3 mt-0.5">{c.audience_label}</p>
                                            </div>
                                            <span className={`badge ${STATUS_BADGE[c.status]}`} style={{ flexShrink: 0 }}>{c.status}</span>
                                        </div>
                                        <div className="flex items-center justify-between mt-2">
                                            <span className="text-xs text-text-3">
                                                {c.status === 'sent' || c.status === 'sending'
                                                    ? `${c.sent_count}/${c.recipient_count} sent`
                                                    : `Updated ${new Date(c.updated_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short' })}`}
                                            </span>
                                            {(c.status === 'draft' || c.status === 'failed') && (
                                                <button
                                                    onClick={e => { e.stopPropagation(); handleDelete(c); }}
                                                    className="btn btn-ghost btn-sm text-xs"
                                                    style={{ color: 'var(--err)' }}
                                                >
                                                    Delete
                                                </button>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </>
                    )}
                </div>
            </div>

            {/* Confirm send modal */}
            {confirmSend && (
                <div
                    style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 100, padding: 16 }}
                    onClick={() => setConfirmSend(null)}
                >
                    <div className="card card-p" style={{ width: '100%', maxWidth: 420 }} onClick={e => e.stopPropagation()}>
                        <h2 className="text-lg font-bold mb-2">Send Campaign</h2>
                        <p className="text-sm text-text-2 mb-6">
                            Send &quot;<strong>{confirmSend.subject}</strong>&quot; to <strong>{selectedSegment?.label || confirmSend.audience_label}</strong>
                            {selectedSegment ? ` (${selectedSegment.count} recipients)` : ''}? This cannot be undone.
                        </p>
                        <div className="flex gap-2">
                            <button
                                onClick={() => handleSendNow(confirmSend)}
                                className="btn flex-1"
                                style={{ background: 'var(--purple)', color: '#fff', border: 'none' }}
                            >
                                Yes, Send Now
                            </button>
                            <button onClick={() => setConfirmSend(null)} className="btn btn-outline">Cancel</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
