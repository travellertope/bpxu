'use client';

import { useState, useEffect, useCallback } from 'react';
import { decodeHtml } from '@/lib/utils';

interface Transaction {
    booking_id: number;
    mentor_name: string;
    mentee_name: string;
    amount: number;
    payment_status: string;
    date: string;
    stripe_payment_id?: string;
}

const STATUS_BADGE: Record<string, string> = {
    paid: 'badge-green',
    pending: 'badge-amber',
    refunded: 'badge-red',
    not_required: 'badge-purple',
};

function formatCurrency(amount: number): string {
    return new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP' }).format(amount);
}

export default function TransactionsAdmin() {
    const [transactions, setTransactions] = useState<Transaction[]>([]);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [statusFilter, setStatusFilter] = useState('all');
    const [page, setPage] = useState(1);
    const perPage = 20;

    const fetchTransactions = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const params = new URLSearchParams({ page: String(page), per_page: String(perPage) });
            if (statusFilter !== 'all') params.set('status', statusFilter);
            if (search.trim()) params.set('search', search.trim());

            const res = await fetch(`/api/paired/admin/transactions?${params}`);
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load transactions.');
            setTransactions(data.transactions || []);
            setTotal(data.total || 0);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load transactions.');
        } finally {
            setLoading(false);
        }
    }, [page, statusFilter, search]);

    useEffect(() => {
        fetchTransactions();
    }, [fetchTransactions]);

    function handleSearch(e: React.FormEvent) {
        e.preventDefault();
        setSearch(searchInput);
        setPage(1);
    }

    const totalPages = Math.ceil(total / perPage);
    const totalAmount = transactions.reduce((s, t) => s + (t.payment_status === 'paid' ? t.amount : 0), 0);

    return (
        <div className="space-y-5">
            {/* Summary */}
            {!loading && !error && transactions.length > 0 && (
                <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div className="card card-p text-center">
                        <p className="text-2xl font-bold">{total}</p>
                        <p className="text-sm text-text-3 mt-1">Total Transactions</p>
                    </div>
                    <div className="card card-p text-center">
                        <p className="text-2xl font-bold" style={{ color: 'var(--ok)' }}>{formatCurrency(totalAmount)}</p>
                        <p className="text-sm text-text-3 mt-1">Page Revenue (Paid)</p>
                    </div>
                    <div className="card card-p text-center col-span-2 md:col-span-1">
                        <p className="text-2xl font-bold" style={{ color: 'var(--purple)' }}>
                            {transactions.filter(t => t.payment_status === 'paid').length}
                        </p>
                        <p className="text-sm text-text-3 mt-1">Paid on Page</p>
                    </div>
                </div>
            )}

            {/* Filters */}
            <div className="flex flex-col sm:flex-row gap-3">
                <div style={{ display: 'flex', gap: 4, background: 'var(--surface)', padding: 4, borderRadius: 10, flexWrap: 'wrap' }}>
                    {['all', 'paid', 'pending', 'refunded'].map(s => (
                        <button
                            key={s}
                            onClick={() => { setStatusFilter(s); setPage(1); }}
                            className={statusFilter === s ? 'btn btn-purple btn-sm' : 'btn btn-ghost btn-sm'}
                            style={{ fontSize: '0.8rem' }}
                        >
                            {s === 'all' ? 'All' : s.charAt(0).toUpperCase() + s.slice(1)}
                        </button>
                    ))}
                </div>
                <form onSubmit={handleSearch} className="flex gap-2 flex-1">
                    <input
                        type="text"
                        className="field-input flex-1"
                        placeholder="Search by name..."
                        value={searchInput}
                        onChange={e => setSearchInput(e.target.value)}
                    />
                    <button type="submit" className="btn btn-purple btn-sm">Search</button>
                    {search && (
                        <button type="button" className="btn btn-ghost btn-sm" onClick={() => { setSearch(''); setSearchInput(''); setPage(1); }}>Clear</button>
                    )}
                </form>
            </div>

            {loading ? (
                <div className="text-center text-sm text-text-2 py-12">Loading transactions...</div>
            ) : error ? (
                <div className="card card-p text-center py-10" style={{ color: 'var(--err)' }}>{error}</div>
            ) : transactions.length === 0 ? (
                <div className="card card-p text-center py-10">
                    <p className="font-semibold text-text-2">No transactions found</p>
                    <p className="text-sm text-text-3 mt-1">Adjust your filters or search.</p>
                </div>
            ) : (
                <>
                    <p className="text-sm text-text-3">{total} transaction{total !== 1 ? 's' : ''}</p>

                    {/* Desktop Table */}
                    <div className="card hidden md:block" style={{ overflow: 'hidden' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid var(--border)', background: 'var(--surface)' }}>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">#</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Mentee</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Mentor</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Date</th>
                                    <th className="text-right text-xs font-semibold text-text-3 p-3">Amount</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Status</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Stripe ID</th>
                                </tr>
                            </thead>
                            <tbody>
                                {transactions.map(t => (
                                    <tr key={t.booking_id} style={{ borderBottom: '1px solid var(--border)' }}>
                                        <td className="p-3 text-xs text-text-3 font-mono">{t.booking_id}</td>
                                        <td className="p-3 text-sm font-medium">{decodeHtml(t.mentee_name)}</td>
                                        <td className="p-3 text-sm text-text-2">{decodeHtml(t.mentor_name)}</td>
                                        <td className="p-3 text-sm text-text-2">{t.date || '-'}</td>
                                        <td className="p-3 text-sm font-semibold text-right">{t.amount ? formatCurrency(t.amount) : '-'}</td>
                                        <td className="p-3 text-center">
                                            <span className={`badge ${STATUS_BADGE[t.payment_status] || 'badge-amber'}`}>{t.payment_status}</span>
                                        </td>
                                        <td className="p-3 text-xs text-text-3 font-mono" style={{ maxWidth: 120, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                                            {t.stripe_payment_id || '-'}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Mobile Cards */}
                    <div className="md:hidden space-y-3">
                        {transactions.map(t => (
                            <div key={t.booking_id} className="card card-p space-y-2">
                                <div className="flex items-center justify-between">
                                    <span className="text-xs font-mono text-text-3">#{t.booking_id}</span>
                                    <span className={`badge ${STATUS_BADGE[t.payment_status] || 'badge-amber'}`}>{t.payment_status}</span>
                                </div>
                                <div className="grid grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <p className="text-xs text-text-3">Mentee</p>
                                        <p className="font-medium truncate">{decodeHtml(t.mentee_name)}</p>
                                    </div>
                                    <div>
                                        <p className="text-xs text-text-3">Mentor</p>
                                        <p className="text-text-2 truncate">{decodeHtml(t.mentor_name)}</p>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between text-sm">
                                    <span className="text-text-3">{t.date}</span>
                                    <span className="font-bold">{t.amount ? formatCurrency(t.amount) : '-'}</span>
                                </div>
                            </div>
                        ))}
                    </div>

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
