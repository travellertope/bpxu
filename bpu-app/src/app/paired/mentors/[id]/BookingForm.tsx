'use client';

import { useState } from 'react';

const TIME_SLOTS = [
    '09:00-10:00',
    '10:00-11:00',
    '11:00-12:00',
    '13:00-14:00',
    '14:00-15:00',
    '15:00-16:00',
    '16:00-17:00',
    '17:00-18:00',
];

interface Props {
    mentorId: number;
    mentorName: string;
    isAuthenticated: boolean;
}

export default function BookingForm({ mentorId, mentorName, isAuthenticated }: Props) {
    const [date, setDate] = useState('');
    const [timeSlot, setTimeSlot] = useState('');
    const [notes, setNotes] = useState('');
    const [loading, setLoading] = useState(false);
    const [success, setSuccess] = useState(false);
    const [error, setError] = useState('');

    const today = new Date().toISOString().split('T')[0];

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (!date || !timeSlot) {
            setError('Please select a date and time slot.');
            return;
        }
        setLoading(true);
        setError('');
        try {
            const res = await fetch('/api/paired/bookings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ mentor_id: mentorId, date, time_slot: timeSlot, notes }),
            });
            const data = await res.json();
            if (!res.ok) {
                setError(data.message || data.error || 'Booking failed. Please try again.');
            } else {
                setSuccess(true);
            }
        } catch {
            setError('Something went wrong. Please try again.');
        } finally {
            setLoading(false);
        }
    }

    if (!isAuthenticated) {
        return (
            <div className="card card-p sticky top-20 space-y-4">
                <p className="section-title">Book a session</p>
                <p className="text-sm text-text-2">
                    Sign in to book a free 1-on-1 session with {mentorName}.
                </p>
                <a
                    href={`/login?returnTo=/paired/mentors/${mentorId}`}
                    className="btn btn-purple btn-sm w-full"
                    style={{ display: 'block', textAlign: 'center' }}
                >
                    Sign in to book
                </a>
            </div>
        );
    }

    if (success) {
        return (
            <div className="card card-p sticky top-20 space-y-4">
                <div className="alert alert-green text-sm">
                    <strong>Booking requested!</strong> {mentorName} will confirm your session shortly.
                </div>
                <button
                    className="btn btn-outline btn-sm w-full"
                    onClick={() => { setSuccess(false); setDate(''); setTimeSlot(''); setNotes(''); }}
                >
                    Book another slot
                </button>
                <a href="/paired/dashboard" className="btn btn-ghost btn-sm w-full" style={{ display: 'block', textAlign: 'center' }}>
                    View my sessions →
                </a>
            </div>
        );
    }

    return (
        <div className="card card-p sticky top-20 space-y-4">
            <p className="section-title">Book a free session</p>
            <form onSubmit={handleSubmit} className="space-y-3">
                <div>
                    <label className="field-label">Date</label>
                    <input
                        type="date"
                        className="field-input w-full"
                        min={today}
                        value={date}
                        onChange={e => setDate(e.target.value)}
                        required
                    />
                </div>
                <div>
                    <label className="field-label">Time slot (GMT)</label>
                    <select
                        className="field-input w-full"
                        value={timeSlot}
                        onChange={e => setTimeSlot(e.target.value)}
                        required
                    >
                        <option value="">Select a time…</option>
                        {TIME_SLOTS.map(s => (
                            <option key={s} value={s}>
                                {s.replace('-', ' – ')} GMT
                            </option>
                        ))}
                    </select>
                </div>
                <div>
                    <label className="field-label">Notes (optional)</label>
                    <textarea
                        className="field-input field-textarea w-full"
                        placeholder="What would you like to discuss?"
                        rows={3}
                        value={notes}
                        onChange={e => setNotes(e.target.value)}
                    />
                </div>
                {error && <div className="alert alert-red text-sm">{error}</div>}
                <button type="submit" disabled={loading} className="btn btn-purple btn-sm w-full">
                    {loading ? 'Booking…' : 'Request session'}
                </button>
                <p className="text-xs text-text-3 text-center">Free · 60 min · Video call</p>
            </form>
        </div>
    );
}
