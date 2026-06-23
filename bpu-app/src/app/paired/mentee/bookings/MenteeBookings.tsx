'use client';

import { useState, useMemo, useRef, useCallback } from 'react';
import { decodeHtml } from '@/lib/utils';

interface Booking {
    id: number;
    date: string;
    time_slot: string;
    notes: string;
    status: 'pending' | 'confirmed' | 'completed' | 'cancelled';
    created_at: string;
    role: 'mentee' | 'mentor';
    mentor?: { id: number; display_name: string; avatar_url: string };
    meet_link?: string;
    payment_status?: string;
    payment_amount?: number;
    is_group_session?: boolean;
}

type TabFilter = 'upcoming' | 'past' | 'cancelled';

const TABS: { key: TabFilter; label: string }[] = [
    { key: 'upcoming', label: 'Upcoming' },
    { key: 'past', label: 'Past' },
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
            weekday: 'short', day: 'numeric', month: 'short', year: 'numeric',
        });
    } catch {
        return dateStr;
    }
}

function mentorColor(id: number): string {
    const colors = ['#6366f1', '#8b5cf6', '#ec4899', '#3b82f6', '#14b8a6', '#f59e0b'];
    return colors[id % colors.length];
}

export default function MenteeBookings({ initial }: { initial: Booking[] }) {
    const menteeBookings = initial.filter(b => b.role === 'mentee');
    const [bookings, setBookings] = useState<Booking[]>(menteeBookings);
    const [activeTab, setActiveTab] = useState<TabFilter>('upcoming');
    const [cancellingId, setCancellingId] = useState<number | null>(null);
    const [confirmCancelId, setConfirmCancelId] = useState<number | null>(null);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const flashTimer = useRef<ReturnType<typeof setTimeout>>(undefined);

    function localToday(): string {
        const d = new Date();
        return `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    }

    const today = localToday();

    const filtered = useMemo(() => {
        let list: Booking[];
        switch (activeTab) {
            case 'upcoming':
                list = bookings.filter(b => b.date >= today && b.status !== 'cancelled');
                break;
            case 'past':
                list = bookings.filter(b => b.date < today && b.status !== 'cancelled');
                break;
            case 'cancelled':
                list = bookings.filter(b => b.status === 'cancelled');
                break;
        }
        return [...list].sort((a, b) =>
            activeTab === 'upcoming'
                ? new Date(a.date).getTime() - new Date(b.date).getTime()
                : new Date(b.date).getTime() - new Date(a.date).getTime()
        );
    }, [bookings, activeTab, today]);

    const counts = useMemo(() => ({
        upcoming: bookings.filter(b => b.date >= today && b.status !== 'cancelled').length,
        past: bookings.filter(b => b.date < today && b.status !== 'cancelled').length,
        cancelled: bookings.filter(b => b.status === 'cancelled').length,
    }), [bookings, today]);

    const flash = useCallback((msg: string, type: 'success' | 'error') => {
        if (type === 'success') { setSuccess(msg); setError(''); }
        else { setError(msg); setSuccess(''); }
        clearTimeout(flashTimer.current);
        flashTimer.current = setTimeout(() => { setSuccess(''); setError(''); }, 4000);
    }, []);

    async function cancelBooking(id: number) {
        setCancellingId(id);
        try {
            const res = await fetch(`/api/paired/bookings/${id}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: 'cancelled' }),
            });
            const result = await res.json().catch(() => ({}));
            if (!res.ok) {
                flash(result.error || 'Failed to cancel booking.', 'error');
                return;
            }
            setBookings(prev => prev.map(b => b.id === id ? { ...b, status: 'cancelled' as const } : b));
            setConfirmCancelId(null);
            flash('Booking cancelled.', 'success');
        } catch {
            flash('Something went wrong. Please try again.', 'error');
        } finally {
            setCancellingId(null);
        }
    }

    return (
        <div>
            {error && <div className="alert alert-red mb-6">{error}</div>}
            {success && <div className="alert alert-green mb-6">{success}</div>}

            {/* Tabs */}
            <div className="flex gap-2 mb-6 flex-wrap">
                {TABS.map(tab => (
                    <button
                        key={tab.key}
                        className={`badge cursor-pointer ${activeTab === tab.key ? 'badge-purple' : ''}`}
                        onClick={() => setActiveTab(tab.key)}
                        style={{ border: 'none', background: activeTab === tab.key ? undefined : 'var(--surface)' }}
                    >
                        {tab.label}
                        {counts[tab.key] > 0 && (
                            <span className="ml-1 text-text-3">({counts[tab.key]})</span>
                        )}
                    </button>
                ))}
            </div>

            {/* List */}
            {filtered.length === 0 ? (
                <div className="card card-p text-center py-16">
                    <p className="text-text-3 text-sm">
                        {activeTab === 'upcoming'
                            ? 'No upcoming sessions.'
                            : activeTab === 'past'
                            ? 'No past sessions yet.'
                            : 'No cancelled sessions.'}
                    </p>
                    {activeTab === 'upcoming' && (
                        <a href="/paired/mentors" className="btn btn-purple btn-sm mt-4 inline-block">
                            Browse mentors
                        </a>
                    )}
                </div>
            ) : (
                <div className="grid gap-4">
                    {filtered.map(booking => {
                        const mentor = booking.mentor;
                        const mentorName = mentor?.display_name || 'Mentor';
                        const color = mentorColor(mentor?.id || booking.id);
                        const isUpcoming = booking.date >= today;

                        return (
                            <div key={booking.id} className="card card-p">
                                <div className="flex items-start gap-4">
                                    {mentor?.avatar_url ? (
                                        <img
                                            src={mentor.avatar_url}
                                            alt=""
                                            width={40} height={40}
                                            style={{ width: 40, height: 40, borderRadius: '50%', objectFit: 'cover', flexShrink: 0 }}
                                        />
                                    ) : (
                                        <div className="avatar avatar-md" style={{ background: color, flexShrink: 0 }}>
                                            {mentorName[0]}
                                        </div>
                                    )}

                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center gap-2 flex-wrap mb-1">
                                            {mentor?.id ? (
                                                <a
                                                    href={`/paired/mentors/${mentor.id}`}
                                                    className="font-semibold text-sm hover:underline"
                                                >
                                                    {decodeHtml(mentorName)}
                                                </a>
                                            ) : (
                                                <span className="font-semibold text-sm">
                                                    {decodeHtml(mentorName)}
                                                </span>
                                            )}
                                            <span className={STATUS_BADGE[booking.status] || 'badge'}>
                                                {booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                                            </span>
                                            {booking.is_group_session && (
                                                <span className="badge text-xs">Group</span>
                                            )}
                                        </div>

                                        <p className="text-sm mt-1">
                                            <strong>{formatDate(booking.date)}</strong>
                                            {booking.time_slot && (
                                                <span className="text-text-2"> at {booking.time_slot.replace('-', ' – ')}</span>
                                            )}
                                        </p>

                                        {booking.notes && (
                                            <p className="text-sm text-text-2 mt-2">{booking.notes}</p>
                                        )}

                                        {booking.payment_amount != null && booking.payment_amount > 0 && (
                                            <p className="text-xs text-text-3 mt-1">
                                                £{booking.payment_amount.toFixed(2)}
                                                {booking.payment_status && (
                                                    <span className="ml-1">· {booking.payment_status}</span>
                                                )}
                                            </p>
                                        )}

                                        {/* Actions */}
                                        <div className="flex flex-wrap gap-2 mt-3">
                                            {booking.meet_link && booking.status === 'confirmed' && isUpcoming && (
                                                <a
                                                    href={booking.meet_link}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className="btn btn-purple btn-sm text-xs"
                                                >
                                                    Join meeting
                                                </a>
                                            )}

                                            {booking.status === 'completed' && mentor?.id && (
                                                <a
                                                    href={`/paired/mentors/${mentor.id}/review`}
                                                    className="btn btn-outline btn-sm text-xs"
                                                >
                                                    Leave a review
                                                </a>
                                            )}

                                            {(booking.status === 'pending' || booking.status === 'confirmed') && isUpcoming && (
                                                confirmCancelId === booking.id ? (
                                                    <div className="flex items-center gap-2">
                                                        <span className="text-xs text-text-2">Cancel this booking?</span>
                                                        <button
                                                            className="btn btn-sm text-xs"
                                                            style={{ backgroundColor: 'var(--err)', color: '#fff' }}
                                                            disabled={cancellingId === booking.id}
                                                            onClick={() => cancelBooking(booking.id)}
                                                        >
                                                            {cancellingId === booking.id ? 'Cancelling...' : 'Yes, cancel'}
                                                        </button>
                                                        <button
                                                            className="btn btn-ghost btn-sm text-xs"
                                                            onClick={() => setConfirmCancelId(null)}
                                                        >
                                                            No
                                                        </button>
                                                    </div>
                                                ) : (
                                                    <button
                                                        className="btn btn-outline btn-sm text-xs"
                                                        onClick={() => setConfirmCancelId(booking.id)}
                                                    >
                                                        Cancel booking
                                                    </button>
                                                )
                                            )}

                                            {mentor?.id && (
                                                <a
                                                    href={`/paired/mentors/${mentor.id}`}
                                                    className="btn btn-ghost btn-sm text-xs"
                                                >
                                                    View mentor
                                                </a>
                                            )}
                                            <a
                                                href={`/paired/bookings/${booking.id}`}
                                                className="btn btn-ghost btn-sm text-xs"
                                            >
                                                Details
                                            </a>
                                        </div>
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
