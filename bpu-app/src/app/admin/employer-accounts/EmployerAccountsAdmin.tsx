'use client';

import { useState, useEffect, useCallback, useRef } from 'react';

interface LinkedUser {
    id: number;
    display_name: string;
    email: string;
    avatar_url: string;
}

interface EmployerAccount {
    id: number;
    name: string;
    logo_url: string;
    job_count: number;
    linked_users: LinkedUser[];
}

interface SearchUser {
    id: number;
    display_name: string;
    email: string;
    avatar_url: string;
}

export default function EmployerAccountsAdmin() {
    const [accounts, setAccounts] = useState<EmployerAccount[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    // Per-term search state
    const [searchQuery, setSearchQuery] = useState<Record<number, string>>({});
    const [searchResults, setSearchResults] = useState<Record<number, SearchUser[]>>({});
    const [searchLoading, setSearchLoading] = useState<Record<number, boolean>>({});
    const [showDropdown, setShowDropdown] = useState<Record<number, boolean>>({});
    const [actionError, setActionError] = useState<Record<number, string>>({});

    const debounceTimers = useRef<Record<number, ReturnType<typeof setTimeout>>>({});

    const fetchAccounts = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const res = await fetch('/api/paired/admin/employer-accounts');
            if (!res.ok) throw new Error('Failed to load employer accounts');
            const data = await res.json();
            setAccounts(data.accounts || []);
        } catch (err: unknown) {
            setError(err instanceof Error ? err.message : 'Failed to load employer accounts');
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => { fetchAccounts(); }, [fetchAccounts]);

    const searchUsers = useCallback(async (termId: number, q: string) => {
        if (!q.trim()) {
            setSearchResults(r => ({ ...r, [termId]: [] }));
            setShowDropdown(d => ({ ...d, [termId]: false }));
            return;
        }
        setSearchLoading(l => ({ ...l, [termId]: true }));
        try {
            const res = await fetch(`/api/paired/admin/user-search?q=${encodeURIComponent(q)}`);
            const data = await res.json();
            setSearchResults(r => ({ ...r, [termId]: data.users || [] }));
            setShowDropdown(d => ({ ...d, [termId]: true }));
        } catch {
            setSearchResults(r => ({ ...r, [termId]: [] }));
        } finally {
            setSearchLoading(l => ({ ...l, [termId]: false }));
        }
    }, []);

    const handleSearchChange = (termId: number, value: string) => {
        setSearchQuery(q => ({ ...q, [termId]: value }));
        clearTimeout(debounceTimers.current[termId]);
        debounceTimers.current[termId] = setTimeout(() => {
            searchUsers(termId, value);
        }, 300);
    };

    const handleLink = async (termId: number, userId: number) => {
        setActionError(e => ({ ...e, [termId]: '' }));
        try {
            const res = await fetch('/api/paired/admin/employer-accounts/link', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ term_id: termId, user_id: userId }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Failed to link user');
            setSearchQuery(q => ({ ...q, [termId]: '' }));
            setSearchResults(r => ({ ...r, [termId]: [] }));
            setShowDropdown(d => ({ ...d, [termId]: false }));
            await fetchAccounts();
        } catch (err: unknown) {
            setActionError(e => ({ ...e, [termId]: err instanceof Error ? err.message : 'Failed to link user' }));
        }
    };

    const handleUnlink = async (termId: number, userId: number) => {
        setActionError(e => ({ ...e, [termId]: '' }));
        try {
            const res = await fetch('/api/paired/admin/employer-accounts/unlink', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.message || 'Failed to unlink user');
            await fetchAccounts();
        } catch (err: unknown) {
            setActionError(e => ({ ...e, [termId]: err instanceof Error ? err.message : 'Failed to unlink user' }));
        }
    };

    if (loading) {
        return (
            <div className="card card-p text-sm text-text-2">Loading employer accounts...</div>
        );
    }

    if (error) {
        return (
            <div className="card card-p" style={{ borderLeft: '4px solid #ef4444', background: 'rgba(239,68,68,0.08)' }}>
                <p className="text-sm" style={{ color: '#ef4444' }}>{error}</p>
            </div>
        );
    }

    if (accounts.length === 0) {
        return (
            <div className="card card-p text-sm text-text-2">No employer taxonomy terms found. Create employer terms in WordPress first.</div>
        );
    }

    return (
        <div className="space-y-6">
            {accounts.map(account => (
                <div key={account.id} className="card card-p space-y-4">
                    {/* Employer header */}
                    <div className="flex items-center gap-3">
                        {account.logo_url ? (
                            <img
                                src={account.logo_url}
                                alt=""
                                className="w-10 h-10 rounded object-contain"
                                style={{ background: '#fff', border: '1px solid var(--border)' }}
                            />
                        ) : (
                            <span
                                className="w-10 h-10 rounded flex items-center justify-center text-base font-bold"
                                style={{ background: 'var(--surface-2)', color: 'var(--text-3)' }}
                            >
                                {account.name.charAt(0).toUpperCase()}
                            </span>
                        )}
                        <div className="flex-1 min-w-0">
                            <p className="font-semibold">{account.name}</p>
                            <p className="text-xs text-text-3">{account.job_count} job{account.job_count !== 1 ? 's' : ''} posted</p>
                        </div>
                    </div>

                    {/* Linked users */}
                    <div>
                        <p className="text-xs font-bold uppercase tracking-wide text-text-3 mb-2">Linked users</p>
                        {account.linked_users.length === 0 ? (
                            <p className="text-sm text-text-2">No users linked to this employer yet.</p>
                        ) : (
                            <div className="space-y-2">
                                {account.linked_users.map(user => (
                                    <div
                                        key={user.id}
                                        className="flex items-center gap-3 p-2 rounded-lg"
                                        style={{ background: 'var(--surface-2)' }}
                                    >
                                        <img
                                            src={user.avatar_url}
                                            alt=""
                                            className="w-8 h-8 rounded-full"
                                        />
                                        <div className="flex-1 min-w-0">
                                            <p className="text-sm font-medium">{user.display_name}</p>
                                            <p className="text-xs text-text-3 truncate">{user.email}</p>
                                        </div>
                                        <button
                                            type="button"
                                            className="btn btn-ghost btn-sm text-xs"
                                            onClick={() => handleUnlink(account.id, user.id)}
                                        >
                                            Unlink
                                        </button>
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>

                    {/* Link new user */}
                    <div>
                        <p className="text-xs font-bold uppercase tracking-wide text-text-3 mb-2">Link a user</p>
                        <div className="relative">
                            <input
                                className="field-input"
                                placeholder="Search by name or email..."
                                value={searchQuery[account.id] || ''}
                                onChange={e => handleSearchChange(account.id, e.target.value)}
                                onFocus={() => {
                                    if ((searchResults[account.id] || []).length > 0) {
                                        setShowDropdown(d => ({ ...d, [account.id]: true }));
                                    }
                                }}
                            />
                            {searchLoading[account.id] && (
                                <p className="text-xs text-text-3 mt-1">Searching...</p>
                            )}
                            {showDropdown[account.id] && (searchResults[account.id] || []).length > 0 && (
                                <div
                                    className="absolute z-10 left-0 right-0 mt-1 max-h-48 overflow-y-auto rounded-lg shadow-lg"
                                    style={{ background: 'var(--surface)', border: '1px solid var(--border)' }}
                                >
                                    {(searchResults[account.id] || []).map(user => (
                                        <button
                                            key={user.id}
                                            type="button"
                                            className="w-full text-left px-4 py-2 flex items-center gap-3 transition-colors"
                                            style={{ background: 'transparent' }}
                                            onMouseEnter={e => (e.currentTarget.style.background = 'var(--surface-2)')}
                                            onMouseLeave={e => (e.currentTarget.style.background = 'transparent')}
                                            onClick={() => handleLink(account.id, user.id)}
                                        >
                                            <img src={user.avatar_url} alt="" className="w-6 h-6 rounded-full" />
                                            <span className="text-sm">{user.display_name}</span>
                                            <span className="text-xs text-text-3 ml-auto">{user.email}</span>
                                        </button>
                                    ))}
                                </div>
                            )}
                            {showDropdown[account.id] && (searchResults[account.id] || []).length === 0 && !searchLoading[account.id] && (searchQuery[account.id] || '').trim() && (
                                <div
                                    className="absolute z-10 left-0 right-0 mt-1 rounded-lg shadow-lg px-4 py-3"
                                    style={{ background: 'var(--surface)', border: '1px solid var(--border)' }}
                                >
                                    <p className="text-sm text-text-3">No users found</p>
                                </div>
                            )}
                        </div>
                    </div>

                    {actionError[account.id] && (
                        <p className="text-xs" style={{ color: '#ef4444' }}>{actionError[account.id]}</p>
                    )}
                </div>
            ))}
        </div>
    );
}
