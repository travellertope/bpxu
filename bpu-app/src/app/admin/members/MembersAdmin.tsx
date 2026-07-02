'use client';

import { useState, useEffect, useCallback } from 'react';

interface Member {
    id: number;
    display_name: string;
    email: string;
    avatar_url?: string;
    is_pro: boolean;
    registered: string;
}

type Filter = 'all' | 'pro' | 'free';

export default function MembersAdmin() {
    const [members, setMembers] = useState<Member[]>([]);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [search, setSearch] = useState('');
    const [filter, setFilter] = useState<Filter>('all');
    const [page, setPage] = useState(1);
    const [actionId, setActionId] = useState<number | null>(null);
    const [confirm, setConfirm] = useState<{ member: Member; action: 'grant' | 'revoke' } | null>(null);
    const perPage = 20;

    const fetchMembers = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const params = new URLSearchParams({
                page: String(page),
                per_page: String(perPage),
                filter,
            });
            if (search) params.set('search', search);

            const res = await fetch(`/api/paired/admin/members?${params}`);
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load members.');
            setMembers(data.members || []);
            setTotal(data.total || 0);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load members.');
        } finally {
            setLoading(false);
        }
    }, [page, search, filter]);

    useEffect(() => {
        fetchMembers();
    }, [fetchMembers]);

    function handleSearch(e: React.FormEvent) {
        e.preventDefault();
        setPage(1);
        setSearch(searchInput.trim());
    }

    function handleFilter(f: Filter) {
        setFilter(f);
        setPage(1);
    }

    async function togglePro(member: Member, action: 'grant' | 'revoke') {
        setActionId(member.id);
        setConfirm(null);
        try {
            const res = await fetch(`/api/paired/admin/members/${member.id}/${action}-pro`, {
                method: 'POST',
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Action failed.');
            setMembers(prev =>
                prev.map(m => m.id === member.id ? { ...m, is_pro: data.is_pro } : m)
            );
        } catch (e) {
            alert(e instanceof Error ? e.message : 'Action failed.');
        } finally {
            setActionId(null);
        }
    }

    const totalPages = Math.ceil(total / perPage);

    return (
        <div className="space-y-5">
            {/* Summary cards */}
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <button
                    onClick={() => handleFilter('all')}
                    className={`card card-p text-center transition-all ${filter === 'all' ? 'ring-2' : ''}`}
                    style={filter === 'all' ? { ringColor: 'var(--purple)' } : {}}
                >
                    <p className="text-xs text-text-3 mb-1">All Members</p>
                    <p className="text-2xl font-bold">{filter === 'all' ? total : '—'}</p>
                </button>
                <button
                    onClick={() => handleFilter('pro')}
                    className={`card card-p text-center transition-all ${filter === 'pro' ? 'ring-2' : ''}`}
                >
                    <p className="text-xs text-text-3 mb-1">Pro Members</p>
                    <p className="text-2xl font-bold" style={{ color: 'var(--purple)' }}>{filter === 'pro' ? total : '—'}</p>
                </button>
                <button
                    onClick={() => handleFilter('free')}
                    className={`card card-p text-center transition-all ${filter === 'free' ? 'ring-2' : ''}`}
                >
                    <p className="text-xs text-text-3 mb-1">Free Members</p>
                    <p className="text-2xl font-bold">{filter === 'free' ? total : '—'}</p>
                </button>
            </div>

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
                    <button
                        type="button"
                        className="btn btn-ghost btn-sm"
                        onClick={() => { setSearch(''); setSearchInput(''); setPage(1); }}
                    >
                        Clear
                    </button>
                )}
            </form>

            {loading ? (
                <div className="text-center text-sm text-text-2 py-12">Loading members...</div>
            ) : error ? (
                <div className="card card-p text-center text-sm py-10" style={{ color: 'var(--err)' }}>{error}</div>
            ) : members.length === 0 ? (
                <div className="card card-p text-center py-10">
                    <p className="font-semibold text-text-2">No members found</p>
                    <p className="text-sm text-text-3 mt-1">
                        {search ? 'Try adjusting your search.' : 'No members match this filter.'}
                    </p>
                </div>
            ) : (
                <>
                    <p className="text-sm text-text-3">{total} member{total !== 1 ? 's' : ''}</p>

                    {/* Desktop table */}
                    <div className="card hidden md:block" style={{ overflow: 'hidden' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid var(--border)', background: 'var(--surface)' }}>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Member</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Email</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Joined</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Status</th>
                                    <th className="text-right text-xs font-semibold text-text-3 p-3">Pro Access</th>
                                </tr>
                            </thead>
                            <tbody>
                                {members.map(m => (
                                    <tr key={m.id} style={{ borderBottom: '1px solid var(--border)' }}>
                                        <td className="p-3">
                                            <div className="flex items-center gap-2">
                                                {m.avatar_url ? (
                                                    <img src={m.avatar_url} alt="" className="rounded-full object-cover" style={{ width: 28, height: 28 }} />
                                                ) : (
                                                    <div className="avatar text-white" style={{ background: '#7c3aed', width: 28, height: 28, fontSize: '0.7rem' }}>
                                                        {m.display_name?.[0]?.toUpperCase() || '?'}
                                                    </div>
                                                )}
                                                <span className="text-sm font-semibold">{m.display_name}</span>
                                            </div>
                                        </td>
                                        <td className="p-3 text-sm text-text-2">{m.email}</td>
                                        <td className="p-3 text-sm text-text-3">
                                            {new Date(m.registered).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </td>
                                        <td className="p-3 text-center">
                                            <span className={`badge ${m.is_pro ? 'badge-purple' : 'badge-grey'}`}>
                                                {m.is_pro ? 'Pro' : 'Free'}
                                            </span>
                                        </td>
                                        <td className="p-3 text-right">
                                            {m.is_pro ? (
                                                <button
                                                    onClick={() => setConfirm({ member: m, action: 'revoke' })}
                                                    disabled={actionId === m.id}
                                                    className="btn btn-ghost btn-sm text-xs"
                                                    style={{ color: 'var(--err)' }}
                                                >
                                                    {actionId === m.id ? '...' : 'Revoke Pro'}
                                                </button>
                                            ) : (
                                                <button
                                                    onClick={() => setConfirm({ member: m, action: 'grant' })}
                                                    disabled={actionId === m.id}
                                                    className="btn btn-purple btn-sm text-xs"
                                                >
                                                    {actionId === m.id ? '...' : 'Grant Pro'}
                                                </button>
                                            )}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Mobile cards */}
                    <div className="md:hidden space-y-3">
                        {members.map(m => (
                            <div key={m.id} className="card card-p">
                                <div className="flex items-center gap-3 mb-3">
                                    {m.avatar_url ? (
                                        <img src={m.avatar_url} alt="" className="rounded-full object-cover" style={{ width: 40, height: 40 }} />
                                    ) : (
                                        <div className="avatar text-white" style={{ background: '#7c3aed', width: 40, height: 40, fontSize: '0.875rem' }}>
                                            {m.display_name?.[0]?.toUpperCase() || '?'}
                                        </div>
                                    )}
                                    <div className="flex-1 min-w-0">
                                        <p className="text-sm font-semibold truncate">{m.display_name}</p>
                                        <p className="text-xs text-text-2 truncate">{m.email}</p>
                                    </div>
                                    <span className={`badge ${m.is_pro ? 'badge-purple' : 'badge-grey'}`}>
                                        {m.is_pro ? 'Pro' : 'Free'}
                                    </span>
                                </div>
                                <div className="flex items-center justify-between">
                                    <span className="text-xs text-text-3">
                                        Joined {new Date(m.registered).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })}
                                    </span>
                                    {m.is_pro ? (
                                        <button
                                            onClick={() => setConfirm({ member: m, action: 'revoke' })}
                                            disabled={actionId === m.id}
                                            className="btn btn-ghost btn-sm text-xs"
                                            style={{ color: 'var(--err)' }}
                                        >
                                            {actionId === m.id ? '...' : 'Revoke Pro'}
                                        </button>
                                    ) : (
                                        <button
                                            onClick={() => setConfirm({ member: m, action: 'grant' })}
                                            disabled={actionId === m.id}
                                            className="btn btn-purple btn-sm text-xs"
                                        >
                                            {actionId === m.id ? '...' : 'Grant Pro'}
                                        </button>
                                    )}
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Pagination */}
                    {totalPages > 1 && (
                        <div className="flex items-center justify-center gap-2 pt-4">
                            <button
                                onClick={() => setPage(p => Math.max(1, p - 1))}
                                disabled={page === 1}
                                className="btn btn-outline btn-sm"
                            >
                                Previous
                            </button>
                            <span className="text-sm text-text-2">Page {page} of {totalPages}</span>
                            <button
                                onClick={() => setPage(p => Math.min(totalPages, p + 1))}
                                disabled={page === totalPages}
                                className="btn btn-outline btn-sm"
                            >
                                Next
                            </button>
                        </div>
                    )}
                </>
            )}

            {/* Confirm modal */}
            {confirm && (
                <div
                    style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 100, padding: 16 }}
                    onClick={() => setConfirm(null)}
                >
                    <div
                        className="card card-p"
                        style={{ width: '100%', maxWidth: 400 }}
                        onClick={e => e.stopPropagation()}
                    >
                        <h2 className="text-lg font-bold mb-2">
                            {confirm.action === 'grant' ? 'Grant Pro Access' : 'Revoke Pro Access'}
                        </h2>
                        <p className="text-sm text-text-2 mb-6">
                            {confirm.action === 'grant'
                                ? <>Grant Pro access to <strong>{confirm.member.display_name}</strong>? They will immediately unlock all Pro features.</>
                                : <>Revoke Pro access from <strong>{confirm.member.display_name}</strong>? They will lose access to all Pro features immediately.</>
                            }
                        </p>
                        <div className="flex gap-2">
                            <button
                                onClick={() => togglePro(confirm.member, confirm.action)}
                                className="btn flex-1"
                                style={confirm.action === 'grant'
                                    ? { background: 'var(--purple)', color: '#fff', border: 'none' }
                                    : { background: 'var(--err)', color: '#fff', border: 'none' }
                                }
                            >
                                {confirm.action === 'grant' ? 'Yes, Grant Pro' : 'Yes, Revoke Pro'}
                            </button>
                            <button onClick={() => setConfirm(null)} className="btn btn-outline">Cancel</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
