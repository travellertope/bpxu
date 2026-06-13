'use client';

import { useState, useEffect } from 'react';

interface EmailTemplate {
    key: string;
    label: string;
    subject: string;
    body: string;
    default_subject: string;
    default_body: string;
    variables: string[];
}

export default function EmailTemplatesAdmin() {
    const [templates, setTemplates] = useState<EmailTemplate[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [editing, setEditing] = useState<EmailTemplate | null>(null);
    const [formSubject, setFormSubject] = useState('');
    const [formBody, setFormBody] = useState('');
    const [saving, setSaving] = useState(false);
    const [saveError, setSaveError] = useState('');
    const [saveSuccess, setSaveSuccess] = useState('');

    useEffect(() => {
        async function load() {
            try {
                const res = await fetch('/api/paired/admin/email-templates');
                const data = await res.json();
                if (!res.ok) throw new Error(data.error || 'Failed to load templates.');
                setTemplates(data.templates || []);
            } catch (e) {
                setError(e instanceof Error ? e.message : 'Failed to load templates.');
            } finally {
                setLoading(false);
            }
        }
        load();
    }, []);

    function openEdit(tpl: EmailTemplate) {
        setEditing(tpl);
        setFormSubject(tpl.subject || tpl.default_subject);
        setFormBody(tpl.body || tpl.default_body);
        setSaveError('');
        setSaveSuccess('');
    }

    async function handleSave() {
        if (!editing) return;
        setSaving(true);
        setSaveError('');
        setSaveSuccess('');

        try {
            const res = await fetch('/api/paired/admin/email-templates', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ key: editing.key, subject: formSubject, body: formBody }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to save.');
            setTemplates(prev => prev.map(t =>
                t.key === editing.key ? { ...t, subject: formSubject, body: formBody } : t
            ));
            setSaveSuccess('Template saved.');
            setTimeout(() => setSaveSuccess(''), 3000);
        } catch (e) {
            setSaveError(e instanceof Error ? e.message : 'Failed to save.');
        } finally {
            setSaving(false);
        }
    }

    async function handleReset() {
        if (!editing) return;
        setSaving(true);
        setSaveError('');

        try {
            const res = await fetch('/api/paired/admin/email-templates', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ key: editing.key, subject: '', body: '' }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to reset.');
            setFormSubject(editing.default_subject);
            setFormBody(editing.default_body);
            setTemplates(prev => prev.map(t =>
                t.key === editing.key ? { ...t, subject: '', body: '' } : t
            ));
            setSaveSuccess('Reverted to default.');
            setTimeout(() => setSaveSuccess(''), 3000);
        } catch (e) {
            setSaveError(e instanceof Error ? e.message : 'Failed to reset.');
        } finally {
            setSaving(false);
        }
    }

    if (loading) return <div className="text-center text-sm text-text-2 py-12">Loading templates...</div>;
    if (error) return <div className="card card-p text-center py-10" style={{ color: 'var(--err)' }}>{error}</div>;

    return (
        <div className="space-y-5">
            {/* Template List */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {templates.map(tpl => (
                    <button
                        key={tpl.key}
                        onClick={() => openEdit(tpl)}
                        className="card card-p text-left"
                        style={{ cursor: 'pointer', border: editing?.key === tpl.key ? '2px solid var(--purple)' : undefined }}
                    >
                        <div className="flex items-center gap-2 mb-2">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                <polyline points="22,6 12,13 2,6"/>
                            </svg>
                            <span className="font-semibold text-sm">{tpl.label}</span>
                        </div>
                        <p className="text-xs text-text-3">
                            {tpl.subject || tpl.default_subject}
                        </p>
                        {!tpl.subject && !tpl.body && (
                            <span className="badge badge-purple mt-2" style={{ fontSize: '0.65rem' }}>Using default</span>
                        )}
                        {(tpl.subject || tpl.body) && (
                            <span className="badge badge-green mt-2" style={{ fontSize: '0.65rem' }}>Customised</span>
                        )}
                    </button>
                ))}
            </div>

            {/* Editor */}
            {editing && (
                <div className="card card-p space-y-4" style={{ maxWidth: 800 }}>
                    <div className="flex items-center justify-between">
                        <h2 className="text-lg font-bold">{editing.label}</h2>
                        <button onClick={() => setEditing(null)} className="btn btn-ghost btn-sm text-xs">Close</button>
                    </div>

                    {saveError && <div className="alert alert-red">{saveError}</div>}
                    {saveSuccess && <div className="alert alert-green">{saveSuccess}</div>}

                    {editing.variables.length > 0 && (
                        <div>
                            <p className="text-xs font-semibold text-text-3 mb-1">Available variables:</p>
                            <div className="flex flex-wrap gap-1">
                                {editing.variables.map(v => (
                                    <span key={v} className="font-mono text-xs px-2 py-0.5 rounded" style={{ background: 'var(--surface)', border: '1px solid var(--border)' }}>
                                        {`{{${v}}}`}
                                    </span>
                                ))}
                            </div>
                        </div>
                    )}

                    <div>
                        <label htmlFor="tpl-subject" className="field-label mb-1 block">Subject Line</label>
                        <input
                            id="tpl-subject"
                            type="text"
                            className="field-input"
                            value={formSubject}
                            onChange={e => setFormSubject(e.target.value)}
                        />
                    </div>

                    <div>
                        <label htmlFor="tpl-body" className="field-label mb-1 block">Email Body</label>
                        <textarea
                            id="tpl-body"
                            className="field-input"
                            rows={12}
                            value={formBody}
                            onChange={e => setFormBody(e.target.value)}
                            style={{ fontFamily: 'monospace', fontSize: '0.8rem', lineHeight: 1.6 }}
                        />
                    </div>

                    <div className="flex gap-2">
                        <button onClick={handleSave} disabled={saving} className="btn btn-purple">
                            {saving ? 'Saving...' : 'Save Template'}
                        </button>
                        <button onClick={handleReset} disabled={saving} className="btn btn-outline" type="button">
                            Reset to Default
                        </button>
                    </div>
                </div>
            )}
        </div>
    );
}
