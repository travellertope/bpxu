'use client';

import { useState, useEffect, useCallback } from 'react';
import { decodeHtml } from '@/lib/utils';

interface Mentee {
    id: number;
    display_name: string;
    email: string;
    avatar_url?: string;
    industry?: string;
    booking_count?: number;
    last_booking_date?: string;
    is_active: boolean;
}

export default function MenteeAdmin() {
    const [mentees, setMentees] = useState<Mentee[]>([]);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [page, setPage] = useState(1);
    const [actionLoadingId, setActionLoadingId] = useState<number | null>(null);
    const [confirmDeactivate, setConfirmDeactivate] = useState<Mentee | null>(null);
    const perPage = 20;

    const fetchMentees = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const params = new URLSearchParams({ page: String(page), per_page: String(perPage) });
            if (search.trim()) params.set('search', search.trim());

            const res = await fetch(`/api/paired/admin/mentees?${params}`);
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load mentees.');
            setMentees(data.mentees || []);
            setTotal(data.total || 0);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load mentees.');
        } finally {
            setLoading(false);
        }
    }, [page, search]);

    useEffect(() => {
        fetchMentees();
    }, [fetchMentees]);

    function handleSearch(e: React.FormEvent) {
        e.preventDefault();
        setSearch(searchInput);
        setPage(1);
    }

    async function toggleActive(mentee: Mentee) {
        setActionLoadingId(mentee.id);
        const endpoint = mentee.is_active ? 'deactivate' : 'activate';
        try {
            const res = await fetch(`/api/paired/admin/mentees/${mentee.id}/${endpoint}`, { method: 'POST' });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Action failed.');
            setMentees(prev => prev.map(m => m.id === mentee.id ? { ...m, is_active: !mentee.is_active } : m));
            setConfirmDeactivate(null);
        } catch (e) {
            alert(e instanceof Error ? e.message : 'Action failed.');
        } finally {
            setActionLoadingId(null);
        }
    }

    const totalPages = Math.ceil(total / perPage);

    return (
        <div className="space-y-5">
            {/* Search */}
            <form onSubmit={handleSearch} className="flex gap-2">
                <input
                    type="text"
                    className="field-input flex-1"
                    placeholder="Search by name or email..."
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
                <div className="text-center text-sm text-text-2 py-12">Loading mentees...</div>
            ) : error ? (
                <div className="card card-p text-center py-10" style={{ color: 'var(--err)' }}>{error}</div>
            ) : mentees.length === 0 ? (
                <div className="card card-p text-center py-10">
                    <p className="font-semibold text-text-2">No mentees found</p>
                    <p className="text-sm text-text-3 mt-1">Try adjusting your search.</p>
                </div>
            ) : (
                <>
                    <p className="text-sm text-text-3">{total} mentee{total !== 1 ? 's' : ''} found</p>

                    {/* Desktop Table */}
                    <div className="card hidden md:block" style={{ overflow: 'hidden' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid var(--border)', background: 'var(--surface)' }}>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Name</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Email</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Industry</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Bookings</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Last Booking</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Status</th>
                                    <th className="text-right text-xs font-semibold text-text-3 p-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {mentees.map(m => (
                                    <tr key={m.id} style={{ borderBottom: '1px solid var(--border)' }}>
                                        <td className="p-3">
                                            <div className="flex items-center gap-2">
                                                {m.avatar_url ? (
                                                    <img src={m.avatar_url} alt="" className="rounded-full object-cover" style={{ width: 28, height: 28 }} />
                                                ) : (
                                                    <div className="avatar text-white" style={{ background: '#0ea5e9', width: 28, height: 28, fontSize: '0.7rem' }}>
                                                        {decodeHtml(m.display_name)?.[0] || '?'}
                                                    </div>
                                                )}
                                                <span className="text-sm font-semibold">{decodeHtml(m.display_name)}</span>
                                            </div>
                                        </td>
                                        <td className="p-3 text-sm text-text-2">{m.email}</td>
                                        <td className="p-3 text-sm text-text-2">{m.industry || '-'}</td>
                                        <td className="p-3 text-sm text-text-2 text-center">{m.booking_count ?? 0}</td>
                                        <td className="p-3 text-sm text-text-2">{m.last_booking_date || '-'}</td>
                                        <td className="p-3 text-center">
                                            <span className={`badge ${m.is_active ? 'badge-green' : 'badge-amber'}`}>
                                                {m.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                        <td className="p-3 text-right">
                                            <button
                                                onClick={() => m.is_active ? setConfirmDeactivate(m) : toggleActive(m)}
                                                disabled={actionLoadingId === m.id}
                                                className="btn btn-ghost btn-sm text-xs"
                                                style={{ color: m.is_active ? 'var(--err)' : 'var(--ok)' }}
                                            >
                                                {actionLoadingId === m.id ? '...' : m.is_active ? 'Deactivate' : 'Activate'}
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Mobile Cards */}
                    <div className="md:hidden space-y-3">
                        {mentees.map(m => (
                            <div key={m.id} className="card card-p">
                                <div className="flex items-start gap-3 mb-3">
                                    {m.avatar_url ? (
                                        <img src={m.avatar_url} alt="" className="rounded-full object-cover" style={{ width: 40, height: 40 }} />
                                    ) : (
                                        <div className="avatar text-white" style={{ background: '#0ea5e9', width: 40, height: 40, fontSize: '0.875rem' }}>
                                            {decodeHtml(m.display_name)?.[0] || '?'}
                                        </div>
                                    )}
                                    <div className="flex-1 min-w-0">
                                        <p className="font-bold text-sm truncate">{decodeHtml(m.display_name)}</p>
                                        <p className="text-xs text-text-2 truncate">{m.email}</p>
                                    </div>
                                    <span className={`badge ${m.is_active ? 'badge-green' : 'badge-amber'}`}>
                                        {m.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </div>
                                <div className="flex items-center gap-4 text-xs text-text-3 mb-3">
                                    {m.industry && <span>{m.industry}</span>}
                                    <span>{m.booking_count ?? 0} bookings</span>
                                    {m.last_booking_date && <span>Last: {m.last_booking_date}</span>}
                                </div>
                                <button
                                    onClick={() => m.is_active ? setConfirmDeactivate(m) : toggleActive(m)}
                                    disabled={actionLoadingId === m.id}
                                    className="btn btn-ghost btn-sm text-xs"
                                    style={{ color: m.is_active ? 'var(--err)' : 'var(--ok)' }}
                                >
                                    {actionLoadingId === m.id ? '...' : m.is_active ? 'Deactivate' : 'Activate'}
                                </button>
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

            {/* Deactivate Confirmation Modal */}
            {confirmDeactivate && (
                <div
                    style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 100, padding: 16 }}
                    onClick={() => setConfirmDeactivate(null)}
                >
                    <div className="card card-p" style={{ width: '100%', maxWidth: 400 }} onClick={e => e.stopPropagation()}>
                        <h2 className="text-lg font-bold mb-2">Deactivate Mentee</h2>
                        <p className="text-sm text-text-2 mb-6">
                            Are you sure you want to deactivate <strong>{decodeHtml(confirmDeactivate.display_name)}</strong>?
                            They will not be able to book sessions while deactivated.
                        </p>
                        <div className="flex gap-2">
                            <button
                                onClick={() => toggleActive(confirmDeactivate)}
                                disabled={actionLoadingId === confirmDeactivate.id}
                                className="btn flex-1"
                                style={{ background: 'var(--err)', color: '#fff', border: 'none' }}
                            >
                                {actionLoadingId === confirmDeactivate.id ? 'Processing...' : 'Yes, Deactivate'}
                            </button>
                            <button onClick={() => setConfirmDeactivate(null)} className="btn btn-outline">Cancel</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
