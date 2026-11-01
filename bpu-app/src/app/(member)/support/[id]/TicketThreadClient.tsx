'use client';

import { useState } from 'react';
import type { BPUUser } from '@/lib/auth';
import type { SupportTicketDetail, TicketStatus, TicketMessage } from '../support-types';

const STATUS_CONFIG: Record<TicketStatus, { label: string; bg: string; color: string }> = {
    open:        { label: 'Open',        bg: '#eff6ff', color: '#1d4ed8' },
    in_progress: { label: 'In Progress', bg: '#fff7ed', color: '#c2410c' },
    resolved:    { label: 'Resolved',    bg: '#f0fdf4', color: '#16a34a' },
    closed:      { label: 'Closed',      bg: 'var(--bg)', color: 'var(--text-3)' },
};

function StatusBadge({ status }: { status: TicketStatus }) {
    const cfg = STATUS_CONFIG[status] ?? STATUS_CONFIG.open;
    return (
        <span
            className="text-[10px] font-black uppercase tracking-wider px-2.5 py-1 rounded"
            style={{ background: cfg.bg, color: cfg.color, border: `1px solid ${cfg.color}40` }}
        >
            {cfg.label}
        </span>
    );
}

function formatDateTime(iso: string) {
    return new Date(iso).toLocaleString('en-GB', {
        day: 'numeric', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit',
    });
}

function MessageBubble({ message, isStaff }: { message: TicketMessage; isStaff: boolean }) {
    return (
        <div className={`flex gap-3 ${isStaff ? '' : 'flex-row-reverse'}`}>
            {/* Avatar */}
            <div
                style={{
                    width: 36, height: 36, borderRadius: '50%', flexShrink: 0,
                    background: isStaff ? '#111317' : 'var(--brand)',
                    border: '2px solid #111317',
                    display: 'flex', alignItems: 'center', justifyContent: 'center',
                    color: '#fff', fontSize: 12, fontWeight: 900,
                }}
            >
                {isStaff ? 'BPU' : message.author.charAt(0).toUpperCase()}
            </div>

            {/* Bubble */}
            <div style={{ maxWidth: '75%', minWidth: 0 }}>
                <div className={`flex items-baseline gap-2 mb-1 ${isStaff ? '' : 'flex-row-reverse'}`}>
                    <span className="text-xs font-black uppercase tracking-tight" style={{ color: '#111317' }}>
                        {isStaff ? 'BPU Support' : message.author}
                    </span>
                    <span className="text-[10px]" style={{ color: 'var(--text-3)' }}>
                        {formatDateTime(message.created_at)}
                    </span>
                </div>
                <div
                    className="text-sm font-medium leading-relaxed p-4 rounded-xl"
                    style={{
                        background: isStaff ? '#fff' : '#111317',
                        color: isStaff ? 'var(--text)' : '#fff',
                        border: '2px solid #111317',
                        boxShadow: isStaff ? '3px 3px 0px 0px #001478' : 'none',
                        whiteSpace: 'pre-wrap',
                    }}
                >
                    {message.content}
                </div>
            </div>
        </div>
    );
}

interface Props {
    ticket: SupportTicketDetail;
    user: BPUUser;
}

export default function TicketThreadClient({ ticket: initial, user }: Props) {
    const [ticket, setTicket] = useState(initial);
    const [reply, setReply] = useState('');
    const [attachment, setAttachment] = useState<File | null>(null);
    const [sending, setSending] = useState(false);
    const [error, setError] = useState('');

    const isClosed = ticket.status === 'resolved' || ticket.status === 'closed';

    async function sendReply(e: React.FormEvent) {
        e.preventDefault();
        if (!reply.trim()) return;
        setError('');
        setSending(true);
        try {
            const body = new FormData();
            body.append('message', reply);
            if (attachment) body.append('attachment', attachment);
            const res = await fetch(`/api/member/support/tickets/${ticket.id}/reply`, {
                method: 'POST',
                body,
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to send reply');
            // Optimistically append the new message
            const newMsg: TicketMessage = {
                id: data.id || Date.now(),
                author: user.display_name,
                author_type: 'member',
                content: reply,
                created_at: new Date().toISOString(),
            };
            setTicket(t => ({ ...t, messages: [...t.messages, newMsg] }));
            setReply('');
            setAttachment(null);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Something went wrong.');
        } finally {
            setSending(false);
        }
    }

    return (
        <div className="dash-page-wrap">
            {/* Breadcrumb */}
            <div className="flex items-center gap-2 mb-6 text-xs font-bold" style={{ color: 'var(--text-3)' }}>
                <a href="/support" style={{ color: 'var(--brand)' }}>Support</a>
                <span>/</span>
                <span className="truncate" style={{ color: 'var(--text-2)' }}>{ticket.subject}</span>
            </div>

            {/* Ticket header */}
            <div
                className="rounded-xl p-5 mb-6"
                style={{ background: '#fff', border: '2px solid #111317', boxShadow: '4px 4px 0px 0px #001478' }}
            >
                <div className="flex items-start justify-between gap-4 flex-wrap">
                    <div className="min-w-0">
                        <h1 className="font-black uppercase tracking-tight text-xl sm:text-2xl" style={{ color: '#111317' }}>
                            {ticket.subject}
                        </h1>
                        <div className="flex flex-wrap items-center gap-3 mt-2">
                            {ticket.ref && (
                                <span className="text-xs font-bold" style={{ color: 'var(--text-3)' }}>#{ticket.ref}</span>
                            )}
                            <span
                                className="text-[10px] font-bold uppercase px-2 py-0.5 rounded"
                                style={{ background: 'var(--bg)', color: 'var(--text-2)', border: '1px solid var(--border)' }}
                            >
                                {ticket.category}
                            </span>
                            {ticket.urgency === 'high' && (
                                <span
                                    className="text-[10px] font-black uppercase px-2 py-0.5 rounded"
                                    style={{ background: '#fee2e2', color: '#dc2626', border: '1px solid #dc262640' }}
                                >
                                    High Urgency
                                </span>
                            )}
                        </div>
                    </div>
                    <div className="flex items-center gap-3 flex-shrink-0">
                        <StatusBadge status={ticket.status} />
                        <span className="text-xs" style={{ color: 'var(--text-3)' }}>
                            Opened {formatDateTime(ticket.created_at)}
                        </span>
                    </div>
                </div>
            </div>

            {/* Message thread */}
            <div
                className="rounded-xl overflow-hidden mb-4"
                style={{ border: '2px solid #111317' }}
            >
                <div
                    className="px-4 py-3 text-xs font-black uppercase tracking-widest"
                    style={{ background: '#111317', color: 'rgba(255,255,255,0.6)' }}
                >
                    Thread · {ticket.messages.length} message{ticket.messages.length !== 1 ? 's' : ''}
                </div>
                <div
                    className="flex flex-col gap-5 p-5"
                    style={{ background: 'var(--bg)', minHeight: 120 }}
                >
                    {ticket.messages.length === 0 ? (
                        <p className="text-sm text-center py-8" style={{ color: 'var(--text-3)' }}>
                            No messages yet. Our team will respond within 48 hours.
                        </p>
                    ) : (
                        ticket.messages.map(msg => (
                            <MessageBubble
                                key={msg.id}
                                message={msg}
                                isStaff={msg.author_type === 'staff'}
                            />
                        ))
                    )}
                </div>
            </div>

            {/* Reply form */}
            {isClosed ? (
                <div
                    className="rounded-xl p-5 text-center"
                    style={{ background: '#fff', border: '2px solid #111317' }}
                >
                    <p className="text-sm font-bold" style={{ color: 'var(--text-2)' }}>
                        This ticket is {ticket.status}.{' '}
                        <a href="/support/new" style={{ color: 'var(--brand)' }}>Raise a new ticket</a> if you need further help.
                    </p>
                </div>
            ) : (
                <form
                    onSubmit={sendReply}
                    className="rounded-xl overflow-hidden"
                    style={{ background: '#fff', border: '2px solid #111317', boxShadow: '4px 4px 0px 0px #001478' }}
                >
                    <div
                        className="px-4 py-3 text-xs font-black uppercase tracking-widest"
                        style={{ background: '#111317', color: 'rgba(255,255,255,0.6)' }}
                    >
                        Add a Reply
                    </div>
                    <div className="p-5 flex flex-col gap-4">
                        <textarea
                            className="field-input"
                            rows={4}
                            placeholder="Type your reply here…"
                            value={reply}
                            onChange={e => setReply(e.target.value)}
                            style={{ resize: 'vertical' }}
                        />
                        <div>
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                Attachment (optional)
                            </label>
                            <input
                                type="file"
                                accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                                className="field-input"
                                style={{ padding: '8px 12px' }}
                                onChange={e => setAttachment(e.target.files?.[0] ?? null)}
                            />
                        </div>
                        {error && (
                            <p className="text-sm font-bold" style={{ color: 'var(--brand)' }}>{error}</p>
                        )}
                        <div className="flex items-center gap-3">
                            <button
                                type="submit"
                                className="btn btn-amber"
                                style={{ border: '2px solid #111317', boxShadow: '3px 3px 0px 0px #001478' }}
                                disabled={sending || !reply.trim()}
                            >
                                {sending ? 'Sending…' : 'Send Reply'}
                            </button>
                            <a href="/support" className="btn btn-ghost">← Back to Tickets</a>
                        </div>
                    </div>
                </form>
            )}
        </div>
    );
}
