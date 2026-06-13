import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { cookies } from 'next/headers';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

interface Stats {
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
}

export default async function AdminDashboardPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/admin/dashboard');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    let stats: Stats = {};
    if (jwt) {
        try {
            const res = await fetch(`${WP}/wp-json/bpu/v1/paired/admin/stats`, {
                headers: { 'Authorization': `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
            });
            if (res.ok) stats = await res.json();
        } catch { /* fail silently */ }
    }

    const byStatus = stats.bookings_by_status || {};

    const adminLinks = [
        {
            href: '/paired/admin/bookings',
            icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>,
            label: 'Booking Management',
            desc: 'View and update the status of all bookings.',
            color: '#7c3aed',
        },
        {
            href: '/paired/admin/mentees',
            icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>,
            label: 'Mentee Management',
            desc: 'Search, activate or deactivate mentee accounts.',
            color: '#0ea5e9',
        },
        {
            href: '/paired/admin/payouts',
            icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>,
            label: 'Payout Management',
            desc: 'Review completed session payouts for mentors.',
            color: '#10b981',
        },
        {
            href: '/paired/admin/mentors',
            icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>,
            label: 'Mentor Management',
            desc: 'Edit mentor profiles and manage their status.',
            color: '#f59e0b',
        },
        {
            href: '/paired/admin/applications',
            icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>,
            label: 'Applications',
            desc: 'Review and approve mentor applications.',
            color: '#6366f1',
        },
        {
            href: '/paired/admin/kyc',
            icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>,
            label: 'KYC Verification',
            desc: 'Manage identity verification documents.',
            color: '#ef4444',
        },
        {
            href: '/paired/admin/stats',
            icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>,
            label: 'Analytics',
            desc: 'Deep-dive platform statistics and top mentors.',
            color: '#8b5cf6',
        },
        {
            href: '/paired/admin/referrals',
            icon: <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>,
            label: 'Referrals',
            desc: 'Track referral codes and their performance.',
            color: '#14b8a6',
        },
    ];

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-1">Admin Dashboard</h1>
            <p className="text-text-2 mb-8">Platform overview and quick access to all admin tools.</p>

            {/* Stat Cards */}
            <div className="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
                <div className="card card-p text-center">
                    <p className="text-3xl font-extrabold" style={{ color: 'var(--purple)' }}>{stats.total_mentors ?? 0}</p>
                    <p className="text-sm text-text-3 mt-1">Total Mentors</p>
                </div>
                <div className="card card-p text-center">
                    <p className="text-3xl font-extrabold" style={{ color: '#0ea5e9' }}>{stats.total_mentees ?? 0}</p>
                    <p className="text-sm text-text-3 mt-1">Total Mentees</p>
                </div>
                <div className="card card-p text-center">
                    <p className="text-3xl font-extrabold">{stats.total_bookings ?? 0}</p>
                    <p className="text-sm text-text-3 mt-1">Total Bookings</p>
                </div>
                <div className="card card-p text-center">
                    <p className="text-3xl font-extrabold" style={{ color: 'var(--ok)' }}>{stats.bookings_this_month ?? 0}</p>
                    <p className="text-sm text-text-3 mt-1">Bookings This Month</p>
                </div>
            </div>

            {/* Booking Status Snapshot */}
            {stats.bookings_by_status && (
                <div className="mb-10">
                    <h2 className="text-base font-semibold mb-3 text-text-2">Booking Status Snapshot</h2>
                    <div className="grid grid-cols-2 md:grid-cols-4 gap-3">
                        {[
                            { label: 'Pending', value: byStatus.pending ?? 0, cls: 'badge-amber' },
                            { label: 'Confirmed', value: byStatus.confirmed ?? 0, cls: 'badge-purple' },
                            { label: 'Completed', value: byStatus.completed ?? 0, cls: 'badge-green' },
                            { label: 'Cancelled', value: byStatus.cancelled ?? 0, cls: 'badge-red' },
                        ].map(({ label, value, cls }) => (
                            <a key={label} href={`/paired/admin/bookings?status=${label.toLowerCase()}`} className="card card-p flex items-center gap-3 no-underline hover-lift">
                                <span className={`badge ${cls}`} style={{ fontSize: '1rem', padding: '4px 10px' }}>{value}</span>
                                <span className="text-sm text-text-2">{label}</span>
                            </a>
                        ))}
                    </div>
                </div>
            )}

            {/* Admin Links Grid */}
            <h2 className="text-base font-semibold mb-3 text-text-2">Admin Tools</h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                {adminLinks.map(({ href, icon, label, desc, color }) => (
                    <a
                        key={href}
                        href={href}
                        className="card card-p no-underline"
                        style={{ display: 'flex', flexDirection: 'column', gap: 12 }}
                    >
                        <div style={{
                            width: 44,
                            height: 44,
                            borderRadius: 10,
                            background: `${color}18`,
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            color,
                        }}>
                            {icon}
                        </div>
                        <div>
                            <p className="font-semibold text-sm">{label}</p>
                            <p className="text-xs text-text-3 mt-0.5">{desc}</p>
                        </div>
                    </a>
                ))}
            </div>
        </div>
    );
}
