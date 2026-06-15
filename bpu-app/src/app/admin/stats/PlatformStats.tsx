'use client';

import { decodeHtml } from '@/lib/utils';

interface TopMentor {
    id: number;
    display_name: string;
    avatar_url?: string;
    booking_count: number;
    rating?: number;
}

interface StatsData {
    total_mentors?: number;
    total_mentees?: number;
    total_bookings?: number;
    completion_rate?: number;
    bookings_by_status?: {
        pending?: number;
        confirmed?: number;
        completed?: number;
        cancelled?: number;
    };
    bookings_this_month?: number;
    new_mentors_this_month?: number;
    top_mentors?: TopMentor[];
    average_rating?: number;
}

interface Props {
    initialStats: StatsData;
}

function StatCard({ label, value, color }: { label: string; value: string | number; color?: string }) {
    return (
        <div className="card card-p text-center">
            <p className="text-3xl font-extrabold" style={{ color: color || 'var(--text)' }}>{value}</p>
            <p className="text-sm text-text-3 mt-1">{label}</p>
        </div>
    );
}

function initialsColor(id: number): string {
    const colors = ['#6366f1', '#8b5cf6', '#ec4899', '#3b82f6', '#14b8a6', '#f59e0b', '#ef4444'];
    return colors[id % colors.length];
}

export default function PlatformStats({ initialStats }: Props) {
    const stats = initialStats;
    const byStatus = stats.bookings_by_status || {};

    return (
        <div className="space-y-8">
            {/* Main Stats Row */}
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <StatCard label="Total Mentors" value={stats.total_mentors ?? 0} color="var(--purple)" />
                <StatCard label="Total Mentees" value={stats.total_mentees ?? 0} color="var(--brand)" />
                <StatCard label="Total Bookings" value={stats.total_bookings ?? 0} />
                <StatCard
                    label="Completion Rate"
                    value={stats.completion_rate != null ? `${Math.round(stats.completion_rate)}%` : '-'}
                    color="var(--ok)"
                />
            </div>

            {/* Bookings by Status */}
            <div>
                <h2 className="text-lg font-bold mb-4">Bookings by Status</h2>
                <div className="grid grid-cols-2 lg:grid-cols-4 gap-4">
                    <div className="card card-p flex items-center gap-3">
                        <span className="badge badge-amber" style={{ fontSize: '1rem', padding: '6px 12px' }}>
                            {byStatus.pending ?? 0}
                        </span>
                        <span className="text-sm text-text-2">Pending</span>
                    </div>
                    <div className="card card-p flex items-center gap-3">
                        <span className="badge badge-purple" style={{ fontSize: '1rem', padding: '6px 12px' }}>
                            {byStatus.confirmed ?? 0}
                        </span>
                        <span className="text-sm text-text-2">Confirmed</span>
                    </div>
                    <div className="card card-p flex items-center gap-3">
                        <span className="badge badge-green" style={{ fontSize: '1rem', padding: '6px 12px' }}>
                            {byStatus.completed ?? 0}
                        </span>
                        <span className="text-sm text-text-2">Completed</span>
                    </div>
                    <div className="card card-p flex items-center gap-3">
                        <span
                            className="badge"
                            style={{ fontSize: '1rem', padding: '6px 12px', background: 'var(--err)', color: '#fff' }}
                        >
                            {byStatus.cancelled ?? 0}
                        </span>
                        <span className="text-sm text-text-2">Cancelled</span>
                    </div>
                </div>
            </div>

            {/* Monthly Stats + Average Rating */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div className="card card-p text-center">
                    <p className="text-2xl font-bold">{stats.bookings_this_month ?? 0}</p>
                    <p className="text-sm text-text-3 mt-1">Bookings This Month</p>
                </div>
                <div className="card card-p text-center">
                    <p className="text-2xl font-bold">{stats.new_mentors_this_month ?? 0}</p>
                    <p className="text-sm text-text-3 mt-1">New Mentors This Month</p>
                </div>
                <div className="card card-p text-center">
                    <div className="flex items-center justify-center gap-2">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" strokeWidth="2">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                        </svg>
                        <p className="text-2xl font-bold">
                            {stats.average_rating != null && stats.average_rating > 0
                                ? stats.average_rating.toFixed(1)
                                : '-'}
                        </p>
                    </div>
                    <p className="text-sm text-text-3 mt-1">Average Platform Rating</p>
                </div>
            </div>

            {/* Top Mentors */}
            {stats.top_mentors && stats.top_mentors.length > 0 && (
                <div>
                    <h2 className="text-lg font-bold mb-4">Top Mentors</h2>
                    <div className="card" style={{ overflow: 'hidden' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid var(--border)', background: 'var(--surface)' }}>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">#</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Mentor</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Bookings</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Rating</th>
                                </tr>
                            </thead>
                            <tbody>
                                {stats.top_mentors.map((m, i) => (
                                    <tr key={m.id} style={{ borderBottom: '1px solid var(--border)' }}>
                                        <td className="p-3 text-sm text-text-3 font-mono">{i + 1}</td>
                                        <td className="p-3">
                                            <div className="flex items-center gap-3">
                                                {m.avatar_url ? (
                                                    <img src={m.avatar_url} alt="" className="rounded-full object-cover" style={{ width: 32, height: 32 }} />
                                                ) : (
                                                    <div
                                                        className="avatar text-white"
                                                        style={{
                                                            background: initialsColor(m.id),
                                                            width: 32,
                                                            height: 32,
                                                            fontSize: '0.75rem',
                                                        }}
                                                    >
                                                        {decodeHtml(m.display_name)?.[0] || '?'}
                                                    </div>
                                                )}
                                                <a
                                                    href={`/paired/mentors/${m.id}`}
                                                    className="text-sm font-semibold hover:underline"
                                                >
                                                    {decodeHtml(m.display_name)}
                                                </a>
                                            </div>
                                        </td>
                                        <td className="p-3 text-sm text-text-2 text-center font-semibold">
                                            {m.booking_count}
                                        </td>
                                        <td className="p-3 text-sm text-text-2 text-center">
                                            {m.rating != null && m.rating > 0 ? (
                                                <span className="flex items-center justify-center gap-1">
                                                    <svg width="12" height="12" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" strokeWidth="2">
                                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                                    </svg>
                                                    {m.rating.toFixed(1)}
                                                </span>
                                            ) : '-'}
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
