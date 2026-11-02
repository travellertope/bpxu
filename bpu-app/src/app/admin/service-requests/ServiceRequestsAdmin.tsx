'use client';

import { useState, useEffect, useCallback } from 'react';

interface ServiceRequest {
    id: number;
    type: 'work_placement' | 'interview_prep' | 'ambassadorship';
    status: string;
    data: Record<string, string | string[]>;
    notes: string;
    created_at: string;
    updated_at: string;
    attachment_url?: string;
    member?: { id: number; display_name: string; email: string };
}

type TypeFilter = 'all' | 'work_placement' | 'interview_prep' | 'ambassadorship';
type StatusFilter = 'all' | 'pending' | 'under_review' | 'accepted' | 'rejected' | 'completed';

const TYPE_TABS: { value: TypeFilter; label: string }[] = [
    { value: 'all', label: 'All' },
    { value: 'work_placement', label: 'Work Placement' },
    { value: 'interview_prep', label: 'Interview Prep' },
    { value: 'ambassadorship', label: 'Ambassadorship' },
];

const STATUS_OPTIONS: { value: string; label: string }[] = [
    { value: 'pending', label: 'Pending' },
    { value: 'under_review', label: 'Under Review' },
    { value: 'accepted', label: 'Accepted' },
    { value: 'rejected', label: 'Rejected' },
    { value: 'completed', label: 'Completed' },
];

const STATUS_BADGE: Record<string, string> = {
    pending: 'badge-amber',
    under_review: 'badge-purple',
    accepted: 'badge-green',
    rejected: 'badge-red',
    completed: 'badge-gray',
};

const TYPE_LABEL: Record<string, string> = {
    work_placement: 'Work Placement',
    interview_prep: 'Interview Prep',
    ambassadorship: 'Ambassadorship',
};

function fmtShort(iso: string) {
    return new Date(iso).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

function DataField({ label, value }: { label: string; value: string | string[] | undefined }) {
    if (!value || (Array.isArray(value) && value.length === 0)) return null;
    return (
        <div className="mb-3">
            <p className="text-[10px] font-bold uppercase text-text-3 mb-0.5">{label}</p>
            <p className="text-sm text-text" style={{ whiteSpace: 'pre-wrap' }}>
                {Array.isArray(value) ? value.join(', ') : value}
            </p>
        </div>
    );
}

export default function ServiceRequestsAdmin() {
    const [requests, setRequests] = useState<ServiceRequest[]>([]);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [typeFilter, setTypeFilter] = useState<TypeFilter>('all');
    const [statusFilter, setStatusFilter] = useState<StatusFilter>('all');
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [page, setPage] = useState(1);
    const perPage = 25;

    const [selected, setSelected] = useState<ServiceRequest | null>(null);
    const [note, setNote] = useState('');
    const [savingNote, setSavingNote] = useState(false);
    const [updatingStatus, setUpdatingStatus] = useState(false);
    const [actionError, setActionError] = useState('');

    const fetchRequests = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const params = new URLSearchParams({ page: String(page), per_page: String(perPage) });
            if (typeFilter !== 'all') params.set('type', typeFilter);
            if (statusFilter !== 'all') params.set('status', statusFilter);
            if (search.trim()) params.set('search', search.trim());
            const res = await fetch(`/api/admin/service-requests?${params}`);
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load requests');
            setRequests(data.requests || []);
            setTotal(data.total || 0);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load requests');
        } finally {
            setLoading(false);
        }
    }, [typeFilter, statusFilter, search, page]);

    useEffect(() => { fetchRequests(); }, [fetchRequests]);

    async function updateStatus(newStatus: string) {
        if (!selected) return;
        setUpdatingStatus(true);
        setActionError('');
        try {
            const res = await fetch(`/api/admin/service-requests/${selected.id}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newStatus }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to update');
            setSelected(r => r ? { ...r, status: newStatus } : r);
            fetchRequests();
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
            const res = await fetch(`/api/admin/service-requests/${selected.id}/note`, {
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
                {/* Type tabs */}
                <div className="flex flex-wrap gap-2 mb-4">
                    {TYPE_TABS.map(t => (
                        <button
                            key={t.value}
                            onClick={() => { setTypeFilter(t.value); setPage(1); }}
                            className={`px-3 py-1.5 rounded-lg text-xs font-bold border transition-colors ${typeFilter === t.value ? 'bg-brand text-white border-brand' : 'bg-white text-text-2 border-border hover:border-text-2'}`}
                        >
                            {t.label}
                        </button>
                    ))}
                </div>

                {/* Status + search */}
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
                            placeholder="Search member name…"
                            value={searchInput}
                            onChange={e => setSearchInput(e.target.value)}
                            style={{ minWidth: 200 }}
                        />
                        <button type="submit" className="btn btn-sm btn-primary">Search</button>
                    </form>
                </div>

                {error && <p className="text-sm text-red-500 mb-4">{error}</p>}

                {loading ? (
                    <div className="text-text-3 text-sm py-12 text-center">Loading requests…</div>
                ) : requests.length === 0 ? (
                    <div className="card card-p text-center text-text-3 text-sm py-12">No requests found.</div>
                ) : (
                    <>
                        <div className="flex flex-col gap-2">
                            {requests.map(req => (
                                <button
                                    key={req.id}
                                    onClick={() => { setSelected(req); setActionError(''); setNote(''); }}
                                    className="card card-p text-left w-full hover:shadow-md transition-shadow"
                                    style={{ borderColor: selected?.id === req.id ? 'var(--brand)' : undefined }}
                                >
                                    <div className="flex items-start justify-between gap-3 flex-wrap">
                                        <div className="min-w-0 flex-1">
                                            <div className="flex items-center gap-2 flex-wrap mb-1">
                                                <span className="badge badge-gray text-[10px]">{TYPE_LABEL[req.type] || req.type}</span>
                                                <span className={`badge ${STATUS_BADGE[req.status] || 'badge-gray'} text-[10px]`}>
                                                    {req.status.replace('_', ' ')}
                                                </span>
                                            </div>
                                            <p className="font-semibold text-sm">
                                                {req.member?.display_name || 'Unknown member'}
                                            </p>
                                            <p className="text-xs text-text-3 mt-0.5">{req.member?.email}</p>
                                        </div>
                                        <p className="text-xs text-text-3 flex-shrink-0">{fmtShort(req.created_at)}</p>
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
                                <span className="badge badge-gray text-[10px]">{TYPE_LABEL[selected.type] || selected.type}</span>
                                <p className="font-bold text-sm mt-1">{selected.member?.display_name}</p>
                                <p className="text-xs text-text-3">{selected.member?.email}</p>
                            </div>
                            <button onClick={() => setSelected(null)} className="text-text-3 hover:text-text text-lg leading-none flex-shrink-0">×</button>
                        </div>

                        <div className="mt-2">
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
                        <p className="text-[10px] font-bold uppercase text-text-3 mb-3">Submission</p>
                        {selected.type === 'work_placement' && <>
                            <DataField label="Level" value={selected.data.current_level as string} />
                            <DataField label="Areas of Interest" value={selected.data.areas_of_interest as string[]} />
                            <DataField label="Career Goals" value={selected.data.career_goals as string} />
                            <DataField label="How Heard" value={selected.data.how_heard as string} />
                        </>}
                        {selected.type === 'interview_prep' && <>
                            <DataField label="Role" value={selected.data.role as string} />
                            <DataField label="Organisation" value={selected.data.organisation as string} />
                            <DataField label="Interview Type" value={selected.data.interview_type as string} />
                            <DataField label="Industry" value={selected.data.industry as string} />
                            <DataField label="Interview Date" value={selected.data.interview_date as string} />
                            <DataField label="Rounds Completed" value={selected.data.rounds_completed as string} />
                            <DataField label="Specific Help" value={selected.data.specific_help as string} />
                        </>}
                        {selected.type === 'ambassadorship' && <>
                            <DataField label="Region" value={selected.data.region as string} />
                            <DataField label="Sector" value={selected.data.sector as string} />
                            <DataField label="Motivation" value={selected.data.motivation as string} />
                            <DataField label="Community Connection" value={selected.data.community_connection as string} />
                            <DataField label="How They'd Represent BPU" value={selected.data.representation as string} />
                            <DataField label="LinkedIn" value={selected.data.linkedin_url as string} />
                        </>}
                        {selected.attachment_url && (
                            <a href={selected.attachment_url} target="_blank" rel="noreferrer" className="btn btn-sm btn-ghost mt-2">
                                View Attachment ↗
                            </a>
                        )}
                        <p className="text-xs text-text-3 mt-2">Submitted {fmtShort(selected.created_at)}</p>
                    </div>

                    {/* Internal notes */}
                    <div className="card-p">
                        <p className="text-[10px] font-bold uppercase text-text-3 mb-2">Internal Notes</p>
                        {selected.notes && (
                            <pre className="text-xs text-text-2 whitespace-pre-wrap mb-3 p-2 rounded" style={{ background: 'var(--bg)', border: '1px solid var(--border)' }}>
                                {selected.notes}
                            </pre>
                        )}
                        {actionError && <p className="text-xs text-red-500 mb-2">{actionError}</p>}
                        <form onSubmit={addNote}>
                            <textarea
                                className="input-sm w-full mb-2"
                                rows={2}
                                placeholder="Add an internal note…"
                                value={note}
                                onChange={e => setNote(e.target.value)}
                                style={{ resize: 'vertical', fontSize: 12 }}
                            />
                            <button
                                type="submit"
                                className="btn btn-sm btn-ghost w-full"
                                disabled={savingNote || !note.trim()}
                            >
                                {savingNote ? 'Saving…' : 'Save Note'}
                            </button>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
