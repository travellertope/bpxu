'use client';

import { useState, useEffect, useCallback } from 'react';

interface PartnershipEnquiry {
    id: number;
    status: string;
    data: {
        org_name?: string;
        contact_name?: string;
        email?: string;
        phone?: string;
        website?: string;
        org_type?: string;
        interests?: string;
        message?: string;
    };
    notes: string;
    created_at: string;
    updated_at: string;
}

type StatusFilter = 'all' | 'new' | 'in_progress' | 'converted' | 'declined';

const STATUS_OPTIONS: { value: string; label: string }[] = [
    { value: 'new',         label: 'New' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'converted',   label: 'Converted' },
    { value: 'declined',    label: 'Declined' },
];

const STATUS_BADGE: Record<string, string> = {
    new:         'badge-amber',
    in_progress: 'badge-purple',
    converted:   'badge-green',
    declined:    'badge-red',
};

function fmtShort(iso: string) {
    return new Date(iso).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

function DataField({ label, value }: { label: string; value: string | undefined }) {
    if (!value) return null;
    return (
        <div className="mb-3">
            <p className="text-[10px] font-bold uppercase text-text-3 mb-0.5">{label}</p>
            <p className="text-sm text-text" style={{ whiteSpace: 'pre-wrap' }}>{value}</p>
        </div>
    );
}

export default function PartnershipSubmissionsAdmin() {
    const [enquiries, setEnquiries]         = useState<PartnershipEnquiry[]>([]);
    const [total, setTotal]                 = useState(0);
    const [loading, setLoading]             = useState(true);
    const [error, setError]                 = useState('');
    const [statusFilter, setStatusFilter]   = useState<StatusFilter>('all');
    const [search, setSearch]               = useState('');
    const [searchInput, setSearchInput]     = useState('');
    const [page, setPage]                   = useState(1);
    const perPage = 25;

    const [selected, setSelected]           = useState<PartnershipEnquiry | null>(null);
    const [note, setNote]                   = useState('');
    const [savingNote, setSavingNote]       = useState(false);
    const [updatingStatus, setUpdatingStatus] = useState(false);
    const [actionError, setActionError]     = useState('');

    const fetchEnquiries = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const params = new URLSearchParams({ page: String(page), per_page: String(perPage) });
            if (statusFilter !== 'all') params.set('status', statusFilter);
            if (search.trim())          params.set('search', search.trim());
            const res  = await fetch(`/api/admin/partnerships?${params}`);
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load enquiries');
            setEnquiries(data.enquiries || []);
            setTotal(data.total || 0);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load enquiries');
        } finally {
            setLoading(false);
        }
    }, [statusFilter, search, page]);

    useEffect(() => { fetchEnquiries(); }, [fetchEnquiries]);

    async function updateStatus(newStatus: string) {
        if (!selected) return;
        setUpdatingStatus(true);
        setActionError('');
        try {
            const res = await fetch(`/api/admin/partnerships/${selected.id}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newStatus }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to update');
            setSelected(r => r ? { ...r, status: newStatus } : r);
            fetchEnquiries();
        } catch (e) {
            setActionError(e instanceof Error ? e.message : 'Failed to update');
        } finally {
            setUpdatingStatus(false);
        }
    }

    async function addNote(e: React.FormEvent) {
        e.preventDefault();
        if (!selected || !note.trim()) return;
        setSavingNote(true);
        setActionError('');
        try {
            const res = await fetch(`/api/admin/partnerships/${selected.id}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ note }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to save note');
            const appended = selected.notes ? selected.notes + '\n\n' + data.note : data.note;
            setSelected(r => r ? { ...r, notes: appended } : r);
            setNote('');
        } catch (e) {
            setActionError(e instanceof Error ? e.message : 'Failed to save note');
        } finally {
            setSavingNote(false);
        }
    }

    const pages = Math.ceil(total / perPage);

    return (
        <div className="flex gap-6" style={{ alignItems: 'flex-start' }}>
            {/* Left: list */}
            <div className="flex-1 min-w-0">
                {/* Summary */}
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                    {['new', 'in_progress', 'converted', 'declined'].map(s => {
                        const count = enquiries.filter(e => e.status === s).length;
                        return (
                            <button
                                key={s}
                                onClick={() => { setStatusFilter(s as StatusFilter); setPage(1); }}
                                className={`card card-p text-center transition-shadow ${statusFilter === s ? 'ring-2 ring-brand' : ''}`}
                            >
                                <p className="text-xs text-text-3 mb-1 capitalize">{s.replace('_', ' ')}</p>
                                <p className="text-xl font-bold">{count}</p>
                            </button>
                        );
                    })}
                </div>

                {/* Filters */}
                <div className="flex flex-wrap gap-3 mb-5 items-center">
                    <select
                        className="input-sm"
                        value={statusFilter}
                        onChange={e => { setStatusFilter(e.target.value as StatusFilter); setPage(1); }}
                    >
                        <option value="all">All Statuses</option>
                        {STATUS_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                    </select>
                    <form className="flex gap-2 ml-auto" onSubmit={e => { e.preventDefault(); setSearch(searchInput); setPage(1); }}>
                        <input
                            className="input-sm"
                            placeholder="Search organisation or email…"
                            value={searchInput}
                            onChange={e => setSearchInput(e.target.value)}
                            style={{ minWidth: 220 }}
                        />
                        <button type="submit" className="btn btn-sm btn-primary">Search</button>
                        {search && (
                            <button type="button" className="btn btn-sm btn-ghost" onClick={() => { setSearch(''); setSearchInput(''); setPage(1); }}>
                                Clear
                            </button>
                        )}
                    </form>
                </div>

                {error && <p className="text-sm text-red-500 mb-4">{error}</p>}

                {loading ? (
                    <div className="text-text-3 text-sm py-12 text-center">Loading enquiries…</div>
                ) : enquiries.length === 0 ? (
                    <div className="card card-p text-center text-text-3 text-sm py-12">No enquiries found.</div>
                ) : (
                    <>
                        <div className="flex flex-col gap-2">
                            {enquiries.map(enq => (
                                <button
                                    key={enq.id}
                                    onClick={() => { setSelected(enq); setActionError(''); setNote(''); }}
                                    className="card card-p text-left w-full hover:shadow-md transition-shadow"
                                    style={{ borderColor: selected?.id === enq.id ? 'var(--brand)' : undefined }}
                                >
                                    <div className="flex items-start justify-between gap-3 flex-wrap">
                                        <div className="min-w-0 flex-1">
                                            <div className="flex items-center gap-2 flex-wrap mb-1">
                                                <span className={`badge ${STATUS_BADGE[enq.status] || 'badge-gray'} text-[10px]`}>
                                                    {enq.status.replace('_', ' ')}
                                                </span>
                                                {enq.data.org_type && (
                                                    <span className="badge badge-gray text-[10px]">{enq.data.org_type}</span>
                                                )}
                                            </div>
                                            <p className="font-semibold text-sm">{enq.data.org_name || 'Unknown organisation'}</p>
                                            <p className="text-xs text-text-3 mt-0.5">
                                                {enq.data.contact_name && <span>{enq.data.contact_name} · </span>}
                                                {enq.data.email}
                                            </p>
                                        </div>
                                        <p className="text-xs text-text-3 flex-shrink-0">{fmtShort(enq.created_at)}</p>
                                    </div>
                                </button>
                            ))}
                        </div>

                        {pages > 1 && (
                            <div className="flex justify-center gap-2 mt-6">
                                <button className="btn btn-sm btn-ghost" disabled={page === 1} onClick={() => setPage(p => p - 1)}>← Prev</button>
                                <span className="text-sm text-text-2 self-center">Page {page} of {pages}</span>
                                <button className="btn btn-sm btn-ghost" disabled={page >= pages} onClick={() => setPage(p => p + 1)}>Next →</button>
                            </div>
                        )}
                    </>
                )}
            </div>

            {/* Right: detail panel */}
            {selected && (
                <div className="card" style={{ width: 440, flexShrink: 0, position: 'sticky', top: 24, maxHeight: 'calc(100vh - 120px)', display: 'flex', flexDirection: 'column', overflowY: 'auto' }}>
                    <div className="card-p" style={{ borderBottom: '1px solid var(--border)' }}>
                        <div className="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <p className="font-bold text-sm">{selected.data.org_name}</p>
                                {selected.data.contact_name && (
                                    <p className="text-xs text-text-3">{selected.data.contact_name}</p>
                                )}
                                <p className="text-xs text-text-3">{selected.data.email}</p>
                            </div>
                            <button onClick={() => setSelected(null)} className="text-text-3 hover:text-text text-lg leading-none flex-shrink-0">×</button>
                        </div>

                        <div className="mt-3">
                            <label className="text-xs text-text-3 font-semibold block mb-1">Status</label>
                            <div className="flex gap-1 flex-wrap">
                                {STATUS_OPTIONS.map(opt => (
                                    <button
                                        key={opt.value}
                                        onClick={() => updateStatus(opt.value)}
                                        disabled={updatingStatus || selected.status === opt.value}
                                        className={`px-2 py-1 rounded text-[10px] font-bold border transition-colors ${selected.status === opt.value ? 'bg-brand text-white border-brand' : 'bg-white text-text-2 border-border hover:border-text-2'}`}
                                    >
                                        {opt.label}
                                    </button>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Submission data */}
                    <div className="card-p" style={{ borderBottom: '1px solid var(--border)' }}>
                        <p className="text-[10px] font-bold uppercase text-text-3 mb-3">Submission Details</p>
                        <DataField label="Organisation"      value={selected.data.org_name} />
                        <DataField label="Contact Name"      value={selected.data.contact_name} />
                        <DataField label="Email"             value={selected.data.email} />
                        <DataField label="Phone"             value={selected.data.phone} />
                        <DataField label="Website"           value={selected.data.website} />
                        <DataField label="Organisation Type" value={selected.data.org_type} />
                        <DataField label="Areas of Interest" value={selected.data.interests} />
                        <DataField label="Message"           value={selected.data.message} />
                        {selected.data.website && (
                            <a href={selected.data.website} target="_blank" rel="noreferrer" className="btn btn-sm btn-ghost mt-1">
                                Visit Website ↗
                            </a>
                        )}
                        {selected.data.email && (
                            <a href={`mailto:${selected.data.email}`} className="btn btn-sm btn-ghost mt-1 ml-2">
                                Reply by Email ↗
                            </a>
                        )}
                        <p className="text-xs text-text-3 mt-3">Submitted {fmtShort(selected.created_at)}</p>
                    </div>

                    {/* Notes */}
                    <div className="card-p flex-1">
                        <p className="text-[10px] font-bold uppercase text-text-3 mb-3">Internal Notes</p>
                        {selected.notes ? (
                            <div className="text-sm text-text bg-surface rounded-lg p-3 mb-3 whitespace-pre-wrap" style={{ maxHeight: 180, overflowY: 'auto' }}>
                                {selected.notes}
                            </div>
                        ) : (
                            <p className="text-xs text-text-3 mb-3">No notes yet.</p>
                        )}
                        <form onSubmit={addNote} className="flex flex-col gap-2">
                            <textarea
                                className="input-sm resize-none text-sm"
                                rows={3}
                                placeholder="Add a note…"
                                value={note}
                                onChange={e => setNote(e.target.value)}
                            />
                            {actionError && <p className="text-xs text-red-500">{actionError}</p>}
                            <button type="submit" disabled={savingNote || !note.trim()} className="btn btn-sm btn-primary self-end">
                                {savingNote ? 'Saving…' : 'Save Note'}
                            </button>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
