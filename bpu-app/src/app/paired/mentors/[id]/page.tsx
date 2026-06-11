import { getBPUSession } from '@/lib/auth';
import { BPUApi } from '@/lib/api';
import BookingForm from './BookingForm';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

function mentorColor(id: number): string {
    const colors = ['#6366f1', '#8b5cf6', '#ec4899', '#3b82f6', '#14b8a6', '#f59e0b', '#ef4444'];
    return colors[id % colors.length];
}

function isGravatar(url: string): boolean {
    if (!url) return true;
    if (!url.includes('gravatar.com')) return false;
    return url.includes('d=blank') || url.includes('d=mm') || url.includes('d=mystery');
}

export default async function MentorProfile({
    params,
}: {
    params: Promise<{ id: string }>;
}) {
    const { id } = await params;
    const session = await getBPUSession();

    let mentor: Record<string, unknown> | null = null;
    let fetchError = '';

    try {
        const res = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/mentors/${id}`, {
            cache: 'no-store',
        });
        if (res.ok) {
            const data = await res.json();
            mentor = data.mentor || null;
        } else if (res.status === 404) {
            fetchError = 'Mentor not found.';
        } else {
            fetchError = 'Failed to load mentor profile.';
        }
    } catch {
        fetchError = 'Could not connect to the server.';
    }

    if (fetchError || !mentor) {
        return (
            <div className="wrap py-12">
                <div className="card card-p text-center space-y-4">
                    <p className="text-text-2">{fetchError || 'Mentor not found.'}</p>
                    <a href="/paired/mentors" className="btn btn-outline btn-sm">← Browse mentors</a>
                </div>
            </div>
        );
    }

    const mentorId = mentor.id as number;
    const name = mentor.display_name as string;
    const avatarUrl = mentor.avatar_url as string;
    const profile = (mentor.profile as Record<string, string>) || {};
    const color = mentorColor(mentorId);
    const hasPhoto = avatarUrl && !isGravatar(avatarUrl);

    const currentRole = profile.current_role || '';
    const company = profile.company || '';
    const title = currentRole || profile.industryfield_of_expertise || profile.industry || 'Professional';
    const industry = profile.industry || '';
    const exp = profile.years_of_experience || '';
    const bio = profile.user_bio || '';
    const residence = profile.residence || '';
    const linkedin = profile.linkedin_profile || '';
    const employmentStatus = profile.employment_status || profile.current_employment_status || '';
    const education = profile.level_of_education || '';
    const mentorshipAvailability = profile.mentorship_availability || '';
    const mentorshipRequirements = profile.mentorship_requirements || '';
    const menteesAtOnce = profile.mentees_at_once || '';

    const skills = (profile.skills_separate || '')
        .split(',')
        .map(s => s.trim())
        .filter(Boolean);

    const isPro = session.user?.is_pro ?? false;
    const compatScore = isPro && session.user?.profile
        ? BPUApi.scoreMentorMatch(
            session.user.profile as unknown as Record<string, string>,
            profile,
          )
        : null;

    const infoItems: { icon: string; label: string; value: string }[] = [];
    if (industry) infoItems.push({ icon: 'briefcase', label: 'Industry', value: industry });
    if (exp) infoItems.push({ icon: 'clock', label: 'Experience', value: `${exp} years` });
    if (company) infoItems.push({ icon: 'building', label: 'Company', value: company });
    if (residence) infoItems.push({ icon: 'map-pin', label: 'Location', value: residence });
    if (employmentStatus) infoItems.push({ icon: 'user', label: 'Status', value: employmentStatus });
    if (education) infoItems.push({ icon: 'book', label: 'Education', value: education });

    const iconSvg = (icon: string) => {
        switch (icon) {
            case 'briefcase': return <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>;
            case 'clock': return <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>;
            case 'building': return <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="4" y="2" width="16" height="20" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M8 10h.01"/><path d="M16 10h.01"/><path d="M8 14h.01"/><path d="M16 14h.01"/></svg>;
            case 'map-pin': return <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>;
            case 'user': return <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>;
            case 'book': return <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>;
            default: return null;
        }
    };

    return (
        <div className="wrap py-12 fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px' }}>

            {/* ── Back link ─────────────────────────────────── */}
            <a href="/paired/mentors" className="text-sm text-text-3 hover:text-brand flex items-center gap-1" style={{ width: 'fit-content' }}>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Back to mentors
            </a>

            {/* ── Profile header ─────────────────────────────── */}
            <div className="card" style={{ overflow: 'hidden' }}>
                <div style={{ height: '6px', background: color }} />
                <div style={{ padding: '32px' }}>
                    <div className="flex flex-col md:flex-row gap-6 items-center md:items-start">
                        {hasPhoto ? (
                            <img
                                src={avatarUrl}
                                alt={name}
                                className="shrink-0 rounded-full object-cover"
                                style={{ width: 96, height: 96, border: `3px solid ${color}20` }}
                            />
                        ) : (
                            <div
                                className="avatar avatar-xl text-white shrink-0"
                                style={{ background: color, width: 96, height: 96, fontSize: '2rem' }}
                            >
                                {name[0]}
                            </div>
                        )}

                        <div className="flex-1 text-center md:text-left space-y-3">
                            <div>
                                <h1 className="text-3xl font-extrabold">{name}</h1>
                                <p className="text-lg text-text-2 mt-1">{title}</p>
                                {company && currentRole && (
                                    <p className="text-sm text-text-3 mt-0.5">at {company}</p>
                                )}
                                {!currentRole && company && (
                                    <p className="text-sm text-text-3 mt-0.5">{company}</p>
                                )}
                            </div>

                            {/* Quick info chips */}
                            <div className="flex flex-wrap gap-2 justify-center md:justify-start">
                                {industry && (
                                    <span className="badge badge-purple">{industry}</span>
                                )}
                                {exp && (
                                    <span className="badge badge-green">{exp} yrs experience</span>
                                )}
                                {residence && (
                                    <span className="badge" style={{ background: 'var(--surface)', color: 'var(--text-2)' }}>{residence}</span>
                                )}
                            </div>

                            {skills.length > 0 && (
                                <div className="flex flex-wrap gap-2 justify-center md:justify-start">
                                    {skills.map(s => (
                                        <span key={s} className="text-xs font-medium" style={{ padding: '4px 10px', borderRadius: '6px', background: `${color}15`, color }}>
                                            {s}
                                        </span>
                                    ))}
                                </div>
                            )}
                        </div>

                        <div className="shrink-0 w-full md:w-auto text-center space-y-3">
                            {compatScore !== null && compatScore > 0 && (
                                <div className="card card-p text-center space-y-1" style={{ minWidth: 100 }}>
                                    <p className="text-2xl font-extrabold" style={{ color: 'var(--brand)' }}>{compatScore}%</p>
                                    <p className="text-xs text-text-3">AI match</p>
                                </div>
                            )}
                            {linkedin && (
                                <a
                                    href={linkedin.startsWith('http') ? linkedin : `https://${linkedin}`}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="btn btn-outline btn-sm w-full justify-center"
                                    style={{ gap: '6px' }}
                                >
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                                    LinkedIn
                                </a>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* ── Body ───────────────────────────────────────── */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div className="md:col-span-2 space-y-6">
                    {/* About */}
                    <div className="card card-p space-y-3">
                        <p className="section-title">About</p>
                        {bio ? (
                            <p className="text-sm text-text-2 leading-relaxed whitespace-pre-line">{bio}</p>
                        ) : (
                            <p className="text-sm text-text-3">This mentor hasn&apos;t added a bio yet.</p>
                        )}
                    </div>

                    {/* Details grid */}
                    {infoItems.length > 0 && (
                        <div className="card card-p space-y-3">
                            <p className="section-title">Details</p>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                {infoItems.map(item => (
                                    <div key={item.label} className="flex items-start gap-3">
                                        <div className="text-text-3 mt-0.5 shrink-0">{iconSvg(item.icon)}</div>
                                        <div>
                                            <p className="text-xs text-text-3 uppercase tracking-wide font-medium">{item.label}</p>
                                            <p className="text-sm text-text font-medium mt-0.5">{item.value}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Mentorship info */}
                    {(mentorshipAvailability || mentorshipRequirements || menteesAtOnce) && (
                        <div className="card card-p space-y-3">
                            <p className="section-title">Mentorship</p>
                            <div className="space-y-4">
                                {mentorshipAvailability && (
                                    <div className="flex items-start gap-3">
                                        <div className="text-text-3 mt-0.5 shrink-0">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                                        </div>
                                        <div>
                                            <p className="text-xs text-text-3 uppercase tracking-wide font-medium">Availability</p>
                                            <p className="text-sm text-text mt-0.5">{mentorshipAvailability}</p>
                                        </div>
                                    </div>
                                )}
                                {menteesAtOnce && (
                                    <div className="flex items-start gap-3">
                                        <div className="text-text-3 mt-0.5 shrink-0">
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                        </div>
                                        <div>
                                            <p className="text-xs text-text-3 uppercase tracking-wide font-medium">Mentees at a time</p>
                                            <p className="text-sm text-text mt-0.5">{menteesAtOnce}</p>
                                        </div>
                                    </div>
                                )}
                                {mentorshipRequirements && (
                                    <div>
                                        <p className="text-xs text-text-3 uppercase tracking-wide font-medium mb-1">What this mentor expects</p>
                                        <p className="text-sm text-text-2 leading-relaxed whitespace-pre-line">{mentorshipRequirements}</p>
                                    </div>
                                )}
                            </div>
                        </div>
                    )}
                </div>

                <div className="space-y-6">
                    <BookingForm
                        mentorId={mentorId}
                        mentorName={name}
                        isAuthenticated={session.authenticated}
                    />
                </div>

            </div>
        </div>
    );
}
