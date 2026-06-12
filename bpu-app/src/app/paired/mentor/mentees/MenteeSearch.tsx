'use client';

import { useState } from 'react';

interface MenteeCard {
    id: number;
    display_name: string;
    email: string;
    avatar_url: string;
    booking_count: number;
    last_booking_date: string;
    isGravatar: boolean;
    initials: string;
    color: string;
    formatted_last_date: string;
    decoded_name: string;
}

export default function MenteeSearch({ mentees }: { mentees: MenteeCard[] }) {
    const [query, setQuery] = useState('');

    const filtered = query.trim()
        ? mentees.filter(m =>
            m.decoded_name.toLowerCase().includes(query.toLowerCase()) ||
            m.email.toLowerCase().includes(query.toLowerCase())
        )
        : mentees;

    return (
        <>
            {/* Search */}
            {mentees.length > 3 && (
                <div className="mb-6">
                    <input
                        type="text"
                        className="field-input"
                        placeholder="Search by name or email..."
                        value={query}
                        onChange={e => setQuery(e.target.value)}
                        style={{ maxWidth: 360 }}
                    />
                </div>
            )}

            {/* Grid */}
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                {filtered.map(m => (
                    <div key={m.id} className="card card-p card-lift space-y-3">
                        <div className="flex items-center gap-3">
                            {m.isGravatar ? (
                                <div
                                    className="avatar avatar-md text-white font-bold text-sm"
                                    style={{ background: m.color, width: 48, height: 48, display: 'flex', alignItems: 'center', justifyContent: 'center', borderRadius: '50%' }}
                                >
                                    {m.initials}
                                </div>
                            ) : (
                                <img
                                    src={m.avatar_url}
                                    alt={m.decoded_name}
                                    style={{ width: 48, height: 48, borderRadius: '50%', objectFit: 'cover' }}
                                />
                            )}
                            <div style={{ minWidth: 0 }}>
                                <p className="font-bold text-sm" style={{ overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                                    {m.decoded_name}
                                </p>
                                <p className="text-xs text-text-3" style={{ overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                                    {m.email}
                                </p>
                            </div>
                        </div>
                        <div className="flex items-center justify-between">
                            <span className="badge badge-purple text-xs">
                                {m.booking_count} session{m.booking_count !== 1 ? 's' : ''}
                            </span>
                            <span className="text-xs text-text-3">
                                Last: {m.formatted_last_date}
                            </span>
                        </div>
                        <a
                            href="/paired/mentor/bookings"
                            className="btn btn-ghost btn-sm w-full text-center text-xs"
                            style={{ display: 'block' }}
                        >
                            View bookings
                        </a>
                    </div>
                ))}
            </div>

            {filtered.length === 0 && query.trim() && (
                <div className="card card-p text-center py-8 mt-4">
                    <p className="text-text-3 text-sm">No mentees match &ldquo;{query}&rdquo;</p>
                </div>
            )}
        </>
    );
}
