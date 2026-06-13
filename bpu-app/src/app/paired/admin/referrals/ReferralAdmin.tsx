'use client';

import { useState, useEffect } from 'react';

interface AdminReferral {
    id: number;
    referrer_name: string;
    referee_name: string;
    code: string;
    created_at: string;
    status: string;
}

interface ReferralAdminData {
    total: number;
    referrals: AdminReferral[];
}

export default function ReferralAdmin() {
    const [data, setData] = useState<ReferralAdminData>({ total: 0, referrals: [] });
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    useEffect(() => {
        async function load() {
            try {
                const res = await fetch('/api/paired/admin/referrals');
                const json = await res.json();
                if (!res.ok) throw new Error(json.error || 'Failed to load referrals.');
                setData({
                    total: json.total || 0,
                    referrals: json.referrals || [],
                });
            } catch (e) {
                setError(e instanceof Error ? e.message : 'Failed to load referrals.');
            } finally {
                setLoading(false);
            }
        }
        load();
    }, []);

    if (loading) {
        return (
            <div className="fade-up">
                <div className="text-center text-sm py-12" style={{ color: 'var(--text-2)' }}>
                    Loading referrals...
                </div>
            </div>
        );
    }

    return (
        <div className="fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px' }}>
            <div>
                <h1 className="text-3xl font-extrabold tracking-tight">Referral Management</h1>
                <p className="mt-2" style={{ color: 'var(--text-2)' }}>
                    View and manage all referrals across the platform.
                </p>
            </div>

            {error && <div className="alert alert-red">{error}</div>}

            {/* Stats summary */}
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit, minmax(180px, 1fr))', gap: '16px' }}>
                <div className="card card-p text-center">
                    <p className="stat-val">{data.total}</p>
                    <p className="stat-label">Total Referrals</p>
                </div>
            </div>

            {/* Referrals table */}
            <div className="card card-p">
                {data.referrals.length === 0 ? (
                    <div className="text-center py-10" style={{ display: 'flex', flexDirection: 'column', gap: '8px', alignItems: 'center' }}>
                        <p className="font-semibold" style={{ color: 'var(--text-2)' }}>No referrals yet</p>
                        <p className="text-sm" style={{ color: 'var(--text-3)' }}>
                            Referrals will appear here once users start sharing their codes.
                        </p>
                    </div>
                ) : (
                    <div style={{ overflowX: 'auto' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid var(--border)' }}>
                                    <th className="text-left text-xs font-medium py-3 px-2" style={{ color: 'var(--text-3)' }}>Referrer</th>
                                    <th className="text-left text-xs font-medium py-3 px-2" style={{ color: 'var(--text-3)' }}>Referee</th>
                                    <th className="text-left text-xs font-medium py-3 px-2" style={{ color: 'var(--text-3)' }}>Code</th>
                                    <th className="text-left text-xs font-medium py-3 px-2" style={{ color: 'var(--text-3)' }}>Date</th>
                                    <th className="text-left text-xs font-medium py-3 px-2" style={{ color: 'var(--text-3)' }}>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                {data.referrals.map(ref => (
                                    <tr key={ref.id} style={{ borderBottom: '1px solid var(--border)' }}>
                                        <td className="py-3 px-2 text-sm font-medium">{ref.referrer_name}</td>
                                        <td className="py-3 px-2 text-sm">{ref.referee_name}</td>
                                        <td className="py-3 px-2">
                                            <span
                                                className="font-mono text-xs px-2 py-1 rounded"
                                                style={{ background: 'var(--bg)', border: '1px solid var(--border)' }}
                                            >
                                                {ref.code}
                                            </span>
                                        </td>
                                        <td className="py-3 px-2 text-sm" style={{ color: 'var(--text-2)' }}>
                                            {new Date(ref.created_at).toLocaleDateString('en-GB', {
                                                day: 'numeric', month: 'short', year: 'numeric',
                                            })}
                                        </td>
                                        <td className="py-3 px-2">
                                            <span className={`badge ${
                                                ref.status === 'active' ? 'badge-green' :
                                                ref.status === 'pending' ? 'badge-amber' : 'badge-purple'
                                            }`}>
                                                {ref.status}
                                            </span>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                )}
            </div>
        </div>
    );
}
