'use client';

import Link from 'next/link';
import type { SupportTicket, TicketStatus } from './support-types';

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
            className="text-[10px] font-black uppercase tracking-wider px-2 py-0.5 rounded"
            style={{ background: cfg.bg, color: cfg.color, border: `1px solid ${cfg.color}30` }}
        >
            {cfg.label}
        </span>
    );
}

function formatDate(iso: string) {
    return new Date(iso).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

export default function SupportListClient({ tickets }: { tickets: SupportTicket[] }) {
    const open = tickets.filter(t => t.status === 'open' || t.status === 'in_progress');
    const closed = tickets.filter(t => t.status === 'resolved' || t.status === 'closed');

    return (
        <div className="dash-page-wrap">
            {/* Header */}
            <div className="flex items-start justify-between mb-8 flex-wrap gap-4">
                <div>
                    <p className="text-xs font-black uppercase tracking-widest mb-1" style={{ color: 'var(--brand)' }}>
                        BPU Support Team
                    </p>
                    <h1 className="font-black uppercase tracking-tight text-2xl sm:text-3xl" style={{ color: '#111317' }}>
                        Member Support
                    </h1>
                    <p className="text-sm font-medium mt-1" style={{ color: 'var(--text-2)' }}>
                        We respond to all tickets within 48 hours.
                    </p>
                </div>
                <Link
                    href="/support/new"
                    className="btn btn-amber"
                    style={{ border: '2px solid #111317', boxShadow: '3px 3px 0px 0px #001478', whiteSpace: 'nowrap' }}
                >
                    + New Ticket
                </Link>
            </div>

            {tickets.length === 0 ? (
                /* Empty state */
                <div
                    className="text-center py-20 rounded-2xl"
                    style={{ background: '#fff', border: '2px solid #111317', boxShadow: '4px 4px 0px 0px #001478' }}
                >
                    <div
                        style={{
                            width: 56, height: 56, borderRadius: '50%',
                            background: 'var(--bg)', border: '2px solid #111317',
                            display: 'flex', alignItems: 'center', justifyContent: 'center',
                            margin: '0 auto 16px',
                        }}
                    >
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--text-3)" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                            <path d="M3 18v-6a9 9 0 0 1 18 0v6"/>
                            <path d="M21 19a2 2 0 0 1-2 2h-1a2 2 0 0 1-2-2v-3a2 2 0 0 1 2-2h3z"/>
                            <path d="M3 19a2 2 0 0 0 2 2h1a2 2 0 0 0 2-2v-3a2 2 0 0 0-2-2H3z"/>
                        </svg>
                    </div>
                    <p className="font-black uppercase tracking-tight text-base mb-1" style={{ color: '#111317' }}>
                        No tickets yet
                    </p>
                    <p className="text-sm mb-6" style={{ color: 'var(--text-2)' }}>
                        Raise a support ticket and our team will respond within 48 hours.
                    </p>
                    <Link href="/support/new" className="btn btn-amber" style={{ border: '2px solid #111317' }}>
                        Raise a Ticket
                    </Link>
                </div>
            ) : (
                <div className="flex flex-col gap-8">
                    {open.length > 0 && (
                        <section>
                            <h2 className="text-xs font-black uppercase tracking-widest mb-3" style={{ color: 'var(--text-2)' }}>
                                Active ({open.length})
                            </h2>
                            <TicketTable tickets={open} />
                        </section>
                    )}
                    {closed.length > 0 && (
                        <section>
                            <h2 className="text-xs font-black uppercase tracking-widest mb-3" style={{ color: 'var(--text-3)' }}>
                                Resolved / Closed ({closed.length})
                            </h2>
                            <TicketTable tickets={closed} muted />
                        </section>
                    )}
                </div>
            )}
        </div>
    );
}

function TicketTable({ tickets, muted }: { tickets: SupportTicket[]; muted?: boolean }) {
    return (
        <div
            style={{
                background: '#fff',
                border: '2px solid #111317',
                borderRadius: 12,
                boxShadow: muted ? 'none' : '4px 4px 0px 0px #001478',
                overflow: 'hidden',
            }}
        >
            {/* Table header */}
            <div
                className="hidden sm:grid text-[10px] font-black uppercase tracking-widest px-5 py-3"
                style={{
                    gridTemplateColumns: '1fr 160px 90px 100px 36px',
                    borderBottom: '2px solid #111317',
                    background: '#111317',
                    color: 'rgba(255,255,255,0.6)',
                    gap: 12,
                }}
            >
                <span>Subject</span>
                <span>Category</span>
                <span>Status</span>
                <span>Last update</span>
                <span />
            </div>

            {tickets.map((t, i) => (
                <Link
                    key={t.id}
                    href={`/support/${t.id}`}
                    style={{
                        display: 'grid',
                        gridTemplateColumns: '1fr',
                        borderBottom: i < tickets.length - 1 ? '1px solid var(--border)' : 'none',
                        textDecoration: 'none',
                        cursor: 'pointer',
                        transition: 'background 0.1s',
                    }}
                    className="hover-row"
                >
                    {/* Mobile layout */}
                    <div className="sm:hidden flex flex-col gap-1.5 px-5 py-4">
                        <div className="flex items-center justify-between gap-2">
                            <span className="font-black text-sm uppercase tracking-tight" style={{ color: '#111317' }}>{t.subject}</span>
                            <StatusBadge status={t.status} />
                        </div>
                        <div className="flex items-center gap-3">
                            <span className="text-xs font-medium" style={{ color: 'var(--text-2)' }}>{t.category}</span>
                            <span className="text-xs" style={{ color: 'var(--text-3)' }}>{formatDate(t.updated_at)}</span>
                        </div>
                        {t.ref && (
                            <span className="text-[10px] font-bold" style={{ color: 'var(--text-3)' }}>#{t.ref}</span>
                        )}
                    </div>

                    {/* Desktop layout */}
                    <div
                        className="hidden sm:grid items-center gap-3 px-5 py-4"
                        style={{ gridTemplateColumns: '1fr 160px 90px 100px 36px' }}
                    >
                        <div className="min-w-0">
                            <p className="font-black text-sm uppercase tracking-tight truncate" style={{ color: '#111317' }}>{t.subject}</p>
                            {t.ref && <p className="text-[10px] font-bold mt-0.5" style={{ color: 'var(--text-3)' }}>#{t.ref}</p>}
                        </div>
                        <span className="text-xs font-medium truncate" style={{ color: 'var(--text-2)' }}>{t.category}</span>
                        <StatusBadge status={t.status} />
                        <span className="text-xs" style={{ color: 'var(--text-3)' }}>{formatDate(t.updated_at)}</span>
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--text-3)" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                            <polyline points="9 18 15 12 9 6"/>
                        </svg>
                    </div>
                </Link>
            ))}

            <style>{`.hover-row:hover { background: var(--bg); }`}</style>
        </div>
    );
}
