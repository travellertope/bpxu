'use client';

import { useState, useEffect, useCallback } from 'react';

interface TicketMessage {
    id: number;
    author: string;
    author_type: 'member' | 'staff';
    content: string;
    created_at: string;
}

interface Ticket {
    id: number;
    ref: string;
    category: string;
    subject: string;
    urgency: 'normal' | 'high';
    status: 'open' | 'in_progress' | 'resolved' | 'closed';
    created_at: string;
    updated_at: string;
    message_count: number;
    member?: { id: number; display_name: string; email: string };
    messages?: TicketMessage[];
}

type StatusFilter = 'all' | 'open' | 'in_progress' | 'resolved' | 'closed';

const STATUS_TABS: { value: StatusFilter; label: string }[] = [
    { value: 'all', label: 'All' },
    { value: 'open', label: 'Open' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'resolved', label: 'Resolved' },
    { value: 'closed', label: 'Closed' },
];

const STATUS_BADGE: Record<string, string> = {
    open: 'badge-blue',
    in_progress: 'badge-amber',
    resolved: 'badge-green',
    closed: 'badge-gray',
};

const STATUS_OPTIONS = [
    { value: 'open', label: 'Open' },
    { value: 'in_progress', label: 'In Progress' },
    { value: 'resolved', label: 'Resolved' },
    { value: 'closed', label: 'Closed' },
];

function fmt(iso: string) {
    return new Date(iso).toLocaleString('en-GB', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
}

function fmtShort(iso: string) {
    return new Date(iso).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

export default function SupportAdmin() {
    const [tickets, setTickets] = useState<Ticket[]>([]);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [statusFilter, setStatusFilter] = useState<StatusFilter>('all');
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [page, setPage] = useState(1);
    const perPage = 25;

    // Detail drawer state
    const [selected, setSelected] = useState<Ticket | null>(null);
    const [detailLoading, setDetailLoading] = useState(false);
    const [reply, setReply] = useState('');
    const [sendingReply, setSendingReply] = useState(false);
    const [updatingStatus, setUpdatingStatus] = useState(false);
    const [actionError, setActionError] = useState('');

    const fetchTickets = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const params = new URLSearchParams({ page: String(page), per_page: String(perPage) });
            if (statusFilter !== 'all') params.set('status', statusFilter);
            if (search.trim()) params.set('search', search.trim());
            const res = await fetch(`/api/admin/support/tickets?${params}`);
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load tickets');
            setTickets(data.tickets || []);
            setTotal(data.total || 0);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load tickets');
        } finally {
            setLoading(false);
        }
    }, [statusFilter, search, page]);

    useEffect(() => { fetchTickets(); }, [fetchTickets]);

    async function openTicket(ticket: Ticket) {
        setSelected(ticket);
        setDetailLoading(true);
        setActionError('');
        setReply('');
        try {
            const res = await fetch(`/api/admin/support/tickets?id=${ticket.id}`);
            // We'll re-fetch through the list endpoint with id; WP returns single ticket detail
            // Actually fetch individual ticket detail
            const res2 = await fetch(`/api/admin/support/tickets/${ticket.id}`);
            if (res2.ok) {
                const d = await res2.json();
                setSelected(d.ticket || ticket);
            }
        } catch {
            // keep existing data
        } finally {
            setDetailLoading(false);
        }
    }

    async function updateStatus(newStatus: string) {
        if (!selected) return;
        setUpdatingStatus(true);
        setActionError('');
        try {
            const res = await fetch(`/api/admin/support/tickets/${selected.id}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newStatus }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to update status');
            setSelected(t => t ? { ...t, status: newStatus as Ticket['status'] } : t);
            fetchTickets();
        } catch (e) {
            setActionError(e instanceof Error ? e.message : 'Failed to update status');
        } finally {
            setUpdatingStatus(false);
        }
    }

    async function sendReply(e: React.FormEvent) {
        e.preventDefault();
        if (!selected || !reply.trim()) return;
        setSendingReply(true);
        setActionError('');
        try {
            const res = await fetch(`/api/admin/support/tickets/${selected.id}/reply`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ message: reply }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to send reply');
            const newMsg: TicketMessage = {
                id: data.id || Date.now(),
                author: 'BPU Support',
                author_type: 'staff',
                content: reply,
                created_at: new Date().toISOString(),
            };
            setSelected(t => t ? { ...t, messages: [...(t.messages || []), newMsg], status: t.status === 'open' ? 'in_progress' : t.status } : t);
            setReply('');
            fetchTickets();
        } catch (e) {
            setActionError(e instanceof Error ? e.message : 'Failed to send reply');
        } finally {
            setSendingReply(false);
        }
    }

    const pages = Math.ceil(total / perPage);

    return (
        <div className="flex gap-6" style={{ alignItems: 'flex-start' }}>
            {/* Left: list */}
            <div className="flex-1 min-w-0">
                {/* Filters */}
                <div className="flex flex-wrap gap-3 mb-5 items-center">
                    <div className="flex gap-1 flex-wrap">
                        {STATUS_TABS.map(t => (
                            <button
                                key={t.value}
                                onClick={() => { setStatusFilter(t.value); setPage(1); }}
                                className={`px-3 py-1.5 rounded-lg text-xs font-bold border transition-colors ${statusFilter === t.value ? 'bg-brand text-white border-brand' : 'bg-white text-text-2 border-border hover:border-text-2'}`}
                            >
                                {t.label}
                            </button>
                        ))}
                    </div>
                    <form className="flex gap-2 ml-auto" onSubmit={e => { e.preventDefault(); setSearch(searchInput); setPage(1); }}>
                        <input
                            className="input-sm"
                            placeholder="Search subject, member…"
                            value={searchInput}
                            onChange={e => setSearchInput(e.target.value)}
                            style={{ minWidth: 220 }}
                        />
                        <button type="submit" className="btn btn-sm btn-primary">Search</button>
                    </form>
                </div>

                {error && <p className="text-sm text-red-500 mb-4">{error}</p>}

                {loading ? (
                    <div className="text-text-3 text-sm py-12 text-center">Loading tickets…</div>
                ) : tickets.length === 0 ? (
                    <div className="card card-p text-center text-text-3 text-sm py-12">No tickets found.</div>
                ) : (
                    <>
                        <div className="flex flex-col gap-2">
                            {tickets.map(ticket => (
                                <button
                                    key={ticket.id}
                                    onClick={() => openTicket(ticket)}
                                    className="card card-p text-left w-full hover:shadow-md transition-shadow"
                                    style={{ borderColor: selected?.id === ticket.id ? 'var(--brand)' : undefined }}
                                >
                                    <div className="flex items-start justify-between gap-3 flex-wrap">
                                        <div className="min-w-0 flex-1">
                                            <div className="flex items-center gap-2 flex-wrap mb-1">
                                                {ticket.ref && <span className="text-xs text-text-3 font-mono">#{ticket.ref}</span>}
                                                {ticket.urgency === 'high' && (
                                                    <span className="badge badge-red text-[10px]">High</span>
                                                )}
                                                <span className={`badge ${STATUS_BADGE[ticket.status] || 'badge-gray'} text-[10px]`}>
                                                    {ticket.status.replace('_', ' ')}
                                                </span>
                                            </div>
                                            <p className="font-semibold text-sm truncate">{ticket.subject}</p>
                                            <p className="text-xs text-text-3 mt-0.5">
                                                {ticket.member?.display_name || '—'} · {ticket.category}
                                            </p>
                                        </div>
                                        <div className="text-right flex-shrink-0">
                                            <p className="text-xs text-text-3">{fmtShort(ticket.updated_at)}</p>
                                            <p className="text-xs text-text-3">{ticket.message_count} msg{ticket.message_count !== 1 ? 's' : ''}</p>
                                        </div>
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
                <div className="card" style={{ width: 420, flexShrink: 0, position: 'sticky', top: 24, maxHeight: 'calc(100vh - 120px)', display: 'flex', flexDirection: 'column' }}>
                    <div className="card-p" style={{ borderBottom: '1px solid var(--border)' }}>
                        <div className="flex items-start justify-between gap-2">
                            <div className="min-w-0">
                                <p className="font-bold text-sm truncate">{selected.subject}</p>
                                {selected.ref && <p className="text-xs text-text-3 font-mono">#{selected.ref}</p>}
                            </div>
                            <button onClick={() => setSelected(null)} className="text-text-3 hover:text-text flex-shrink-0 text-lg leading-none">×</button>
                        </div>

                        <div className="flex flex-wrap gap-2 mt-2">
                            <span className="badge badge-gray text-[10px]">{selected.category}</span>
                            {selected.urgency === 'high' && <span className="badge badge-red text-[10px]">High Urgency</span>}
                            <span className={`badge ${STATUS_BADGE[selected.status] || 'badge-gray'} text-[10px]`}>{selected.status.replace('_', ' ')}</span>
                        </div>

                        {selected.member && (
                            <div className="mt-2 text-xs text-text-2">
                                <span className="font-semibold">{selected.member.display_name}</span> · {selected.member.email}
                            </div>
                        )}

                        <div className="mt-3">
                            <label className="text-xs text-text-3 font-semibold block mb-1">Change Status</label>
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

                    {/* Thread */}
                    <div className="flex-1 overflow-y-auto p-4 flex flex-col gap-3" style={{ minHeight: 0 }}>
                        {detailLoading && <p className="text-xs text-text-3 text-center py-4">Loading thread…</p>}
                        {!detailLoading && (!selected.messages || selected.messages.length === 0) && (
                            <p className="text-xs text-text-3 text-center py-4">No messages yet.</p>
                        )}
                        {(selected.messages || []).map(msg => (
                            <div key={msg.id} className={`flex gap-2 ${msg.author_type === 'staff' ? '' : 'flex-row-reverse'}`}>
                                <div
                                    style={{
                                        width: 28, height: 28, borderRadius: '50%', flexShrink: 0,
                                        background: msg.author_type === 'staff' ? '#111317' : 'var(--brand)',
                                        display: 'flex', alignItems: 'center', justifyContent: 'center',
                                        color: '#fff', fontSize: 10, fontWeight: 900,
                                    }}
                                >
                                    {msg.author_type === 'staff' ? 'BPU' : msg.author.charAt(0).toUpperCase()}
                                </div>
                                <div style={{ maxWidth: '78%' }}>
                                    <div className={`flex items-baseline gap-1.5 mb-0.5 ${msg.author_type === 'staff' ? '' : 'flex-row-reverse'}`}>
                                        <span className="text-[10px] font-bold">{msg.author_type === 'staff' ? 'BPU Support' : msg.author}</span>
                                        <span className="text-[10px] text-text-3">{fmt(msg.created_at)}</span>
                                    </div>
                                    <div
                                        className="text-xs p-2.5 rounded-lg"
                                        style={{
                                            background: msg.author_type === 'staff' ? '#fff' : '#111317',
                                            color: msg.author_type === 'staff' ? 'var(--text)' : '#fff',
                                            border: '1px solid var(--border)',
                                            whiteSpace: 'pre-wrap',
                                        }}
                                    >
                                        {msg.content}
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Reply form */}
                    {selected.status !== 'resolved' && selected.status !== 'closed' ? (
                        <form onSubmit={sendReply} className="card-p" style={{ borderTop: '1px solid var(--border)' }}>
                            {actionError && <p className="text-xs text-red-500 mb-2">{actionError}</p>}
                            <textarea
                                className="input-sm w-full mb-2"
                                rows={3}
                                placeholder="Write a reply to the member…"
                                value={reply}
                                onChange={e => setReply(e.target.value)}
                                style={{ resize: 'vertical', fontSize: 12 }}
                            />
                            <button
                                type="submit"
                                className="btn btn-sm btn-primary w-full"
                                disabled={sendingReply || !reply.trim()}
                            >
                                {sendingReply ? 'Sending…' : 'Send Reply'}
                            </button>
                        </form>
                    ) : (
                        <div className="card-p text-xs text-text-3 text-center" style={{ borderTop: '1px solid var(--border)' }}>
                            Ticket is {selected.status}.
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}
