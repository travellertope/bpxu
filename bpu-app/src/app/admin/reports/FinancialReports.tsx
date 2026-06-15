'use client';

import { useState, useEffect } from 'react';
import { decodeHtml } from '@/lib/utils';

interface MonthlyRevenue {
    month: string;
    total: number;
    count: number;
}

interface MentorRevenue {
    mentor_id: number;
    mentor_name: string;
    total: number;
    booking_count: number;
}

interface ReportsData {
    total_revenue?: number;
    total_paid_bookings?: number;
    average_booking_value?: number;
    total_refunded?: number;
    monthly_revenue?: MonthlyRevenue[];
    revenue_by_mentor?: MentorRevenue[];
}

function formatCurrency(amount: number): string {
    return new Intl.NumberFormat('en-GB', { style: 'currency', currency: 'GBP' }).format(amount);
}

export default function FinancialReports() {
    const [data, setData] = useState<ReportsData>({});
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    useEffect(() => {
        async function load() {
            try {
                const res = await fetch('/api/admin/reports');
                const json = await res.json();
                if (!res.ok) throw new Error(json.error || 'Failed to load reports.');
                setData(json);
            } catch (e) {
                setError(e instanceof Error ? e.message : 'Failed to load reports.');
            } finally {
                setLoading(false);
            }
        }
        load();
    }, []);

    if (loading) return <div className="text-center text-sm text-text-2 py-12">Loading reports...</div>;
    if (error) return <div className="card card-p text-center py-10" style={{ color: 'var(--err)' }}>{error}</div>;

    const monthly = data.monthly_revenue || [];
    const byMentor = data.revenue_by_mentor || [];
    const maxMonthly = Math.max(...monthly.map(m => m.total), 1);

    return (
        <div className="space-y-8">
            {/* Summary Cards */}
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div className="card card-p text-center">
                    <p className="text-3xl font-extrabold" style={{ color: 'var(--ok)' }}>{formatCurrency(data.total_revenue ?? 0)}</p>
                    <p className="text-sm text-text-3 mt-1">Total Revenue</p>
                </div>
                <div className="card card-p text-center">
                    <p className="text-3xl font-extrabold" style={{ color: 'var(--purple)' }}>{data.total_paid_bookings ?? 0}</p>
                    <p className="text-sm text-text-3 mt-1">Paid Bookings</p>
                </div>
                <div className="card card-p text-center">
                    <p className="text-3xl font-extrabold">{formatCurrency(data.average_booking_value ?? 0)}</p>
                    <p className="text-sm text-text-3 mt-1">Avg. Booking Value</p>
                </div>
                <div className="card card-p text-center">
                    <p className="text-3xl font-extrabold" style={{ color: 'var(--err)' }}>{formatCurrency(data.total_refunded ?? 0)}</p>
                    <p className="text-sm text-text-3 mt-1">Total Refunded</p>
                </div>
            </div>

            {/* Monthly Revenue Chart (bar chart using CSS) */}
            {monthly.length > 0 && (
                <div>
                    <h2 className="text-lg font-bold mb-4">Monthly Revenue</h2>
                    <div className="card card-p">
                        <div style={{ display: 'flex', alignItems: 'flex-end', gap: 6, height: 200, padding: '0 4px' }}>
                            {monthly.slice(-12).map(m => (
                                <div key={m.month} style={{ flex: 1, display: 'flex', flexDirection: 'column', alignItems: 'center', height: '100%', justifyContent: 'flex-end' }}>
                                    <span className="text-xs font-semibold mb-1" style={{ color: 'var(--ok)' }}>
                                        {formatCurrency(m.total)}
                                    </span>
                                    <div
                                        style={{
                                            width: '100%',
                                            maxWidth: 48,
                                            height: `${Math.max((m.total / maxMonthly) * 100, 4)}%`,
                                            background: 'var(--purple)',
                                            borderRadius: '6px 6px 0 0',
                                            minHeight: 4,
                                        }}
                                    />
                                    <span className="text-xs text-text-3 mt-1" style={{ fontSize: '0.65rem' }}>
                                        {m.month}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            )}

            {/* Revenue by Mentor */}
            {byMentor.length > 0 && (
                <div>
                    <h2 className="text-lg font-bold mb-4">Revenue by Mentor</h2>
                    <div className="card" style={{ overflow: 'hidden' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid var(--border)', background: 'var(--surface)' }}>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">#</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Mentor</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Bookings</th>
                                    <th className="text-right text-xs font-semibold text-text-3 p-3">Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                {byMentor.map((m, i) => (
                                    <tr key={m.mentor_id} style={{ borderBottom: '1px solid var(--border)' }}>
                                        <td className="p-3 text-sm text-text-3 font-mono">{i + 1}</td>
                                        <td className="p-3">
                                            <a href={`/paired/mentors/${m.mentor_id}`} className="text-sm font-semibold hover:underline">
                                                {decodeHtml(m.mentor_name)}
                                            </a>
                                        </td>
                                        <td className="p-3 text-sm text-text-2 text-center">{m.booking_count}</td>
                                        <td className="p-3 text-sm font-bold text-right" style={{ color: 'var(--ok)' }}>
                                            {formatCurrency(m.total)}
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </div>
            )}
        </div>
    );
}
