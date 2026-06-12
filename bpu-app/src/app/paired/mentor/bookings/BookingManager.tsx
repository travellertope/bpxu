'use client';

import { useState, useMemo } from 'react';

interface Booking {
    id: number;
    mentor_id: number;
    mentee_id: number;
    mentee_name: string;
    mentee_email: string;
    mentee_avatar: string;
    date: string;
    time_slot: string;
    notes: string;
    status: 'pending' | 'confirmed' | 'completed' | 'cancelled';
    created_at: string;
}

type TabFilter = 'all' | 'pending' | 'confirmed' | 'completed' | 'cancelled';

const TABS: { key: TabFilter; label: string }[] = [
    { key: 'all', label: 'All' },
    { key: 'pending', label: 'Pending' },
    { key: 'confirmed', label: 'Confirmed' },
    { key: 'completed', label: 'Completed' },
    { key: 'cancelled', label: 'Cancelled' },
];

const STATUS_BADGE: Record<string, string> = {
    pending: 'badge badge-amber',
    confirmed: 'badge badge-purple',
    completed: 'badge badge-green',
    cancelled: 'badge',
};

function formatDate(dateStr: string): string {
    try {
        return new Date(dateStr).toLocaleDateString('en-GB', {
            weekday: 'short',
            day: 'numeric',
            month: 'short',
            year: 'numeric',
        });
    } catch {
        return dateStr;
    }
}

function formatTimestamp(dateStr: string): string {
    try {
        return new Date(dateStr).toLocaleDateString('en-GB', {
            day: 'numeric',
            month: 'short',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    } catch {
        return dateStr;
    }
}

function isDatePassed(dateStr: string): boolean {
    try {
        const d = new Date(dateStr);
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        return d < today;
    } catch {
        return false;
    }
}

export default function BookingManager({ initial }: { initial: Booking[] }) {
    const [bookings, setBookings] = useState<Booking[]>(initial);
    const [activeTab, setActiveTab] = useState<TabFilter>('all');
    const [loadingAction, setLoadingAction] = useState<string | null>(null);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [expandedNotes, setExpandedNotes] = useState<Set<number>>(new Set());
    const [confirmDecline, setConfirmDecline] = useState<number | null>(null);

    function flash(msg: string, type: 'success' | 'error') {
        if (type === 'success') {
            setSuccess(msg);
            setError('');
        } else {
            setError(msg);
            setSuccess('');
        }
        setTimeout(() => {
            setSuccess('');
            setError('');
        }, 4000);
    }

    async function updateStatus(id: number, status: 'confirmed' | 'cancelled' | 'completed') {
        const actionKey = `${id}-${status}`;
        setLoadingAction(actionKey);
        try {
            const res = await fetch(`/api/paired/bookings/${id}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status }),
            });
            const result = await res.json().catch(() => ({}));
            if (!res.ok) {
                flash(result.error || 'Failed to update booking.', 'error');
                return;
            }
            setBookings(prev =>
                prev.map(b => (b.id === id ? { ...b, status } : b))
            );
            setConfirmDecline(null);
            const labels: Record<string, string> = {
                confirmed: 'Booking accepted.',
                cancelled: 'Booking declined.',
                completed: 'Booking marked as complete.',
            };
            flash(labels[status], 'success');
        } catch {
            flash('Something went wrong. Please try again.', 'error');
        } finally {
            setLoadingAction(null);
        }
    }

    const filtered = useMemo(() => {
        const list = activeTab === 'all'
            ? bookings
            : bookings.filter(b => b.status === activeTab);
        return [...list].sort(
            (a, b) => new Date(b.date).getTime() - new Date(a.date).getTime()
        );
    }, [bookings, activeTab]);

    const counts = useMemo(() => {
        const c: Record<string, number> = { all: bookings.length };
        for (const b of bookings) {
            c[b.status] = (c[b.status] || 0) + 1;
        }
        return c;
    }, [bookings]);

    return (
        <div>
            {error && <div className="alert alert-red mb-6">{error}</div>}
            {success && <div className="alert alert-green mb-6">{success}</div>}

            {/* Tabs */}
            <div className="flex gap-2 mb-6 flex-wrap">
                {TABS.map(tab => (
                    <button
                        key={tab.key}
                        className={`badge cursor-pointer ${
                            activeTab === tab.key ? 'badge-purple' : ''
                        }`}
                        onClick={() => setActiveTab(tab.key)}
                        style={{ border: 'none', background: activeTab === tab.key ? undefined : 'var(--surface)' }}
                    >
                        {tab.label}
                        {(counts[tab.key] ?? 0) > 0 && (
                            <span className="ml-1 text-text-3">({counts[tab.key]})</span>
                        )}
                    </button>
                ))}
            </div>

            {/* Bookings list */}
            {filtered.length === 0 ? (
                <div className="card card-p text-center py-16">
                    <p className="text-text-3 text-sm">
                        {activeTab === 'all'
                            ? 'No bookings yet. Once mentees book sessions with you, they will appear here.'
                            : `No ${activeTab} bookings.`}
                    </p>
                </div>
            ) : (
                <div className="grid gap-4">
                    {filtered.map(booking => {
                        const notesExpanded = expandedNotes.has(booking.id);
                        const hasLongNotes = booking.notes && booking.notes.length > 120;

                        return (
                            <div key={booking.id} className="card card-p card-lift">
                                <div className="flex items-start gap-4">
                                    {/* Avatar */}
                                    {booking.mentee_avatar ? (
                                        <img
                                            src={booking.mentee_avatar}
                                            alt=""
                                            width={32}
                                            height={32}
                                            style={{
                                                width: 32,
                                                height: 32,
                                                borderRadius: '50%',
                                                objectFit: 'cover',
                                                flexShrink: 0,
                                            }}
                                        />
                                    ) : (
                                        <div
                                            style={{
                                                width: 32,
                                                height: 32,
                                                borderRadius: '50%',
                                                background: 'var(--purple-bg)',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'center',
                                                flexShrink: 0,
                                                fontSize: 14,
                                                fontWeight: 600,
                                                color: 'var(--purple)',
                                            }}
                                        >
                                            {(booking.mentee_name || '?')[0].toUpperCase()}
                                        </div>
                                    )}

                                    {/* Content */}
                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center gap-2 flex-wrap mb-1">
                                            <span className="font-semibold text-sm">
                                                {booking.mentee_name || 'Unknown'}
                                            </span>
                                            <span className={STATUS_BADGE[booking.status] || 'badge'}>
                                                {booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                                            </span>
                                        </div>
                                        <p className="text-sm text-text-2">
                                            {booking.mentee_email}
                                        </p>
                                        <p className="text-sm mt-2">
                                            <strong>{formatDate(booking.date)}</strong>
                                            {booking.time_slot && (
                                                <span className="text-text-2"> at {booking.time_slot.replace('-', ' - ')}</span>
                                            )}
                                        </p>

                                        {/* Notes */}
                                        {booking.notes && (
                                            <div className="mt-2">
                                                <p className="text-sm text-text-2">
                                                    {notesExpanded || !hasLongNotes
                                                        ? booking.notes
                                                        : booking.notes.slice(0, 120) + '...'}
                                                </p>
                                                {hasLongNotes && (
                                                    <button
                                                        className="text-xs mt-1"
                                                        style={{ color: 'var(--purple)', background: 'none', border: 'none', cursor: 'pointer', padding: 0 }}
                                                        onClick={() =>
                                                            setExpandedNotes(prev => {
                                                                const next = new Set(prev);
                                                                if (next.has(booking.id)) next.delete(booking.id);
                                                                else next.add(booking.id);
                                                                return next;
                                                            })
                                                        }
                                                    >
                                                        {notesExpanded ? 'Show less' : 'Read more'}
                                                    </button>
                                                )}
                                            </div>
                                        )}

                                        <p className="text-xs text-text-3 mt-2">
                                            Booked {formatTimestamp(booking.created_at)}
                                        </p>

                                        {/* Actions */}
                                        {booking.status === 'pending' && (
                                            <div className="flex gap-2 mt-3">
                                                {confirmDecline === booking.id ? (
                                                    <>
                                                        <span className="text-sm text-text-2 self-center mr-1">
                                                            Decline this booking?
                                                        </span>
                                                        <button
                                                            className="btn btn-sm"
                                                            style={{ backgroundColor: 'var(--err)', color: '#fff' }}
                                                            disabled={loadingAction === `${booking.id}-cancelled`}
                                                            onClick={() => updateStatus(booking.id, 'cancelled')}
                                                        >
                                                            {loadingAction === `${booking.id}-cancelled` ? 'Declining...' : 'Yes, decline'}
                                                        </button>
                                                        <button
                                                            className="btn btn-ghost btn-sm"
                                                            onClick={() => setConfirmDecline(null)}
                                                        >
                                                            No
                                                        </button>
                                                    </>
                                                ) : (
                                                    <>
                                                        <button
                                                            className="btn btn-purple btn-sm"
                                                            disabled={loadingAction === `${booking.id}-confirmed`}
                                                            onClick={() => updateStatus(booking.id, 'confirmed')}
                                                        >
                                                            {loadingAction === `${booking.id}-confirmed` ? 'Accepting...' : 'Accept'}
                                                        </button>
                                                        <button
                                                            className="btn btn-outline btn-sm"
                                                            onClick={() => setConfirmDecline(booking.id)}
                                                        >
                                                            Decline
                                                        </button>
                                                    </>
                                                )}
                                            </div>
                                        )}

                                        {booking.status === 'confirmed' && (
                                            <div className="flex gap-2 mt-3">
                                                {isDatePassed(booking.date) && (
                                                    <button
                                                        className="btn btn-purple btn-sm"
                                                        disabled={loadingAction === `${booking.id}-completed`}
                                                        onClick={() => updateStatus(booking.id, 'completed')}
                                                    >
                                                        {loadingAction === `${booking.id}-completed` ? 'Completing...' : 'Mark Complete'}
                                                    </button>
                                                )}
                                                <button
                                                    className="btn btn-outline btn-sm"
                                                    disabled={loadingAction === `${booking.id}-cancelled`}
                                                    onClick={() => updateStatus(booking.id, 'cancelled')}
                                                >
                                                    {loadingAction === `${booking.id}-cancelled` ? 'Cancelling...' : 'Cancel'}
                                                </button>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
}
