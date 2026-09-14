'use client';

import { useState, useEffect, useCallback, useRef } from 'react';

interface NewsletterList {
    id: number;
    name: string;
    description: string;
    subscriber_count: number;
    created_at: string;
    updated_at: string;
}

interface Subscriber {
    id: number;
    email: string;
    first_name: string;
    last_name: string;
    name: string;
    wp_user_id: number | null;
    status: 'subscribed' | 'unsubscribed';
    source: string;
    created_at: string;
}

export default function ListsPanel() {
    const [lists, setLists] = useState<NewsletterList[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    const [newListName, setNewListName] = useState('');
    const [newListDescription, setNewListDescription] = useState('');
    const [creating, setCreating] = useState(false);

    const [selected, setSelected] = useState<NewsletterList | null>(null);
    const [subscribers, setSubscribers] = useState<Subscriber[]>([]);
    const [subTotal, setSubTotal] = useState(0);
    const [subLoading, setSubLoading] = useState(false);

    const [newEmail, setNewEmail] = useState('');
    const [newFirstName, setNewFirstName] = useState('');
    const [addingSub, setAddingSub] = useState(false);
    const [subError, setSubError] = useState('');

    const [importing, setImporting] = useState(false);
    const [importMessage, setImportMessage] = useState('');
    const fileInputRef = useRef<HTMLInputElement>(null);

    const fetchLists = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const res = await fetch('/api/paired/admin/newsletter/lists');
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load lists.');
            setLists(data.lists || []);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load lists.');
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => { fetchLists(); }, [fetchLists]);

    const fetchSubscribers = useCallback(async (listId: number) => {
        setSubLoading(true);
        try {
            const res = await fetch(`/api/paired/admin/newsletter/lists/${listId}/subscribers?per_page=100`);
            const data = await res.json();
            if (res.ok) {
                setSubscribers(data.subscribers || []);
                setSubTotal(data.total || 0);
            }
        } finally {
            setSubLoading(false);
        }
    }, []);

    function openList(list: NewsletterList) {
        setSelected(list);
        setSubError('');
        setImportMessage('');
        fetchSubscribers(list.id);
    }

    async function handleCreateList() {
        if (!newListName.trim()) return;
        setCreating(true);
        try {
            const res = await fetch('/api/paired/admin/newsletter/lists', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name: newListName, description: newListDescription }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to create list.');
            setNewListName('');
            setNewListDescription('');
            await fetchLists();
        } catch (e) {
            alert(e instanceof Error ? e.message : 'Failed to create list.');
        } finally {
            setCreating(false);
        }
    }

    async function handleDeleteList(list: NewsletterList) {
        if (!confirm(`Delete the list "${list.name}"? Subscribers are kept, but list membership is removed. This cannot be undone.`)) return;
        try {
            const res = await fetch(`/api/paired/admin/newsletter/lists/${list.id}`, { method: 'DELETE' });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to delete list.');
            if (selected?.id === list.id) setSelected(null);
            await fetchLists();
        } catch (e) {
            alert(e instanceof Error ? e.message : 'Failed to delete list.');
        }
    }

    async function handleAddSubscriber() {
        if (!selected || !newEmail.trim()) return;
        setAddingSub(true);
        setSubError('');
        try {
            const res = await fetch(`/api/paired/admin/newsletter/lists/${selected.id}/subscribers`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ email: newEmail, first_name: newFirstName }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to add subscriber.');
            setNewEmail('');
            setNewFirstName('');
            await fetchSubscribers(selected.id);
            await fetchLists();
        } catch (e) {
            setSubError(e instanceof Error ? e.message : 'Failed to add subscriber.');
        } finally {
            setAddingSub(false);
        }
    }

    async function handleRemoveSubscriber(sub: Subscriber) {
        if (!selected) return;
        try {
            const res = await fetch(`/api/paired/admin/newsletter/lists/${selected.id}/subscribers/${sub.id}`, { method: 'DELETE' });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to remove subscriber.');
            await fetchSubscribers(selected.id);
            await fetchLists();
        } catch (e) {
            alert(e instanceof Error ? e.message : 'Failed to remove subscriber.');
        }
    }

    async function handleCsvUpload(e: React.ChangeEvent<HTMLInputElement>) {
        const file = e.target.files?.[0];
        if (!file || !selected) return;
        setImporting(true);
        setImportMessage('');
        try {
            const csv = await file.text();
            const res = await fetch(`/api/paired/admin/newsletter/lists/${selected.id}/import-csv`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ csv }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to import CSV.');
            setImportMessage(`Imported ${data.imported} subscriber${data.imported !== 1 ? 's' : ''}${data.skipped ? ` (${data.skipped} skipped — invalid email).` : '.'}`);
            await fetchSubscribers(selected.id);
            await fetchLists();
        } catch (e) {
            setImportMessage(e instanceof Error ? e.message : 'Failed to import CSV.');
        } finally {
            setImporting(false);
            if (fileInputRef.current) fileInputRef.current.value = '';
        }
    }

    return (
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {/* Lists */}
            <div className="space-y-3">
                <div className="card card-p space-y-3">
                    <h2 className="text-lg font-bold">New List</h2>
                    <input
                        type="text"
                        className="field-input"
                        placeholder="List name (e.g. Mentorship Programme)"
                        value={newListName}
                        onChange={e => setNewListName(e.target.value)}
                    />
                    <input
                        type="text"
                        className="field-input"
                        placeholder="Description (optional)"
                        value={newListDescription}
                        onChange={e => setNewListDescription(e.target.value)}
                    />
                    <button onClick={handleCreateList} disabled={creating || !newListName.trim()} className="btn btn-purple btn-sm">
                        {creating ? 'Creating...' : '+ Create List'}
                    </button>
                </div>

                {loading ? (
                    <div className="text-center text-sm text-text-2 py-12">Loading lists...</div>
                ) : error ? (
                    <div className="card card-p text-center text-sm py-10" style={{ color: 'var(--err)' }}>{error}</div>
                ) : lists.length === 0 ? (
                    <div className="card card-p text-center py-10">
                        <p className="font-semibold text-text-2">No custom lists yet</p>
                        <p className="text-sm text-text-3 mt-1">Create a list to manage subscribers outside the standard member roles.</p>
                    </div>
                ) : (
                    <div className="space-y-2">
                        {lists.map(l => (
                            <div
                                key={l.id}
                                className="card card-p"
                                style={{ cursor: 'pointer', border: selected?.id === l.id ? '2px solid var(--purple)' : undefined }}
                                onClick={() => openList(l)}
                            >
                                <div className="flex items-start justify-between gap-2">
                                    <div className="min-w-0">
                                        <p className="text-sm font-semibold truncate">{l.name}</p>
                                        {l.description && <p className="text-xs text-text-3 mt-0.5 truncate">{l.description}</p>}
                                    </div>
                                    <span className="badge badge-gray" style={{ flexShrink: 0 }}>{l.subscriber_count}</span>
                                </div>
                                <div className="flex justify-end mt-2">
                                    <button
                                        onClick={e => { e.stopPropagation(); handleDeleteList(l); }}
                                        className="btn btn-ghost btn-sm text-xs"
                                        style={{ color: 'var(--err)' }}
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            {/* Subscribers for selected list */}
            <div className="card card-p space-y-4">
                {!selected ? (
                    <p className="text-sm text-text-2 py-12 text-center">Select a list to manage its subscribers.</p>
                ) : (
                    <>
                        <div className="flex items-center justify-between">
                            <h2 className="text-lg font-bold">{selected.name}</h2>
                            <span className="badge badge-gray">{subTotal} subscriber{subTotal !== 1 ? 's' : ''}</span>
                        </div>

                        <div className="flex gap-2">
                            <input
                                type="email"
                                className="field-input flex-1"
                                placeholder="email@example.com"
                                value={newEmail}
                                onChange={e => setNewEmail(e.target.value)}
                            />
                            <input
                                type="text"
                                className="field-input"
                                style={{ maxWidth: 140 }}
                                placeholder="First name"
                                value={newFirstName}
                                onChange={e => setNewFirstName(e.target.value)}
                            />
                            <button onClick={handleAddSubscriber} disabled={addingSub || !newEmail.trim()} className="btn btn-outline btn-sm">
                                {addingSub ? 'Adding...' : 'Add'}
                            </button>
                        </div>
                        {subError && <p className="text-xs" style={{ color: 'var(--err)' }}>{subError}</p>}

                        <div className="pt-2" style={{ borderTop: '1px solid var(--border)' }}>
                            <label className="field-label mb-1 block">Bulk import from CSV</label>
                            <p className="text-xs text-text-3 mb-2">Columns: email, first_name, last_name. A header row is detected automatically.</p>
                            <input ref={fileInputRef} type="file" accept=".csv,text/csv" onChange={handleCsvUpload} disabled={importing} className="text-sm" />
                            {importMessage && <p className="text-xs mt-1" style={{ color: importMessage.startsWith('Imported') ? 'var(--ok)' : 'var(--err)' }}>{importMessage}</p>}
                        </div>

                        <div className="pt-2" style={{ borderTop: '1px solid var(--border)' }}>
                            {subLoading ? (
                                <p className="text-sm text-text-2 py-6 text-center">Loading subscribers...</p>
                            ) : subscribers.length === 0 ? (
                                <p className="text-sm text-text-3 py-6 text-center">No subscribers in this list yet.</p>
                            ) : (
                                <div style={{ maxHeight: 360, overflowY: 'auto' }} className="space-y-1">
                                    {subscribers.map(s => (
                                        <div key={s.id} className="flex items-center justify-between gap-2 py-1.5" style={{ borderBottom: '1px solid var(--border)' }}>
                                            <div className="min-w-0">
                                                <p className="text-sm font-medium truncate">{s.name || s.email}</p>
                                                <p className="text-xs text-text-3 truncate">{s.email}</p>
                                            </div>
                                            <div className="flex items-center gap-2" style={{ flexShrink: 0 }}>
                                                {s.status === 'unsubscribed' && <span className="badge badge-red">unsubscribed</span>}
                                                <button
                                                    onClick={() => handleRemoveSubscriber(s)}
                                                    className="btn btn-ghost btn-sm text-xs"
                                                    style={{ color: 'var(--err)' }}
                                                >
                                                    Remove
                                                </button>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </>
                )}
            </div>
        </div>
    );
}
