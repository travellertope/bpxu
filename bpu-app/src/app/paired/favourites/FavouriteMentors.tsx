'use client';

import { useState } from 'react';
import { decodeHtml } from '@/lib/utils';

interface Mentor {
    id: number;
    display_name: string;
    avatar_url?: string;
    industry?: string;
    job_title?: string;
    rating?: number;
    total_reviews?: number;
}

interface Props {
    initialMentors: Mentor[];
}

function initialsColor(id: number): string {
    const colors = ['#6366f1', '#8b5cf6', '#ec4899', '#3b82f6', '#14b8a6', '#f59e0b', '#ef4444'];
    return colors[id % colors.length];
}

export default function FavouriteMentors({ initialMentors }: Props) {
    const [mentors, setMentors] = useState<Mentor[]>(initialMentors);
    const [removing, setRemoving] = useState<number | null>(null);

    async function handleRemove(mentorId: number) {
        setRemoving(mentorId);
        try {
            const res = await fetch(`/api/paired/favourites/${mentorId}`, {
                method: 'DELETE',
            });
            if (res.ok) {
                setMentors(prev => prev.filter(m => m.id !== mentorId));
            }
        } catch {
            // Silently fail
        } finally {
            setRemoving(null);
        }
    }

    if (mentors.length === 0) {
        return (
            <div className="card card-p text-center py-16 space-y-4">
                <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--text-3)" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" style={{ margin: '0 auto', opacity: 0.5 }}>
                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                </svg>
                <p className="font-semibold text-text-2">No favourite mentors yet</p>
                <p className="text-sm text-text-3">Browse mentors to find your match.</p>
                <a href="/paired/mentors" className="btn btn-purple btn-sm">Browse mentors</a>
            </div>
        );
    }

    return (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            {mentors.map((mentor) => (
                <div key={mentor.id} className="card card-lift card-p flex flex-col">
                    <div className="flex items-start gap-4 mb-4">
                        {mentor.avatar_url ? (
                            <img
                                src={mentor.avatar_url}
                                alt={decodeHtml(mentor.display_name)}
                                className="rounded-full shrink-0 object-cover"
                                style={{ width: 56, height: 56 }}
                            />
                        ) : (
                            <div
                                className="avatar shrink-0 text-white font-bold"
                                style={{
                                    background: initialsColor(mentor.id),
                                    width: 56,
                                    height: 56,
                                    fontSize: '1.25rem',
                                }}
                            >
                                {decodeHtml(mentor.display_name)?.[0] || '?'}
                            </div>
                        )}
                        <div className="flex-1 min-w-0">
                            <h3 className="font-bold text-text truncate">{decodeHtml(mentor.display_name)}</h3>
                            {mentor.job_title && (
                                <p className="text-sm text-text-2 truncate">{mentor.job_title}</p>
                            )}
                            {mentor.industry && (
                                <span className="badge badge-purple mt-1">{mentor.industry}</span>
                            )}
                        </div>
                    </div>

                    {/* Rating */}
                    {mentor.rating != null && mentor.rating > 0 && (
                        <div className="flex items-center gap-1 mb-4 text-sm text-text-2">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" strokeWidth="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                            </svg>
                            <span>{mentor.rating.toFixed(1)}</span>
                            {mentor.total_reviews != null && (
                                <span className="text-text-3">({mentor.total_reviews} review{mentor.total_reviews !== 1 ? 's' : ''})</span>
                            )}
                        </div>
                    )}

                    <div className="flex items-center gap-2 mt-auto">
                        <a href={`/paired/mentors/${mentor.id}`} className="btn btn-purple btn-sm flex-1 text-center">
                            View profile
                        </a>
                        <button
                            onClick={() => handleRemove(mentor.id)}
                            disabled={removing === mentor.id}
                            className="btn btn-ghost btn-sm"
                            style={{ color: 'var(--brand)' }}
                            title="Remove from favourites"
                        >
                            {removing === mentor.id ? (
                                <span className="text-sm">...</span>
                            ) : (
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="var(--brand)" stroke="var(--brand)" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
                                </svg>
                            )}
                        </button>
                    </div>
                </div>
            ))}
        </div>
    );
}
