import { getBPUSession } from '@/lib/auth';
import { BPUApi } from '@/lib/api';
import { decodeHtml } from '@/lib/utils';
import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';
import BookingActions from './BookingActions';
import OnboardingChecklist from '../mentor/OnboardingChecklist';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

interface Booking {
    id: number;
    date: string;
    time_slot: string;
    status: string;
    notes?: string;
    role: 'mentee' | 'mentor';
    mentor?: { id: number; display_name: string; avatar_url: string; };
    mentee?: { id: number; display_name: string; avatar_url: string; };
    meet_link?: string;
    payment_status?: string;
    payment_amount?: number;
    is_group_session?: boolean;
}

interface MentorStats {
    total_bookings: number;
    pending: number;
    confirmed: number;
    completed: number;
    cancelled: number;
    unique_mentees: number;
    total_minutes: number;
}

interface MentorSummary {
    id: number;
    display_name: string;
    industry: string;
    industryfield_of_expertise: string;
    profile?: Record<string, string>;
    match_score?: number;
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
    const isPro = user.is_pro;
    const isMentor = user.roles.includes('mentor');

    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value || '';

    let bookings: Booking[] = [];
    let suggested: MentorSummary[] = [];
    let mentorStats: MentorStats | null = null as MentorStats | null;
    let menteeProfile: Record<string, string> = {};

    await Promise.all([
        fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/bookings?per_page=50`, {
            headers: { 'Authorization': `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
        })
            .then(r => r.ok ? r.json() : null)
            .then(d => { if (d?.bookings) bookings = d.bookings; })
            .catch(() => {}),

        isMentor
            ? fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/paired/mentor/stats`, {
                headers: { 'Authorization': `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
            })
                .then(r => r.ok ? r.json() : null)
                .then(d => { if (d?.stats) mentorStats = d.stats; })
                .catch(() => {})
            : Promise.resolve(),

        !isMentor
            ? fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/paired/mentee/profile`, {
                headers: { 'Authorization': `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
            })
                .then(r => r.ok ? r.json() : null)
                .then(d => { if (d?.profile) menteeProfile = d.profile; })
                .catch(() => {})
            : Promise.resolve(),

        !isMentor && isPro
            ? fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/mentors?per_page=12`, {
                headers: { 'Cache-Control': 'no-store' },
            })
                .then(r => r.ok ? r.json() : null)
                .then(d => {
                    if (d?.mentors && user.profile) {
                        const memberProfile = { ...(user.profile as unknown as Record<string, string>), ...menteeProfile };
                        suggested = (d.mentors as MentorSummary[])
                            .map(m => ({
                                ...m,
                                match_score: BPUApi.scoreMentorMatch(memberProfile, m.profile || {
                                    industry: m.industry,
                                    industryfield_of_expertise: m.industryfield_of_expertise,
                                }),
                            }))
                            .sort((a, b) => (b.match_score ?? 0) - (a.match_score ?? 0))
                            .slice(0, 3);
                    }
                })
                .catch(() => {})
            : Promise.resolve(),
    ]);

    const today = new Date().toISOString().split('T')[0];

    if (isMentor) {
        const mentorBookings = bookings.filter(b => b.role === 'mentor');
        const menteeBookings = bookings.filter(b => b.role === 'mentee');
        const pendingRequests = mentorBookings.filter(b => b.status === 'pending' && b.date >= today);
        const upcomingAsMentor = mentorBookings.filter(b => b.date >= today && b.status === 'confirmed');
        const upcomingAsMentee = menteeBookings.filter(b => b.date >= today && b.status !== 'cancelled');
        const pastAsMentor = mentorBookings.filter(b => b.date < today);

        return (
            <div className="wrap py-10 fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px' }}>

                {/* ── Mentor header ────────────────────────────── */}
                <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div className="flex items-center gap-2 mb-1">
                            <h1 className="text-2xl font-bold">Mentor Dashboard</h1>
                            <span className="badge badge-purple">Mentor</span>
                        </div>
                        <p className="text-sm text-text-2">Welcome back, {decodeHtml(user.display_name)}</p>
                    </div>
                    <a href={`/paired/mentors/${user.id}`} className="btn btn-purple btn-sm shrink-0">
                        View my profile →
                    </a>
                </div>

                {/* ── Stats ────────────────────────────────────── */}
                <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    {[
                        { val: mentorStats?.pending ?? pendingRequests.length, label: 'Pending requests', accent: 'var(--amber, #f59e0b)' },
                        { val: mentorStats?.confirmed ?? upcomingAsMentor.length, label: 'Upcoming sessions', accent: 'var(--purple)' },
                        { val: mentorStats?.completed ?? pastAsMentor.length, label: 'Completed', accent: 'var(--green, #22c55e)' },
                        { val: mentorStats?.unique_mentees ?? new Set(mentorBookings.map(b => b.mentee?.id).filter(Boolean)).size, label: 'Total mentees', accent: undefined },
                    ].map(s => (
                        <div key={s.label} className="card card-p text-center" style={s.accent ? { borderTop: `3px solid ${s.accent}` } : undefined}>
                            <div className="stat-val">{s.val}</div>
                            <div className="stat-label">{s.label}</div>
                        </div>
                    ))}
                </div>

                <OnboardingChecklist />

                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">

                    <div className="lg:col-span-2 space-y-6">

                        {/* ── Pending requests ─────────────────────── */}
                        {pendingRequests.length > 0 && (
                            <div className="card card-p space-y-4">
                                <div className="flex items-center justify-between">
                                    <p className="section-title">Pending requests</p>
                                    <span className="badge badge-amber text-xs">{pendingRequests.length} new</span>
                                </div>
                                {pendingRequests.map(b => {
                                    const mentee = b.mentee;
                                    const menteeName = mentee?.display_name || 'Mentee';
                                    const color = mentorColor(mentee?.id || b.id);
                                    return (
                                        <div
                                            key={b.id}
                                            className="p-4 rounded-lg border border-border space-y-3"
                                        >
                                            <div className="flex items-center justify-between">
                                                <div className="flex items-center gap-3">
                                                    <div className="avatar avatar-md text-white" style={{ background: color }}>
                                                        {menteeName[0]}
                                                    </div>
                                                    <div>
                                                        <p className="font-bold text-sm">{decodeHtml(menteeName)}</p>
                                                        <p className="text-xs text-text-3">
                                                            {formatDate(b.date)}, {formatTimeSlot(b.time_slot)}
                                                        </p>
                                                    </div>
                                                </div>
                                                <BookingActions bookingId={b.id} />
                                            </div>
                                            {b.notes && (
                                                <p className="text-sm text-text-2 bg-bg rounded-lg p-3">{decodeHtml(b.notes)}</p>
                                            )}
                                        </div>
                                    );
                                })}
                            </div>
                        )}

                        {/* ── Upcoming as mentor ───────────────────── */}
                        <div className="card card-p space-y-4">
                            <p className="section-title">Upcoming mentoring sessions</p>
                            {upcomingAsMentor.length === 0 ? (
                                <div className="text-sm text-text-3 py-4 text-center">
                                    No confirmed sessions yet.
                                </div>
                            ) : (
                                upcomingAsMentor.map(b => {
                                    const mentee = b.mentee;
                                    const menteeName = mentee?.display_name || 'Mentee';
                                    const color = mentorColor(mentee?.id || b.id);
                                    return (
                                        <div
                                            key={b.id}
                                            className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 rounded-lg"
                                            style={{ background: 'var(--purple-bg)' }}
                                        >
                                            <div className="flex items-center gap-3">
                                                <div className="avatar avatar-md text-white" style={{ background: color }}>
                                                    {menteeName[0]}
                                                </div>
                                                <div>
                                                    <p className="font-bold text-sm">{decodeHtml(menteeName)}</p>
                                                    <p className="text-xs text-text-3 mt-0.5">
                                                        {formatDate(b.date)}, {formatTimeSlot(b.time_slot)}
                                                    </p>
                                                </div>
                                            </div>
                                            <div className="flex items-center gap-2 shrink-0">
                                                {b.meet_link && (
                                                    <a
                                                        href={b.meet_link}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="btn btn-purple btn-sm text-xs"
                                                        onClick={(e) => e.stopPropagation()}
                                                    >
                                                        Join meeting
                                                    </a>
                                                )}
                                                <a
                                                    href={`/paired/bookings/${b.id}`}
                                                    className="btn btn-ghost btn-sm text-xs"
                                                    onClick={(e) => e.stopPropagation()}
                                                >
                                                    Details
                                                </a>
                                                <span className="badge badge-green text-xs">Confirmed</span>
                                            </div>
                                        </div>
                                    );
                                })
                            )}
                        </div>

                        {/* ── Upcoming as mentee (if they also use the platform as a mentee) */}
                        {upcomingAsMentee.length > 0 && (
                            <div className="card card-p space-y-4">
                                <p className="section-title">My sessions as mentee</p>
                                {upcomingAsMentee.map(b => {
                                    const mentor = b.mentor;
                                    const mentorName = mentor?.display_name || 'Mentor';
                                    const color = mentorColor(mentor?.id || b.id);
                                    return (
                                        <div
                                            key={b.id}
                                            className="flex items-center justify-between p-4 rounded-lg"
                                            style={{ background: 'var(--surface)' }}
                                        >
                                            <div className="flex items-center gap-3">
                                                <div className="avatar avatar-md text-white" style={{ background: color }}>
                                                    {mentorName[0]}
                                                </div>
                                                <div>
                                                    <p className="font-bold text-sm">{decodeHtml(mentorName)}</p>
                                                    <p className="text-xs text-text-2">Mentor</p>
                                                    <p className="text-xs text-text-3 mt-0.5">
                                                        {formatDate(b.date)}, {formatTimeSlot(b.time_slot)}
                                                    </p>
                                                </div>
                                            </div>
                                            <div className="flex items-center gap-2">
                                                <a
                                                    href={`/paired/bookings/${b.id}`}
                                                    className="btn btn-ghost btn-sm text-xs"
                                                >
                                                    Details
                                                </a>
                                                <span className={`badge ${b.status === 'pending' ? 'badge-amber' : 'badge-green'} text-xs capitalize`}>
                                                    {b.status || 'pending'}
                                                </span>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        )}

                        {/* Past sessions given */}
                        {pastAsMentor.length > 0 && (
                            <div className="card card-p space-y-4">
                                <p className="section-title">Past sessions given</p>
                                <div className="space-y-2">
                                    {pastAsMentor.map(b => {
                                        const mentee = b.mentee;
                                        const menteeName = mentee?.display_name || 'Mentee';
                                        const color = mentorColor(mentee?.id || b.id);
                                        return (
                                            <div
                                                key={b.id}
                                                className="flex items-center justify-between p-3 rounded-lg hover:bg-bg transition-colors"
                                            >
                                                <div className="flex items-center gap-3">
                                                    <div className="avatar avatar-sm text-white" style={{ background: color }}>
                                                        {menteeName[0]}
                                                    </div>
                                                    <div>
                                                        <p className="text-sm font-semibold">{decodeHtml(menteeName)}</p>
                                                        <p className="text-xs text-text-3">{formatDate(b.date)}</p>
                                                    </div>
                                                </div>
                                                <a
                                                    href={`/paired/bookings/${b.id}`}
                                                    className="btn btn-ghost btn-sm text-xs"
                                                >
                                                    Details
                                                </a>
                                            </div>
                                        );
                                    })}
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Right sidebar */}
                    <div className="space-y-6">
                        {/* Profile card */}
                        <div className="card card-p space-y-4" style={{ background: '#1e1b4b', borderColor: '#312e81', color: '#fff' }}>
                            <div className="space-y-2">
                                <p className="font-bold">Your mentor profile</p>
                                <p className="text-xs" style={{ color: '#c4b5fd' }}>
                                    Your profile is live in the PAIRED directory. Mentees can find and book sessions with you.
                                </p>
                            </div>
                            <a
                                href={`/paired/mentors/${user.id}`}
                                className="btn btn-sm w-full"
                                style={{ display: 'block', textAlign: 'center', background: 'rgba(255,255,255,0.12)', color: '#e9d5ff', border: '1px solid rgba(255,255,255,0.15)' }}
                            >
                                View my profile →
                            </a>
                            <a
                                href="/paired/mentors"
                                className="btn btn-sm w-full"
                                style={{ display: 'block', textAlign: 'center', background: 'rgba(255,255,255,0.06)', color: '#a78bfa', border: '1px solid rgba(255,255,255,0.08)' }}
                            >
                                Browse other mentors
                            </a>
                        </div>

                        {/* Quick links */}
                        <div className="card card-p space-y-3">
                            <p className="section-title">Quick links</p>
                            <div className="space-y-2">
                                <a href="/paired/mentor/settings" className="btn btn-ghost btn-sm w-full text-left text-sm" style={{ display: 'block' }}>
                                    Edit profile
                                </a>
                                <a href="/paired/mentor/sessions" className="btn btn-ghost btn-sm w-full text-left text-sm" style={{ display: 'block' }}>
                                    My sessions
                                </a>
                                <a href={`/paired/mentors/${user.id}`} className="btn btn-ghost btn-sm w-full text-left text-sm" style={{ display: 'block' }}>
                                    View public profile
                                </a>
                                <a href="/paired/mentor/mentees" className="btn btn-ghost btn-sm w-full text-left text-sm" style={{ display: 'block' }}>
                                    My mentees
                                </a>
                            </div>
                        </div>

                        {/* Quick tips */}
                        <div className="card card-p space-y-3">
                            <p className="section-title">Mentor tips</p>
                            <ul className="space-y-2 text-sm text-text-2">
                                <li className="flex items-start gap-2">
                                    <span className="text-purple shrink-0 mt-0.5">1.</span>
                                    <span>Respond to session requests within 24 hours</span>
                                </li>
                                <li className="flex items-start gap-2">
                                    <span className="text-purple shrink-0 mt-0.5">2.</span>
                                    <span>Keep your profile bio and skills up to date</span>
                                </li>
                                <li className="flex items-start gap-2">
                                    <span className="text-purple shrink-0 mt-0.5">3.</span>
                                    <span>Set clear expectations in your first session</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    // ═══════════════════════════════════════════════════════════
    //  MENTEE DASHBOARD (existing)
    // ═══════════════════════════════════════════════════════════

    const upcoming = bookings.filter(b => b.date >= today && b.status !== 'cancelled');
    const past = bookings.filter(b => b.date < today);
    const hoursTotal = past.length;

    return (
        <div className="wrap py-10 fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px' }}>

            {/* ── Page header ────────────────────────────────── */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold">My sessions</h1>
                    <p className="text-sm text-text-2 mt-1">Welcome back, {decodeHtml(user.display_name)}</p>
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
                                                <p className="font-bold text-sm">{decodeHtml(otherName)}</p>
                                                <p className="text-xs text-text-2 capitalize">{b.role === 'mentee' ? 'Mentor' : 'Mentee'}</p>
                                                <p className="text-xs text-text-3 mt-0.5">
                                                    {formatDate(b.date)}, {formatTimeSlot(b.time_slot)}
                                                </p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-2 shrink-0">
                                            {b.meet_link && b.status === 'confirmed' && (
                                                <a
                                                    href={b.meet_link}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className="btn btn-purple btn-sm text-xs"
                                                >
                                                    Join meeting
                                                </a>
                                            )}
                                            <a
                                                href={`/paired/bookings/${b.id}`}
                                                className="btn btn-ghost btn-sm text-xs"
                                            >
                                                Details
                                            </a>
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
                                                    <p className="text-sm font-semibold">{decodeHtml(otherName)}</p>
                                                    <p className="text-xs text-text-3">{formatDate(b.date)}</p>
                                                </div>
                                            </div>
                                            <div className="flex gap-2">
                                                <a
                                                    href={`/paired/bookings/${b.id}`}
                                                    className="btn btn-ghost btn-sm text-xs"
                                                >
                                                    Details
                                                </a>
                                                {b.role === 'mentee' && (
                                                    <a
                                                        href={`/paired/mentors/${other?.id || ''}`}
                                                        className="btn btn-ghost btn-sm text-xs"
                                                    >
                                                        Book again
                                                    </a>
                                                )}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}
                </div>

                {/* Right: AI matched mentors (Pro) or upgrade CTA (Free) */}
                <div>
                    {isPro ? (
                        <div
                            className="card card-p space-y-4"
                            style={{ background: '#1e1b4b', borderColor: '#312e81', color: '#fff' }}
                        >
                            <div className="space-y-1">
                                <p className="font-bold">AI matched mentors</p>
                                <p className="text-xs" style={{ color: '#c4b5fd' }}>
                                    Ranked by AI compatibility with your profile.
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
                                                    <p className="text-sm font-bold text-white">{decodeHtml(m.display_name)}</p>
                                                    <p className="text-xs" style={{ color: '#c4b5fd' }}>
                                                        {decodeHtml(m.industryfield_of_expertise || m.industry || 'Professional')}
                                                    </p>
                                                </div>
                                            </div>
                                            {m.match_score != null && m.match_score > 0 && (
                                                <span
                                                    className="text-xs font-bold shrink-0"
                                                    style={{ color: '#fbbf24' }}
                                                >
                                                    {m.match_score}%
                                                </span>
                                            )}
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
                    ) : (
                        <div
                            className="card card-p space-y-5 text-center"
                            style={{ background: '#1e1b4b', borderColor: '#312e81', color: '#fff' }}
                        >
                            <div className="space-y-2">
                                <p className="text-2xl">✨</p>
                                <p className="font-bold text-base">AI Mentor Matching</p>
                                <p className="text-xs leading-relaxed" style={{ color: '#c4b5fd' }}>
                                    Upgrade to BPU Pro and our AI will analyse your profile to surface the mentors
                                    whose experience best aligns with where you want to go.
                                </p>
                            </div>
                            <a
                                href="/upgrade"
                                className="btn btn-amber btn-sm w-full"
                                style={{ display: 'block', textAlign: 'center' }}
                            >
                                Upgrade to Pro →
                            </a>
                            <a
                                href="/paired/mentors"
                                className="btn btn-sm w-full"
                                style={{
                                    display: 'block',
                                    textAlign: 'center',
                                    background: 'rgba(255,255,255,0.10)',
                                    color: '#e9d5ff',
                                    border: '1px solid rgba(255,255,255,0.15)',
                                }}
                            >
                                Browse mentors manually
                            </a>
                        </div>
                    )}
                </div>

            </div>
        </div>
    );
}
