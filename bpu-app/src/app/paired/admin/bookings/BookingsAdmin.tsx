'use client';

import { useState, useEffect, useCallback } from 'react';
import { decodeHtml } from '@/lib/utils';

interface BookingUser {
    id: number;
    display_name: string;
    email: string;
    avatar_url?: string;
}

interface Booking {
    id: number;
    title: string;
    status: string;
    date: string;
    time?: string;
    mentor?: BookingUser;
    mentee?: BookingUser;
    price?: number;
    currency?: string;
}

type StatusFilter = 'all' | 'pending' | 'confirmed' | 'completed' | 'cancelled';

const STATUS_TABS: { value: StatusFilter; label: string }[] = [
    { value: 'all', label: 'All' },
    { value: 'pending', label: 'Pending' },
    { value: 'confirmed', label: 'Confirmed' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
];

const VALID_STATUSES = ['pending', 'confirmed', 'completed', 'cancelled'];

const STATUS_BADGE: Record<string, string> = {
    pending: 'badge-amber',
    confirmed: 'badge-purple',
    completed: 'badge-green',
    cancelled: 'badge-red',
};

function Avatar({ user, size = 32 }: { user?: BookingUser; size?: number }) {
    if (!user) return <div style={{ width: size, height: size, borderRadius: '50%', background: 'var(--border)' }} />;
    if (user.avatar_url) return <img src={user.avatar_url} alt="" className="rounded-full object-cover" style={{ width: size, height: size }} />;
    return (
        <div className="avatar text-white" style={{ background: '#7c3aed', width: size, height: size, fontSize: size < 36 ? '0.7rem' : '0.875rem' }}>
            {decodeHtml(user.display_name)?.[0] || '?'}
        </div>
    );
}

export default function BookingsAdmin() {
    const [bookings, setBookings] = useState<Booking[]>([]);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [statusFilter, setStatusFilter] = useState<StatusFilter>('all');
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [page, setPage] = useState(1);
    const [updatingId, setUpdatingId] = useState<number | null>(null);
    const perPage = 20;

    const fetchBookings = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const params = new URLSearchParams({ page: String(page), per_page: String(perPage) });
            if (statusFilter !== 'all') params.set('status', statusFilter);
            if (search.trim()) params.set('search', search.trim());

            const res = await fetch(`/api/paired/admin/bookings?${params}`);
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load bookings.');
            setBookings(data.bookings || []);
            setTotal(data.total || 0);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load bookings.');
        } finally {
            setLoading(false);
        }
    }, [page, statusFilter, search]);

    useEffect(() => {
        fetchBookings();
    }, [fetchBookings]);

    function handleSearch(e: React.FormEvent) {
        e.preventDefault();
        setSearch(searchInput);
        setPage(1);
    }

    function handleTabChange(val: StatusFilter) {
        setStatusFilter(val);
        setPage(1);
    }

    async function updateStatus(bookingId: number, newStatus: string) {
        setUpdatingId(bookingId);
        try {
            const res = await fetch(`/api/paired/admin/bookings/${bookingId}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newStatus }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to update status.');
            setBookings(prev => prev.map(b => b.id === bookingId ? { ...b, status: newStatus } : b));
        } catch (e) {
            alert(e instanceof Error ? e.message : 'Action failed.');
        } finally {
            setUpdatingId(null);
        }
    }

    const totalPages = Math.ceil(total / perPage);

    return (
        <div className="space-y-5">
            {/* Tabs */}
            <div style={{ display: 'flex', gap: 4, background: 'var(--surface)', padding: 4, borderRadius: 10, width: 'fit-content', flexWrap: 'wrap' }}>
                {STATUS_TABS.map(tab => (
                    <button
                        key={tab.value}
                        onClick={() => handleTabChange(tab.value)}
                        className={statusFilter === tab.value ? 'btn btn-purple btn-sm' : 'btn btn-ghost btn-sm'}
                        style={{ fontSize: '0.8rem' }}
                    >
                        {tab.label}
                    </button>
                ))}
            </div>

            {/* Search */}
            <form onSubmit={handleSearch} className="flex gap-2">
                <input
                    type="text"
                    className="field-input flex-1"
                    placeholder="Search by mentee or mentor name..."
                    value={searchInput}
                    onChange={e => setSearchInput(e.target.value)}
                />
                <button type="submit" className="btn btn-purple btn-sm">Search</button>
                {search && (
                    <button type="button" className="btn btn-ghost btn-sm" onClick={() => { setSearch(''); setSearchInput(''); setPage(1); }}>
                        Clear
                    </button>
                )}
            </form>

            {loading ? (
                <div className="text-center text-sm text-text-2 py-12">Loading bookings...</div>
            ) : error ? (
                <div className="card card-p text-center py-10" style={{ color: 'var(--err)' }}>{error}</div>
            ) : bookings.length === 0 ? (
                <div className="card card-p text-center py-10">
                    <p className="font-semibold text-text-2">No bookings found</p>
                    <p className="text-sm text-text-3 mt-1">Try adjusting your filters.</p>
                </div>
            ) : (
                <>
                    <p className="text-sm text-text-3">{total} booking{total !== 1 ? 's' : ''} found</p>

                    {/* Desktop Table */}
                    <div className="card hidden md:block" style={{ overflow: 'hidden' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid var(--border)', background: 'var(--surface)' }}>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">#</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Mentee</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Mentor</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Date</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Status</th>
                                    <th className="text-right text-xs font-semibold text-text-3 p-3">Update Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {bookings.map(b => (
                                    <tr key={b.id} style={{ borderBottom: '1px solid var(--border)' }}>
                                        <td className="p-3 text-xs text-text-3 font-mono">{b.id}</td>
                                        <td className="p-3">
                                            <div className="flex items-center gap-2">
                                                <Avatar user={b.mentee} size={28} />
                                                <div>
                                                    <p className="text-sm font-semibold leading-tight">{decodeHtml(b.mentee?.display_name || '—')}</p>
                                                    <p className="text-xs text-text-3">{b.mentee?.email}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="p-3">
                                            <div className="flex items-center gap-2">
                                                <Avatar user={b.mentor} size={28} />
                                                <div>
                                                    <p className="text-sm font-semibold leading-tight">{decodeHtml(b.mentor?.display_name || '—')}</p>
                                                    <p className="text-xs text-text-3">{b.mentor?.email}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="p-3 text-sm text-text-2">
                                            {b.date}
                                            {b.time && <span className="text-text-3"> · {b.time}</span>}
                                        </td>
                                        <td className="p-3 text-center">
                                            <span className={`badge ${STATUS_BADGE[b.status] || 'badge-amber'}`}>{b.status}</span>
                                        </td>
                                        <td className="p-3 text-right">
                                            <select
                                                className="field-input"
                                                style={{ fontSize: '0.75rem', padding: '4px 8px', height: 'auto', display: 'inline-block', width: 'auto' }}
                                                value={b.status}
                                                disabled={updatingId === b.id}
                                                onChange={e => updateStatus(b.id, e.target.value)}
                                            >
                                                {VALID_STATUSES.map(s => (
                                                    <option key={s} value={s}>{s.charAt(0).toUpperCase() + s.slice(1)}</option>
                                                ))}
                                            </select>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Mobile Cards */}
                    <div className="md:hidden space-y-3">
                        {bookings.map(b => (
                            <div key={b.id} className="card card-p space-y-3">
                                <div className="flex items-center justify-between">
                                    <span className="text-xs font-mono text-text-3">#{b.id}</span>
                                    <span className={`badge ${STATUS_BADGE[b.status] || 'badge-amber'}`}>{b.status}</span>
                                </div>
                                <div className="grid grid-cols-2 gap-3">
                                    <div>
                                        <p className="text-xs text-text-3 mb-1">Mentee</p>
                                        <div className="flex items-center gap-2">
                                            <Avatar user={b.mentee} size={24} />
                                            <p className="text-sm font-semibold truncate">{decodeHtml(b.mentee?.display_name || '—')}</p>
                                        </div>
                                    </div>
                                    <div>
                                        <p className="text-xs text-text-3 mb-1">Mentor</p>
                                        <div className="flex items-center gap-2">
                                            <Avatar user={b.mentor} size={24} />
                                            <p className="text-sm font-semibold truncate">{decodeHtml(b.mentor?.display_name || '—')}</p>
                                        </div>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-xs text-text-3">{b.date}{b.time ? ` · ${b.time}` : ''}</span>
                                    <select
                                        className="field-input"
                                        style={{ fontSize: '0.75rem', padding: '4px 8px', height: 'auto', display: 'inline-block', width: 'auto' }}
                                        value={b.status}
                                        disabled={updatingId === b.id}
                                        onChange={e => updateStatus(b.id, e.target.value)}
                                    >
                                        {VALID_STATUSES.map(s => (
                                            <option key={s} value={s}>{s.charAt(0).toUpperCase() + s.slice(1)}</option>
                                        ))}
                                    </select>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Pagination */}
                    {totalPages > 1 && (
                        <div className="flex items-center justify-center gap-2 pt-2">
                            <button onClick={() => setPage(p => Math.max(1, p - 1))} disabled={page === 1} className="btn btn-outline btn-sm">Previous</button>
                            <span className="text-sm text-text-2">Page {page} of {totalPages}</span>
                            <button onClick={() => setPage(p => Math.min(totalPages, p + 1))} disabled={page === totalPages} className="btn btn-outline btn-sm">Next</button>
                        </div>
                    )}
                </>
            )}
        </div>
    );
}
