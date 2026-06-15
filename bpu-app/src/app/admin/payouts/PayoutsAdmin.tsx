'use client';

import { useState, useEffect } from 'react';
import { decodeHtml } from '@/lib/utils';

interface Payout {
    booking_id: number;
    mentor_id: number;
    mentor_name: string;
    amount: number;
    payment_status: string;
    date: string;
    stripe_account?: string;
}

function formatCurrency(amount: number): string {
    return new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP' }).format(amount);
}

export default function PayoutsAdmin() {
    const [payouts, setPayouts] = useState<Payout[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [search, setSearch] = useState('');

    useEffect(() => {
        async function load() {
            try {
                const res = await fetch('/api/paired/admin/payouts');
                const data = await res.json();
                if (!res.ok) throw new Error(data.error || 'Failed to load payouts.');
                setPayouts(data.payouts || []);
            } catch (e) {
                setError(e instanceof Error ? e.message : 'Failed to load payouts.');
            } finally {
                setLoading(false);
            }
        }
        load();
    }, []);

    const filtered = search.trim()
        ? payouts.filter(p => p.mentor_name.toLowerCase().includes(search.toLowerCase()))
        : payouts;

    const totalAmount = filtered.reduce((sum, p) => sum + (p.amount || 0), 0);

    return (
        <div className="space-y-5">
            {/* Summary */}
            {!loading && !error && payouts.length > 0 && (
                <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div className="card card-p text-center">
                        <p className="text-2xl font-bold">{filtered.length}</p>
                        <p className="text-sm text-text-3 mt-1">Total Payouts</p>
                    </div>
                    <div className="card card-p text-center">
                        <p className="text-2xl font-bold" style={{ color: 'var(--ok)' }}>{formatCurrency(totalAmount)}</p>
                        <p className="text-sm text-text-3 mt-1">Total Paid Out</p>
                    </div>
                    <div className="card card-p text-center col-span-2 md:col-span-1">
                        <p className="text-2xl font-bold" style={{ color: 'var(--purple)' }}>
                            {new Set(filtered.map(p => p.mentor_id)).size}
                        </p>
                        <p className="text-sm text-text-3 mt-1">Unique Mentors</p>
                    </div>
                </div>
            )}

            {/* Search */}
            <input
                type="text"
                className="field-input"
                placeholder="Filter by mentor name..."
                value={search}
                onChange={e => setSearch(e.target.value)}
            />

            {loading ? (
                <div className="text-center text-sm text-text-2 py-12">Loading payouts...</div>
            ) : error ? (
                <div className="card card-p text-center py-10" style={{ color: 'var(--err)' }}>{error}</div>
            ) : filtered.length === 0 ? (
                <div className="card card-p text-center py-10">
                    <p className="font-semibold text-text-2">{search ? 'No payouts match your search' : 'No payouts found'}</p>
                    <p className="text-sm text-text-3 mt-1">Paid bookings will appear here.</p>
                </div>
            ) : (
                <>
                    <p className="text-sm text-text-3">{filtered.length} payout{filtered.length !== 1 ? 's' : ''}</p>

                    {/* Desktop Table */}
                    <div className="card hidden md:block" style={{ overflow: 'hidden' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid var(--border)', background: 'var(--surface)' }}>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Booking #</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Mentor</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Date</th>
                                    <th className="text-right text-xs font-semibold text-text-3 p-3">Amount</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Status</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Stripe Account</th>
                                </tr>
                            </thead>
                            <tbody>
                                {filtered.map(p => (
                                    <tr key={p.booking_id} style={{ borderBottom: '1px solid var(--border)' }}>
                                        <td className="p-3 text-xs text-text-3 font-mono">{p.booking_id}</td>
                                        <td className="p-3">
                                            <div className="flex items-center gap-2">
                                                <div className="avatar text-white" style={{ background: '#7c3aed', width: 28, height: 28, fontSize: '0.7rem' }}>
                                                    {decodeHtml(p.mentor_name)?.[0] || '?'}
                                                </div>
                                                <span className="text-sm font-semibold">{decodeHtml(p.mentor_name)}</span>
                                            </div>
                                        </td>
                                        <td className="p-3 text-sm text-text-2">{p.date || '-'}</td>
                                        <td className="p-3 text-sm font-semibold text-right" style={{ color: 'var(--ok)' }}>
                                            {p.amount ? formatCurrency(p.amount) : '-'}
                                        </td>
                                        <td className="p-3 text-center">
                                            <span className="badge badge-green">{p.payment_status}</span>
                                        </td>
                                        <td className="p-3 text-xs text-text-3 font-mono" style={{ maxWidth: 140, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>
                                            {p.stripe_account || <span className="text-text-3 italic">Not connected</span>}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Mobile Cards */}
                    <div className="md:hidden space-y-3">
                        {filtered.map(p => (
                            <div key={p.booking_id} className="card card-p space-y-2">
                                <div className="flex items-center justify-between">
                                    <span className="text-xs font-mono text-text-3">#{p.booking_id}</span>
                                    <span className="badge badge-green">{p.payment_status}</span>
                                </div>
                                <div className="flex items-center gap-2">
                                    <div className="avatar text-white" style={{ background: '#7c3aed', width: 32, height: 32, fontSize: '0.75rem' }}>
                                        {decodeHtml(p.mentor_name)?.[0] || '?'}
                                    </div>
                                    <div>
                                        <p className="text-sm font-semibold">{decodeHtml(p.mentor_name)}</p>
                                        <p className="text-xs text-text-3">{p.date}</p>
                                    </div>
                                    <p className="ml-auto text-sm font-bold" style={{ color: 'var(--ok)' }}>
                                        {p.amount ? formatCurrency(p.amount) : '-'}
                                    </p>
                                </div>
                                {p.stripe_account && (
                                    <p className="text-xs text-text-3 font-mono truncate">Stripe: {p.stripe_account}</p>
                                )}
                            </div>
                        ))}
                    </div>
                </>
            )}
        </div>
    );
}
