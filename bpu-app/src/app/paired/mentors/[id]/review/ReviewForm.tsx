'use client';

import { useState, useEffect } from 'react';

interface Booking {
    id: number;
    date: string;
    session_name?: string;
}

interface Props {
    mentorId: number;
    mentorName: string;
}

export default function ReviewForm({ mentorId, mentorName }: Props) {
    const [rating, setRating] = useState(0);
    const [hoverRating, setHoverRating] = useState(0);
    const [feedback, setFeedback] = useState('');
    const [bookingId, setBookingId] = useState('');
    const [bookings, setBookings] = useState<Booking[]>([]);
    const [loadingBookings, setLoadingBookings] = useState(true);
    const [loading, setLoading] = useState(false);
    const [success, setSuccess] = useState(false);
    const [error, setError] = useState('');

    useEffect(() => {
        async function fetchBookings() {
            try {
                const res = await fetch(`/api/paired/bookings?mentor_id=${mentorId}&status=completed&reviewable=true`);
                if (res.ok) {
                    const data = await res.json();
                    setBookings(data.bookings || []);
                }
            } catch {
                // Silently fail
            } finally {
                setLoadingBookings(false);
            }
        }
        fetchBookings();
    }, [mentorId]);

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (rating === 0) {
            setError('Please select a rating.');
            return;
        }
        if (!bookingId) {
            setError('Please select a booking to review.');
            return;
        }

        setLoading(true);
        setError('');

        try {
            const res = await fetch('/api/paired/reviews', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    mentor_id: mentorId,
                    booking_id: Number(bookingId),
                    rating,
                    feedback,
                }),
            });
            const data = await res.json();
            if (!res.ok) {
                setError(data.message || data.error || 'Failed to submit review.');
            } else {
                setSuccess(true);
            }
        } catch {
            setError('Something went wrong. Please try again.');
        } finally {
            setLoading(false);
        }
    }

    if (success) {
        return (
            <div className="card card-p text-center space-y-4">
                <div style={{ fontSize: '3rem' }}>
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--ok)" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ margin: '0 auto' }}><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <h2 className="text-xl font-bold">Thank you!</h2>
                <p className="text-text-2">Your review of {mentorName} has been submitted successfully.</p>
                <a href={`/paired/mentors/${mentorId}`} className="btn btn-purple">
                    Back to mentor profile
                </a>
            </div>
        );
    }

    const activeRating = hoverRating || rating;

    return (
        <form onSubmit={handleSubmit} className="card card-p space-y-6">
            {error && (
                <div className="alert alert-red">{error}</div>
            )}

            {/* Star Rating */}
            <div>
                <label className="field-label mb-2 block">Rating</label>
                <div className="flex gap-1">
                    {[1, 2, 3, 4, 5].map((star) => (
                        <button
                            key={star}
                            type="button"
                            onClick={() => setRating(star)}
                            onMouseEnter={() => setHoverRating(star)}
                            onMouseLeave={() => setHoverRating(0)}
                            style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 4 }}
                            aria-label={`${star} star${star > 1 ? 's' : ''}`}
                        >
                            <svg
                                width="32"
                                height="32"
                                viewBox="0 0 24 24"
                                fill={star <= activeRating ? '#f59e0b' : 'none'}
                                stroke={star <= activeRating ? '#f59e0b' : 'var(--text-3)'}
                                strokeWidth="2"
                                strokeLinecap="round"
                                strokeLinejoin="round"
                            >
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                            </svg>
                        </button>
                    ))}
                </div>
                {rating > 0 && (
                    <p className="text-sm text-text-3 mt-1">
                        {rating === 1 && 'Poor'}
                        {rating === 2 && 'Below average'}
                        {rating === 3 && 'Average'}
                        {rating === 4 && 'Good'}
                        {rating === 5 && 'Excellent'}
                    </p>
                )}
            </div>

            {/* Booking Select */}
            <div>
                <label htmlFor="booking" className="field-label mb-2 block">Session</label>
                {loadingBookings ? (
                    <p className="text-sm text-text-3">Loading your sessions...</p>
                ) : bookings.length === 0 ? (
                    <p className="text-sm text-text-3">No completed sessions available to review.</p>
                ) : (
                    <select
                        id="booking"
                        className="field-input"
                        value={bookingId}
                        onChange={(e) => setBookingId(e.target.value)}
                    >
                        <option value="">Select a completed session</option>
                        {bookings.map((b) => (
                            <option key={b.id} value={b.id}>
                                {b.session_name || 'Session'} — {new Date(b.date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })}
                            </option>
                        ))}
                    </select>
                )}
            </div>

            {/* Feedback */}
            <div>
                <label htmlFor="feedback" className="field-label mb-2 block">Written feedback (optional)</label>
                <textarea
                    id="feedback"
                    className="field-textarea"
                    rows={5}
                    value={feedback}
                    onChange={(e) => setFeedback(e.target.value)}
                    placeholder="Share what you found helpful, what could improve, or how the session impacted you..."
                />
            </div>

            {/* Submit */}
            <button
                type="submit"
                className="btn btn-purple btn-lg w-full"
                disabled={loading || rating === 0 || !bookingId || bookings.length === 0}
            >
                {loading ? 'Submitting...' : 'Submit Review'}
            </button>
        </form>
    );
}
