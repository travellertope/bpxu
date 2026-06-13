import { getBPUSession } from '@/lib/auth';
import { redirect, notFound } from 'next/navigation';
import { cookies } from 'next/headers';
import { decodeHtml } from '@/lib/utils';
import BookingDetailActions from './BookingDetailActions';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

interface BookingDetail {
    id: number;
    date: string;
    time_slot: string;
    notes: string;
    status: string;
    created_at: string;
    role: 'mentee' | 'mentor';
    mentor?: { id: number; display_name: string; avatar_url: string };
    mentee?: { id: number; display_name: string; avatar_url: string };
    meet_link?: string;
    payment_status?: string;
    payment_amount?: number;
    is_group_session?: boolean;
    session_id?: number;
}

function formatDate(dateStr: string): string {
    try {
        return new Date(dateStr).toLocaleDateString('en-GB', {
            weekday: 'long', day: 'numeric', month: 'long', year: 'numeric',
        });
    } catch { return dateStr; }
}

function formatTimestamp(dateStr: string): string {
    try {
        return new Date(dateStr).toLocaleDateString('en-GB', {
            day: 'numeric', month: 'short', year: 'numeric',
            hour: '2-digit', minute: '2-digit',
        });
    } catch { return dateStr; }
}

const STATUS_BADGE: Record<string, string> = {
    pending: 'badge badge-amber',
    confirmed: 'badge badge-purple',
    completed: 'badge badge-green',
    cancelled: 'badge',
};

function InfoRow({ label, value }: { label: string; value: React.ReactNode }) {
    return (
        <div style={{ display: 'flex', flexDirection: 'column', gap: '2px' }}>
            <span className="text-xs font-medium" style={{ color: 'var(--text-3)', textTransform: 'uppercase', letterSpacing: '0.05em' }}>{label}</span>
            <span className="text-sm">{value}</span>
        </div>
    );
}

export default async function BookingDetailPage({
    params,
}: {
    params: Promise<{ id: string }>;
}) {
    const { id } = await params;

    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect(`/login?returnTo=/paired/bookings/${id}`);
    }

    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    let booking: BookingDetail | null = null;

    if (jwt) {
        try {
            const res = await fetch(`${WP}/wp-json/bpu/v1/bookings/${id}`, {
                headers: { 'Authorization': `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
            });
            if (res.ok) {
                const data = await res.json();
                booking = data.booking || null;
            } else if (res.status === 404) {
                notFound();
            }
        } catch { /* fall through to notFound */ }
    }

    if (!booking) notFound();

    const today = new Date().toISOString().split('T')[0];
    const isUpcoming = booking.date >= today;
    const other = booking.role === 'mentee' ? booking.mentor : booking.mentee;
    const otherLabel = booking.role === 'mentee' ? 'Mentor' : 'Mentee';
    const otherName = other?.display_name || otherLabel;

    const backHref = booking.role === 'mentee' ? '/paired/mentee/bookings' : '/paired/mentor/bookings';

    return (
        <div className="wrap py-10 fade-up" style={{ maxWidth: '640px' }}>
            {/* Back */}
            <a
                href={backHref}
                className="text-sm flex items-center gap-1 mb-6"
                style={{ color: 'var(--text-3)', width: 'fit-content' }}
            >
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <polyline points="15 18 9 12 15 6" />
                </svg>
                Back to bookings
            </a>

            <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
                {/* Header */}
                <div className="flex items-start justify-between gap-4">
                    <div>
                        <h1 className="text-2xl font-bold">Booking #{booking.id}</h1>
                        <p className="text-text-2 text-sm mt-1">Created {formatTimestamp(booking.created_at)}</p>
                    </div>
                    <div className="flex flex-col items-end gap-1 shrink-0">
                        <span className={STATUS_BADGE[booking.status] || 'badge'}>
                            {booking.status.charAt(0).toUpperCase() + booking.status.slice(1)}
                        </span>
                        {booking.is_group_session && <span className="badge text-xs">Group session</span>}
                    </div>
                </div>

                {/* Session info */}
                <div className="card card-p" style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                    <h2 className="font-semibold text-sm" style={{ color: 'var(--text-3)', textTransform: 'uppercase', letterSpacing: '0.05em' }}>Session Details</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <InfoRow label="Date" value={<strong>{formatDate(booking.date)}</strong>} />
                        <InfoRow label="Time" value={booking.time_slot?.replace('-', ' – ') || '—'} />
                        {booking.role === 'mentee' ? (
                            <InfoRow label={otherLabel} value={
                                <a href={`/paired/mentors/${other?.id || ''}`} className="font-medium hover:underline" style={{ color: 'var(--purple)' }}>
                                    {decodeHtml(otherName)}
                                </a>
                            } />
                        ) : (
                            <InfoRow label={otherLabel} value={decodeHtml(otherName)} />
                        )}
                        {booking.role === 'mentee' && other?.avatar_url && (
                            <div />
                        )}
                    </div>

                    {booking.notes && (
                        <div>
                            <span className="text-xs font-medium" style={{ color: 'var(--text-3)', textTransform: 'uppercase', letterSpacing: '0.05em' }}>Notes</span>
                            <p className="text-sm mt-1 text-text-2">{booking.notes}</p>
                        </div>
                    )}
                </div>

                {/* Payment info */}
                {(booking.payment_amount != null && booking.payment_amount > 0) && (
                    <div className="card card-p" style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                        <h2 className="font-semibold text-sm" style={{ color: 'var(--text-3)', textTransform: 'uppercase', letterSpacing: '0.05em' }}>Payment</h2>
                        <div className="flex items-center justify-between">
                            <span className="text-sm text-text-2">Amount</span>
                            <span className="font-bold">£{booking.payment_amount.toFixed(2)}</span>
                        </div>
                        <div className="flex items-center justify-between">
                            <span className="text-sm text-text-2">Status</span>
                            <span className="badge badge-green text-xs capitalize">{booking.payment_status || 'paid'}</span>
                        </div>
                    </div>
                )}

                {/* Actions */}
                <div className="card card-p" style={{ display: 'flex', flexDirection: 'column', gap: '12px' }}>
                    <h2 className="font-semibold text-sm" style={{ color: 'var(--text-3)', textTransform: 'uppercase', letterSpacing: '0.05em' }}>Actions</h2>

                    {booking.meet_link && booking.status === 'confirmed' && isUpcoming && (
                        <a
                            href={booking.meet_link}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="btn btn-purple"
                        >
                            Join meeting
                        </a>
                    )}

                    {booking.status === 'completed' && booking.role === 'mentee' && (
                        <a
                            href={`/paired/mentors/${booking.mentor?.id || ''}/review`}
                            className="btn btn-outline"
                        >
                            Leave a review
                        </a>
                    )}

                    {booking.role === 'mentee' && other && (
                        <a
                            href={`/paired/mentors/${other.id}`}
                            className="btn btn-outline"
                        >
                            View mentor profile
                        </a>
                    )}

                    <BookingDetailActions
                        bookingId={booking.id}
                        status={booking.status}
                        role={booking.role}
                        isUpcoming={isUpcoming}
                    />
                </div>
            </div>
        </div>
    );
}
