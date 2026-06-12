'use client';

import { useState } from 'react';

interface Props {
    bookingId: number;
}

export default function BookingActions({ bookingId }: Props) {
    const [status, setStatus] = useState<'idle' | 'loading' | 'accepted' | 'declined' | 'error'>('idle');
    const [errorMsg, setErrorMsg] = useState('');

    async function updateStatus(newStatus: 'confirmed' | 'cancelled') {
        setStatus('loading');
        setErrorMsg('');
        try {
            const res = await fetch(`/api/paired/bookings/${bookingId}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newStatus }),
            });
            if (!res.ok) {
                const data = await res.json().catch(() => ({}));
                throw new Error(data.error || 'Failed to update booking.');
            }
            setStatus(newStatus === 'confirmed' ? 'accepted' : 'declined');
        } catch (e: unknown) {
            setErrorMsg(e instanceof Error ? e.message : 'Something went wrong.');
            setStatus('error');
        }
    }

    if (status === 'accepted') {
        return <span className="badge badge-green text-xs">Accepted</span>;
    }
    if (status === 'declined') {
        return <span className="badge text-xs" style={{ opacity: 0.6 }}>Declined</span>;
    }

    return (
        <div className="flex flex-col gap-2">
            <div className="flex gap-2">
                <button
                    className="btn btn-purple btn-sm text-xs"
                    onClick={() => updateStatus('confirmed')}
                    disabled={status === 'loading'}
                >
                    {status === 'loading' ? 'Updating...' : 'Accept'}
                </button>
                <button
                    className="btn btn-outline btn-sm text-xs"
                    onClick={() => updateStatus('cancelled')}
                    disabled={status === 'loading'}
                >
                    Decline
                </button>
            </div>
            {status === 'error' && (
                <p className="text-xs" style={{ color: 'var(--red, #ef4444)' }}>{errorMsg}</p>
            )}
        </div>
    );
}
