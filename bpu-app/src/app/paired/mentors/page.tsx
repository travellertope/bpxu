import { Suspense } from 'react';
import { cookies } from 'next/headers';
import { decodeHtml } from '@/lib/utils';
import { getBPUSession } from '@/lib/auth';
import FavouriteButton from '../FavouriteButton';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

interface MentorSummary {
    id: number;
    display_name: string;
    avatar_url: string;
    industry: string;
    years_of_experience: string;
    skills_separate: string;
    user_bio: string;
    industryfield_of_expertise: string;
    company: string;
    current_role: string;
    average_rating: number;
    review_count: number;
}

function mentorColor(id: number): string {
    const colors = ['#6366f1', '#8b5cf6', '#ec4899', '#3b82f6', '#14b8a6', '#f59e0b', '#ef4444'];
    return colors[id % colors.length];
}

const INDUSTRIES = [
    'Accounting', 'Administration & Office Support', 'Advertising, Arts & Media',
    'Banking & Financial Services', 'Call Centre & Customer Service',
    'Community Services & Development', 'Construction', 'Consulting & Strategy',
    'Education & Training', 'Engineering', 'Farming, Animals & Conservation',
    'Government & Defence', 'Healthcare & Medical', 'Hospitality & Tourism',
    'Human Resources & Recruitment', 'Information & Communication Technology',
    'Insurance & Superannuation', 'Legal', 'Manufacturing, Transport & Logistics',
    'Marketing & Communications', 'Mining, Resources & Energy',
    'Real Estate & Property', 'Retail & Consumer Products', 'Sales',
    'Science & Technology', 'Self Employment', 'Sport & Recreation',
    'Trades & Services', 'Other',
];

function isGravatar(url: string): boolean {
    if (!url) return true;
    if (!url.includes('gravatar.com')) return false;
    return url.includes('d=blank') || url.includes('d=mm') || url.includes('d=mystery');
}

async function MentorGrid({
    search,
    industry,
    page,
    favouritedIds,
    isAuthenticated,
}: {
    search: string;
    industry: string;
    page: number;
    favouritedIds: Set<number>;
    isAuthenticated: boolean;
}) {
    const url = new URL(`${WP_BACKEND_URL}/wp-json/bpu/v1/mentors`);
    url.searchParams.set('per_page', '12');
    url.searchParams.set('page', String(page));
    if (search) url.searchParams.set('search', search);
    if (industry) url.searchParams.set('industry', industry);

    let mentors: MentorSummary[] = [];
    let total = 0;
    let totalPages = 1;
    let fetchError = '';

    try {
        const res = await fetch(url.toString(), { cache: 'no-store' });
        if (res.ok) {
            const data = await res.json();
            mentors = data.mentors || [];
            total = data.total || 0;
            totalPages = data.total_pages || 1;
        } else {
            fetchError = 'Could not load mentors right now.';
        }
    } catch {
        fetchError = 'Could not connect to the server.';
    }

    if (fetchError) {
        return (
            <div className="card card-p text-center text-text-2 text-sm py-10">
                {fetchError}
            </div>
        );
    }

    if (mentors.length === 0) {
        return (
            <div className="card card-p text-center py-10 space-y-2">
                <p className="font-semibold text-text-2">No mentors found</p>
                <p className="text-sm text-text-3">
                    {search || industry
                        ? 'Try adjusting your search or filter.'
                        : 'Check back soon — we are onboarding mentors now.'}
                </p>
            </div>
        );
    }

    return (
        <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
            <p className="text-sm text-text-3">{total} mentor{total !== 1 ? 's' : ''} found</p>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                {mentors.map(m => {
                    const skills = (m.skills_separate || '')
                        .split(',')
                        .map(s => decodeHtml(s.trim()))
                        .filter(Boolean)
                        .slice(0, 3);
                    const color = mentorColor(m.id);
                    const hasPhoto = m.avatar_url && !isGravatar(m.avatar_url);
                    const subtitle = decodeHtml(m.current_role || m.industryfield_of_expertise || m.industry || 'Professional');
                    const companyLine = m.company ? `at ${decodeHtml(m.company)}` : '';
                    const industry = decodeHtml(m.industry);

                    return (
                        <a
                            key={m.id}
                            href={`/paired/mentors/${m.id}`}
                            className="card card-lift flex flex-col"
                            style={{ textDecoration: 'none', color: 'inherit' }}
                        >
                            <div style={{ padding: '24px 24px 0' }} className="flex items-start gap-4">
                                {hasPhoto ? (
                                    <img
                                        src={m.avatar_url}
                                        alt={m.display_name}
                                        className="shrink-0 rounded-full object-cover"
                                        style={{ width: 56, height: 56 }}
                                    />
                                ) : (
                                    <div
                                        className="avatar avatar-md text-white shrink-0"
                                        style={{ background: color }}
                                    >
                                        {m.display_name[0]}
                                    </div>
                                )}
                                <div className="min-w-0 flex-1">
                                    <p className="font-bold truncate leading-tight">{m.display_name}</p>
                                    <p className="text-sm text-text-2 truncate mt-0.5">{subtitle}</p>
                                    {companyLine && (
                                        <p className="text-xs text-text-3 truncate mt-0.5">{companyLine}</p>
                                    )}
                                </div>
                                {isAuthenticated && (
                                    <div className="shrink-0">
                                        <FavouriteButton
                                            mentorId={m.id}
                                            initialFavourited={favouritedIds.has(m.id)}
                                            size={18}
                                        />
                                    </div>
                                )}
                            </div>

                            <div style={{ padding: '16px 24px' }} className="flex-1 flex flex-col gap-3">
                                {(m.industry || m.years_of_experience) && (
                                    <div className="flex flex-wrap gap-x-3 gap-y-1 text-xs text-text-3">
                                        {industry && (
                                            <span className="flex items-center gap-1">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                                                {industry}
                                            </span>
                                        )}
                                        {m.years_of_experience && (
                                            <span className="flex items-center gap-1">
                                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                {m.years_of_experience} yrs
                                            </span>
                                        )}
                                    </div>
                                )}

                                {skills.length > 0 && (
                                    <div className="flex flex-wrap gap-1.5">
                                        {skills.map(s => (
                                            <span key={s} className="badge badge-purple text-xs">{s}</span>
                                        ))}
                                    </div>
                                )}

                                {m.average_rating > 0 && (
                                    <div className="flex items-center gap-1 text-xs">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="#f59e0b" stroke="#f59e0b" strokeWidth="2">
                                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                        </svg>
                                        <span className="font-semibold" style={{ color: '#f59e0b' }}>{m.average_rating.toFixed(1)}</span>
                                        <span className="text-text-3">({m.review_count})</span>
                                    </div>
                                )}
                            </div>

                            <div style={{ padding: '0 24px 24px' }}>
                                <span className="btn btn-outline btn-sm w-full justify-center">
                                    View profile →
                                </span>
                            </div>
                        </a>
                    );
                })}
            </div>

            {totalPages > 1 && (
                <div className="flex items-center justify-center gap-2">
                    {page > 1 && (
                        <a
                            href={`/paired/mentors?page=${page - 1}${search ? `&search=${encodeURIComponent(search)}` : ''}${industry ? `&industry=${encodeURIComponent(industry)}` : ''}`}
                            className="btn btn-outline btn-sm"
                        >
                            ← Previous
                        </a>
                    )}
                    <span className="text-sm text-text-2">Page {page} of {totalPages}</span>
                    {page < totalPages && (
                        <a
                            href={`/paired/mentors?page=${page + 1}${search ? `&search=${encodeURIComponent(search)}` : ''}${industry ? `&industry=${encodeURIComponent(industry)}` : ''}`}
                            className="btn btn-outline btn-sm"
                        >
                            Next →
                        </a>
                    )}
                </div>
            )}
        </div>
    );
}

async function MentorGridWithFavourites({
    search,
    industry,
    page,
}: {
    search: string;
    industry: string;
    page: number;
}) {
    const session = await getBPUSession();
    const isAuthenticated = session.authenticated;

    let favouritedIds = new Set<number>();
    if (isAuthenticated) {
        try {
            const cookieStore = await cookies();
            const jwt = cookieStore.get('bpu_session')?.value || '';
            if (jwt) {
                const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';
                const favRes = await fetch(`${WP}/wp-json/bpu/v1/paired/favourites`, {
                    headers: { 'Authorization': `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
                });
                if (favRes.ok) {
                    const favData = await favRes.json();
                    const favourites: Array<{ mentor_id: number }> = favData.favourites || [];
                    favouritedIds = new Set(favourites.map(f => f.mentor_id));
                }
            }
        } catch {
            // Favourites unavailable
        }
    }

    return (
        <MentorGrid
            search={search}
            industry={industry}
            page={page}
            favouritedIds={favouritedIds}
            isAuthenticated={isAuthenticated}
        />
    );
}

export default async function MentorDirectory({
    searchParams,
}: {
    searchParams: Promise<{ search?: string; industry?: string; page?: string }>;
}) {
    const params = await searchParams;
    const search = params.search || '';
    const industry = params.industry || '';
    const page = Math.max(1, parseInt(params.page || '1', 10));

    return (
        <div className="wrap py-8 fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px' }}>

            <div style={{ textAlign: 'center', display: 'flex', flexDirection: 'column', gap: '12px' }}>
                <h1 className="text-4xl font-extrabold tracking-tight">Browse mentors</h1>
                <p className="text-text-2" style={{ maxWidth: '480px', marginLeft: 'auto', marginRight: 'auto' }}>
                    Connect with experienced Black professionals ready to guide your career.
                </p>
            </div>

            {/* Search & filter bar */}
            <form
                method="GET"
                className="card"
                style={{ padding: '20px 24px', maxWidth: '800px', marginLeft: 'auto', marginRight: 'auto', width: '100%' }}
            >
                <div className="flex flex-col sm:flex-row gap-3 items-stretch">
                    <div className="flex-1 relative">
                        <svg
                            width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"
                            className="absolute left-3 top-1/2 -translate-y-1/2 text-text-3"
                            style={{ pointerEvents: 'none' }}
                        >
                            <circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/>
                        </svg>
                        <input
                            type="text"
                            name="search"
                            defaultValue={search}
                            placeholder="Search by name, role or skill…"
                            className="field-input w-full"
                            style={{ paddingLeft: '36px' }}
                        />
                    </div>
                    <select
                        name="industry"
                        defaultValue={industry}
                        className="field-input"
                        style={{ minWidth: '220px' }}
                    >
                        <option value="">All industries</option>
                        {INDUSTRIES.map(i => (
                            <option key={i} value={i}>{i}</option>
                        ))}
                    </select>
                    <button type="submit" className="btn btn-purple" style={{ whiteSpace: 'nowrap' }}>
                        Search
                    </button>
                </div>
                {(search || industry) && (
                    <div className="mt-3 flex items-center gap-2 text-xs text-text-3">
                        <span>Filtering by:</span>
                        {search && (
                            <span className="badge badge-purple">&quot;{search}&quot;</span>
                        )}
                        {industry && (
                            <span className="badge badge-purple">{industry}</span>
                        )}
                        <a href="/paired/mentors" className="text-brand hover:underline ml-1">Clear all</a>
                    </div>
                )}
            </form>

            <Suspense fallback={
                <div className="text-center text-sm text-text-2 py-12">Loading mentors…</div>
            }>
                <MentorGridWithFavourites search={search} industry={industry} page={page} />
            </Suspense>

        </div>
    );
}
