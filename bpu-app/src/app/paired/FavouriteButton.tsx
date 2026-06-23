'use client';

import { useState } from 'react';

interface Props {
    mentorId: number;
    initialFavourited?: boolean;
    size?: number;
}

export default function FavouriteButton({ mentorId, initialFavourited = false, size = 22 }: Props) {
    const [favourited, setFavourited] = useState(initialFavourited);
    const [loading, setLoading] = useState(false);

    async function toggle() {
        setLoading(true);

        try {
            let res: Response;
            if (favourited) {
                res = await fetch(`/api/paired/favourites/${mentorId}`, { method: 'DELETE' });
            } else {
                res = await fetch('/api/paired/favourites', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ mentor_id: mentorId }),
                });
            }
            if (res.ok) {
                setFavourited(!favourited);
            }
        } catch {
            // Silently fail
        } finally {
            setLoading(false);
        }
    }

    return (
        <button
            onClick={(e) => {
                e.preventDefault();
                e.stopPropagation();
                toggle();
            }}
            disabled={loading}
            aria-label={favourited ? 'Remove from favourites' : 'Add to favourites'}
            title={favourited ? 'Remove from favourites' : 'Add to favourites'}
            style={{
                background: 'none',
                border: 'none',
                cursor: loading ? 'wait' : 'pointer',
                padding: 4,
                display: 'inline-flex',
                alignItems: 'center',
                justifyContent: 'center',
                transition: 'transform 0.15s ease',
                transform: loading ? 'scale(0.9)' : 'scale(1)',
            }}
        >
            <svg
                width={size}
                height={size}
                viewBox="0 0 24 24"
                fill={favourited ? 'var(--brand)' : 'none'}
                stroke={favourited ? 'var(--brand)' : 'var(--text-3)'}
                strokeWidth="2"
                strokeLinecap="round"
                strokeLinejoin="round"
            >
                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
            </svg>
        </button>
    );
}
