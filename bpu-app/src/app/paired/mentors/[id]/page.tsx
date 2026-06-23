import { getBPUSession } from '@/lib/auth';
import { BPUApi } from '@/lib/api';
import { decodeHtml } from '@/lib/utils';
import { cookies } from 'next/headers';
import BookingForm from './BookingForm';
import FavouriteButton from '../../FavouriteButton';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

interface Review {
    id: number;
    rating: number;
    feedback: string;
    mentee_name: string;
    mentee_avatar: string;
    created_at: string;
}

function StarRating({ rating, size = 14 }: { rating: number; size?: number }) {
    return (
        <span className="inline-flex gap-0.5">
            {[1, 2, 3, 4, 5].map(i => (
                <svg
                    key={i}
                    width={size}
                    height={size}
                    viewBox="0 0 24 24"
                    fill={i <= rating ? '#f59e0b' : 'none'}
                    stroke={i <= rating ? '#f59e0b' : 'var(--text-3)'}
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                >
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                </svg>
            ))}
        </span>
    );
}

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
            <div className="wrap py-8">
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

    const currentRole = decodeHtml(profile.current_role || '');
    const company = decodeHtml(profile.company || '');
    const title = currentRole || decodeHtml(profile.industryfield_of_expertise || profile.industry || 'Professional');
    const industry = decodeHtml(profile.industry || '');
    const exp = profile.years_of_experience || '';
    const bio = decodeHtml(profile.user_bio || '');
    const residence = decodeHtml(profile.residence || '');
    const linkedin = profile.linkedin_profile || '';
    const employmentStatus = decodeHtml(profile.employment_status || profile.current_employment_status || '');
    const education = decodeHtml(profile.level_of_education || '');
    const mentorshipAvailability = decodeHtml(profile.mentorship_availability || '');
    const mentorshipRequirements = decodeHtml(profile.mentorship_requirements || '');
    const menteesAtOnce = profile.mentees_at_once || '';

    const skills = (profile.skills_separate || '')
        .split(',')
        .map(s => decodeHtml(s.trim()))
        .filter(Boolean);

    const experiences = (mentor.experiences as Array<Record<string, unknown>>) || [];
    const educationList = (mentor.education as Array<Record<string, unknown>>) || [];

    // Fetch session types (public endpoint)
    let sessionTypes: Array<Record<string, unknown>> = [];
    try {
        const sessRes = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/paired/mentors/${id}/sessions`, {
            cache: 'no-store',
        });
        if (sessRes.ok) {
            const sessData = await sessRes.json();
            sessionTypes = sessData.sessions || [];
        }
    } catch {
        // Sessions unavailable — section simply won't render
    }

    // Fetch reviews
    let reviews: Review[] = [];
    let avgRating = 0;
    let reviewCount = 0;
    try {
        const revRes = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/paired/mentors/${id}/reviews`, {
            cache: 'no-store',
        });
        if (revRes.ok) {
            const revData = await revRes.json();
            reviews = revData.reviews || [];
            avgRating = revData.average_rating || 0;
            reviewCount = revData.total || reviews.length;
        }
    } catch {
        // Reviews unavailable — section simply won't render
    }

    // Fetch user's favourites to check if this mentor is favourited
    let isFavourited = false;
    if (session.authenticated) {
        try {
            const cookieStore = await cookies();
            const jwt = cookieStore.get('bpu_session')?.value || '';
            if (jwt) {
                const favRes = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/paired/favourites`, {
                    headers: { 'Authorization': `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
                });
                if (favRes.ok) {
                    const favData = await favRes.json();
                    const favourites: Array<{ mentor_id: number }> = favData.favourites || [];
                    isFavourited = favourites.some(f => f.mentor_id === mentorId);
                }
            }
        } catch {
            // Favourites unavailable — button will default to unfavourited
        }
    }

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
        <div className="wrap py-8 fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px' }}>

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
                                <div className="flex items-center gap-2 justify-center md:justify-start">
                                    <h1 className="text-3xl font-extrabold">{name}</h1>
                                    {session.authenticated && (
                                        <FavouriteButton mentorId={mentorId} initialFavourited={isFavourited} size={26} />
                                    )}
                                </div>
                                <p className="text-lg text-text-2 mt-1">{title}</p>
                                {company && currentRole && (
                                    <p className="text-sm text-text-3 mt-0.5">at {company}</p>
                                )}
                                {!currentRole && company && (
                                    <p className="text-sm text-text-3 mt-0.5">{company}</p>
                                )}
                                {reviewCount > 0 && (
                                    <div className="flex items-center gap-2 mt-1 justify-center md:justify-start">
                                        <StarRating rating={Math.round(avgRating)} size={16} />
                                        <span className="text-sm font-semibold" style={{ color: '#f59e0b' }}>
                                            {avgRating.toFixed(1)}
                                        </span>
                                        <span className="text-sm text-text-3">
                                            ({reviewCount} review{reviewCount !== 1 ? 's' : ''})
                                        </span>
                                    </div>
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

                    {/* Available Sessions */}
                    {sessionTypes.length > 0 && (
                        <div className="card card-p space-y-3">
                            <p className="section-title">Available Sessions</p>
                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                {sessionTypes.map((s) => (
                                    <div key={s.id as number} className="rounded-lg p-4 space-y-2" style={{ background: 'var(--surface)', border: '1px solid var(--border)' }}>
                                        <div className="flex items-center gap-2">
                                            <p className="text-sm font-semibold text-text flex-1">{decodeHtml(s.name as string)}</p>
                                            {(s.duration as number) > 0 && (
                                                <span className="badge badge-green text-xs shrink-0">{s.duration as number} min</span>
                                            )}
                                        </div>
                                        {(s.description as string) && (
                                            <p className="text-xs text-text-3 leading-relaxed">{decodeHtml(s.description as string)}</p>
                                        )}
                                        {(s.price as number) > 0 && (
                                            <p className="text-xs font-medium" style={{ color: 'var(--brand)' }}>&pound;{(s.price as number).toFixed(2)}</p>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {/* Work Experience */}
                    {experiences.length > 0 && (
                        <div className="card card-p space-y-3">
                            <p className="section-title">Work Experience</p>
                            <div className="space-y-4">
                                {experiences.map((exp) => {
                                    const startDate = exp.start_date as string;
                                    const endDate = exp.end_date as string;
                                    const isCurrent = exp.is_current as number;
                                    const formatDate = (d: string) => {
                                        if (!d) return '';
                                        const date = new Date(d);
                                        return date.toLocaleDateString('en-GB', { month: 'short', year: 'numeric' });
                                    };
                                    const dateRange = startDate
                                        ? `${formatDate(startDate)} - ${isCurrent ? 'Present' : formatDate(endDate)}`
                                        : '';
                                    return (
                                        <div key={exp.id as number} className="flex items-start gap-3">
                                            <div className="text-text-3 mt-0.5 shrink-0">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                            </div>
                                            <div className="flex-1">
                                                <p className="text-sm font-semibold text-text">{decodeHtml(exp.title as string)}</p>
                                                {(exp.company as string) && (
                                                    <p className="text-sm text-text-2">{decodeHtml(exp.company as string)}</p>
                                                )}
                                                {dateRange && (
                                                    <p className="text-xs text-text-3 mt-0.5">{dateRange}</p>
                                                )}
                                                {(exp.description as string) && (
                                                    <p className="text-xs text-text-3 mt-1 leading-relaxed">{decodeHtml(exp.description as string)}</p>
                                                )}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}

                    {/* Education */}
                    {educationList.length > 0 && (
                        <div className="card card-p space-y-3">
                            <p className="section-title">Education</p>
                            <div className="space-y-4">
                                {educationList.map((edu) => {
                                    const startYear = edu.start_year as string;
                                    const endYear = edu.end_year as string;
                                    const yearRange = startYear
                                        ? `${startYear}${endYear ? ` - ${endYear}` : ''}`
                                        : '';
                                    return (
                                        <div key={edu.id as number} className="flex items-start gap-3">
                                            <div className="text-text-3 mt-0.5 shrink-0">
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c0 2 4 3 6 3s6-1 6-3v-5"/></svg>
                                            </div>
                                            <div className="flex-1">
                                                <p className="text-sm font-semibold text-text">{decodeHtml(edu.degree as string)}</p>
                                                {(edu.institution as string) && (
                                                    <p className="text-sm text-text-2">{decodeHtml(edu.institution as string)}</p>
                                                )}
                                                {yearRange && (
                                                    <p className="text-xs text-text-3 mt-0.5">{yearRange}</p>
                                                )}
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>
                        </div>
                    )}

                    {/* Reviews */}
                    <div className="card card-p space-y-4">
                        <div className="flex items-center justify-between">
                            <p className="section-title">Reviews</p>
                            {session.authenticated && (
                                <a
                                    href={`/paired/mentors/${id}/review`}
                                    className="btn btn-outline btn-sm"
                                >
                                    Leave a review
                                </a>
                            )}
                        </div>
                        {reviews.length === 0 ? (
                            <p className="text-sm text-text-3 py-4 text-center">
                                No reviews yet.{' '}
                                {session.authenticated && (
                                    <a href={`/paired/mentors/${id}/review`} className="text-purple font-semibold hover:underline">
                                        Be the first to leave one.
                                    </a>
                                )}
                            </p>
                        ) : (
                            <div className="space-y-4">
                                {reviews.map(review => (
                                    <div
                                        key={review.id}
                                        className="rounded-lg p-4 space-y-2"
                                        style={{ background: 'var(--surface)', border: '1px solid var(--border)' }}
                                    >
                                        <div className="flex items-center gap-3">
                                            {review.mentee_avatar ? (
                                                <img
                                                    src={review.mentee_avatar}
                                                    alt=""
                                                    style={{ width: 32, height: 32, borderRadius: '50%', objectFit: 'cover', flexShrink: 0 }}
                                                />
                                            ) : (
                                                <div
                                                    style={{
                                                        width: 32, height: 32, borderRadius: '50%',
                                                        background: 'var(--purple-bg)', color: 'var(--purple)',
                                                        display: 'flex', alignItems: 'center', justifyContent: 'center',
                                                        flexShrink: 0, fontSize: 14, fontWeight: 600,
                                                    }}
                                                >
                                                    {(review.mentee_name || '?')[0].toUpperCase()}
                                                </div>
                                            )}
                                            <div className="flex-1">
                                                <p className="text-sm font-semibold">{decodeHtml(review.mentee_name)}</p>
                                                <div className="flex items-center gap-2">
                                                    <StarRating rating={review.rating} size={12} />
                                                    <span className="text-xs text-text-3">
                                                        {new Date(review.created_at).toLocaleDateString('en-GB', {
                                                            day: 'numeric', month: 'short', year: 'numeric',
                                                        })}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        {review.feedback && (
                                            <p className="text-sm text-text-2 leading-relaxed">{decodeHtml(review.feedback)}</p>
                                        )}
                                    </div>
                                ))}
                            </div>
                        )}
                    </div>
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
