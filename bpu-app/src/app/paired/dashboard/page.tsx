import { getBPUSession } from '@/lib/auth';
import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

interface Booking {
    id: number;
    date: string;
    time_slot: string;
    status: string;
    role: 'mentee' | 'mentor';
    mentor?: { id: number; display_name: string; avatar_url: string; };
    mentee?: { id: number; display_name: string; avatar_url: string; };
}

interface MentorSummary {
    id: number;
    display_name: string;
    industry: string;
    industryfield_of_expertise: string;
}

function mentorColor(id: number): string {
    const colors = ['#6366f1', '#8b5cf6', '#ec4899', '#3b82f6', '#14b8a6', '#f59e0b'];
    return colors[id % colors.length];
}

function formatDate(dateStr: string): string {
    if (!dateStr) return '';
    const d = new Date(dateStr + 'T00:00:00');
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    const diff = Math.round((d.getTime() - today.getTime()) / 86400000);
    if (diff === 0) return 'Today';
    if (diff === 1) return 'Tomorrow';
    if (diff === -1) return 'Yesterday';
    return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

function formatTimeSlot(slot: string): string {
    return slot ? slot.replace('-', ' – ') + ' GMT' : '';
}

export default async function PairedDashboard() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/dashboard');
    }
    const user = session.user!;

    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value || '';

    let bookings: Booking[] = [];
    let suggested: MentorSummary[] = [];

    await Promise.all([
        fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/bookings?per_page=50`, {
            headers: { 'Authorization': `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
        })
            .then(r => r.ok ? r.json() : null)
            .then(d => { if (d?.bookings) bookings = d.bookings; })
            .catch(() => {}),

        fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/mentors?per_page=3`, {
            headers: { 'Cache-Control': 'no-store' },
        })
            .then(r => r.ok ? r.json() : null)
            .then(d => { if (d?.mentors) suggested = d.mentors; })
            .catch(() => {}),
    ]);

    const today = new Date().toISOString().split('T')[0];
    const upcoming = bookings.filter(b => b.date >= today && b.status !== 'cancelled');
    const past = bookings.filter(b => b.date < today);
    const hoursTotal = past.length;

    return (
        <div className="wrap py-10 fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px' }}>

            {/* ── Page header ────────────────────────────────── */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold">My sessions</h1>
                    <p className="text-sm text-text-2 mt-1">Welcome back, {user.display_name}</p>
                </div>
                <a href="/paired/mentors" className="btn btn-purple btn-sm shrink-0">
                    Find a mentor
                </a>
            </div>

            {/* ── Stats ──────────────────────────────────────── */}
            <div className="grid grid-cols-3 gap-4">
                {[
                    { val: upcoming.length, label: 'Upcoming' },
                    { val: past.length,     label: 'Past sessions' },
                    { val: hoursTotal,      label: 'Hours mentored' },
                ].map(s => (
                    <div key={s.label} className="card card-p text-center">
                        <div className="stat-val">{s.val}</div>
                        <div className="stat-label">{s.label}</div>
                    </div>
                ))}
            </div>

            {/* ── Main grid ──────────────────────────────────── */}
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                {/* Left: sessions */}
                <div className="lg:col-span-2 space-y-6">

                    {/* Upcoming */}
                    <div className="card card-p space-y-4">
                        <p className="section-title">Upcoming sessions</p>
                        {upcoming.length === 0 ? (
                            <div className="text-sm text-text-3 py-4 text-center">
                                No upcoming sessions.{' '}
                                <a href="/paired/mentors" className="text-purple font-semibold hover:underline">
                                    Find a mentor →
                                </a>
                            </div>
                        ) : (
                            upcoming.map(b => {
                                const other = b.role === 'mentee' ? b.mentor : b.mentee;
                                const otherName = other?.display_name || 'Mentor';
                                const color = mentorColor(other?.id || b.id);
                                return (
                                    <div
                                        key={b.id}
                                        className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-lg"
                                        style={{ background: 'var(--purple-bg)' }}
                                    >
                                        <div className="flex items-center gap-3">
                                            <div className="avatar avatar-md" style={{ background: color }}>
                                                {otherName[0]}
                                            </div>
                                            <div>
                                                <p className="font-bold text-sm">{otherName}</p>
                                                <p className="text-xs text-text-2 capitalize">{b.role === 'mentee' ? 'Mentor' : 'Mentee'}</p>
                                                <p className="text-xs text-text-3 mt-0.5">
                                                    {formatDate(b.date)}, {formatTimeSlot(b.time_slot)}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex gap-2 shrink-0">
                                            <span className={`badge ${b.status === 'pending' ? 'badge-amber' : 'badge-green'} text-xs capitalize`}>
                                                {b.status || 'pending'}
                                            </span>
                                        </div>
                                    </div>
                                );
                            })
                        )}
                    </div>

                    {/* Past */}
                    {past.length > 0 && (
                        <div className="card card-p space-y-4">
                            <p className="section-title">Past sessions</p>
                            <div className="space-y-2">
                                {past.map(b => {
                                    const other = b.role === 'mentee' ? b.mentor : b.mentee;
                                    const otherName = other?.display_name || 'Mentor';
                                    const color = mentorColor(other?.id || b.id);
                                    return (
                                        <div
                                            key={b.id}
                                            className="flex items-center justify-between p-3 rounded-lg hover:bg-bg transition-colors"
                                        >
                                            <div className="flex items-center gap-3">
                                                <div className="avatar avatar-sm" style={{ background: color }}>
                                                    {otherName[0]}
                                                </div>
                                                <div>
                                                    <p className="text-sm font-semibold">{otherName}</p>
                                                    <p className="text-xs text-text-3">{formatDate(b.date)}</p>
                                                </div>
                                            </div>
                                            <a
                                                href={`/paired/mentors/${other?.id || ''}`}
                                                className="btn btn-ghost btn-sm text-xs"
                                            >
                                                Book again
                                            </a>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}
                </div>

                {/* Right: suggested mentors */}
                <div>
                    <div
                        className="card card-p space-y-4"
                        style={{ background: '#1e1b4b', borderColor: '#312e81', color: '#fff' }}
                    >
                        <div className="space-y-1">
                            <p className="font-bold">✨ Suggested mentors</p>
                            <p className="text-xs" style={{ color: '#c4b5fd' }}>
                                Experienced Black professionals ready to guide you.
                            </p>
                        </div>

                        {suggested.length === 0 ? (
                            <p className="text-xs" style={{ color: '#c4b5fd' }}>
                                Mentors are being onboarded — check back soon.
                            </p>
                        ) : (
                            <div className="space-y-3">
                                {suggested.map(m => (
                                    <a
                                        key={m.id}
                                        href={`/paired/mentors/${m.id}`}
                                        className="ai-match-item flex items-center justify-between p-3 rounded-lg"
                                    >
                                        <div className="flex items-center gap-3">
                                            <div
                                                className="avatar avatar-sm"
                                                style={{ background: mentorColor(m.id) }}
                                            >
                                                {m.display_name[0]}
                                            </div>
                                            <div>
                                                <p className="text-sm font-bold text-white">{m.display_name}</p>
                                                <p className="text-xs" style={{ color: '#c4b5fd' }}>
                                                    {m.industryfield_of_expertise || m.industry || 'Professional'}
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                ))}
                            </div>
                        )}

                        <a
                            href="/paired/mentors"
                            className="btn btn-sm w-full"
                            style={{
                                display: 'block',
                                textAlign: 'center',
                                background: 'rgba(255,255,255,0.12)',
                                color: '#e9d5ff',
                                border: '1px solid rgba(255,255,255,0.15)',
                            }}
                        >
                            Browse all mentors
                        </a>
                    </div>
                </div>

            </div>
        </div>
    );
}
