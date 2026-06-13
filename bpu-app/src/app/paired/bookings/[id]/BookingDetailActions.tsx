'use client';

import { useState } from 'react';

interface Props {
    bookingId: number;
    status: string;
    role: 'mentee' | 'mentor';
    isUpcoming: boolean;
}

export default function BookingDetailActions({ bookingId, status, role, isUpcoming }: Props) {
    const [currentStatus, setCurrentStatus] = useState(status);
    const [loading, setLoading] = useState<string | null>(null);
    const [confirming, setConfirming] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    async function updateStatus(newStatus: string) {
        setLoading(newStatus);
        setError('');
        setSuccess('');
        try {
            const res = await fetch(`/api/paired/bookings/${bookingId}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newStatus }),
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                setError(data.error || 'Failed to update booking.');
                return;
            }
            setCurrentStatus(newStatus);
            setConfirming(false);
            const labels: Record<string, string> = {
                confirmed: 'Booking accepted.',
                cancelled: 'Booking cancelled.',
                completed: 'Marked as complete.',
            };
            setSuccess(labels[newStatus] || 'Updated.');
        } catch {
            setError('Something went wrong. Please try again.');
        } finally {
            setLoading(null);
        }
    }

    if (currentStatus === 'cancelled') {
        return (
            <p className="text-sm text-text-3 text-center py-2">This booking has been cancelled.</p>
        );
    }

    const canCancel = (currentStatus === 'pending' || currentStatus === 'confirmed') && isUpcoming;
    const canAccept = role === 'mentor' && currentStatus === 'pending';
    const canComplete = role === 'mentor' && currentStatus === 'confirmed' && !isUpcoming;

    if (!canCancel && !canAccept && !canComplete) return null;

    return (
        <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
            {error && <div className="alert alert-red text-sm">{error}</div>}
            {success && <div className="alert alert-green text-sm">{success}</div>}

            {canAccept && (
                <button
                    className="btn btn-purple"
                    disabled={loading === 'confirmed'}
                    onClick={() => updateStatus('confirmed')}
                >
                    {loading === 'confirmed' ? 'Accepting...' : 'Accept booking'}
                </button>
            )}

            {canComplete && (
                <button
                    className="btn btn-outline"
                    disabled={loading === 'completed'}
                    onClick={() => updateStatus('completed')}
                >
                    {loading === 'completed' ? 'Completing...' : 'Mark as complete'}
                </button>
            )}

            {canCancel && (
                confirming ? (
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
                        <p className="text-sm text-text-2">Are you sure you want to cancel this booking?</p>
                        <div className="flex gap-2">
                            <button
                                className="btn btn-sm"
                                style={{ backgroundColor: 'var(--err)', color: '#fff' }}
                                disabled={loading === 'cancelled'}
                                onClick={() => updateStatus('cancelled')}
                            >
                                {loading === 'cancelled' ? 'Cancelling...' : 'Yes, cancel'}
                            </button>
                            <button
                                className="btn btn-ghost btn-sm"
                                onClick={() => setConfirming(false)}
                            >
                                No, keep it
                            </button>
                        </div>
                    </div>
                ) : (
                    <button
                        className="btn btn-outline"
                        style={{ color: 'var(--err)', borderColor: 'var(--err)' }}
                        onClick={() => setConfirming(true)}
                    >
                        Cancel booking
                    </button>
                )
            )}
        </div>
    );
}
