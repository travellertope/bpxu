import { Suspense } from 'react';

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

async function MentorGrid({
    search,
    industry,
    page,
}: {
    search: string;
    industry: string;
    page: number;
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
                        .map(s => s.trim())
                        .filter(Boolean)
                        .slice(0, 3);
                    const color = mentorColor(m.id);
                    return (
                        <div key={m.id} className="card card-p card-lift flex flex-col gap-4">
                            <div className="flex items-center gap-4">
                                <div
                                    className="avatar avatar-md text-white shrink-0"
                                    style={{ background: color }}
                                >
                                    {m.display_name[0]}
                                </div>
                                <div className="min-w-0">
                                    <p className="font-bold truncate">{m.display_name}</p>
                                    <p className="text-sm text-text-2 truncate">
                                        {m.industryfield_of_expertise || m.industry || 'Professional'}
                                    </p>
                                    {(m.industry || m.years_of_experience) && (
                                        <p className="text-xs text-text-3 mt-0.5">
                                            {[m.industry, m.years_of_experience && `${m.years_of_experience} yrs`]
                                                .filter(Boolean)
                                                .join(' · ')}
                                        </p>
                                    )}
                                </div>
                            </div>

                            {skills.length > 0 && (
                                <div className="flex flex-wrap gap-1.5">
                                    {skills.map(s => (
                                        <span key={s} className="badge badge-purple text-xs">{s}</span>
                                    ))}
                                </div>
                            )}

                            <a
                                href={`/paired/mentors/${m.id}`}
                                className="btn btn-outline btn-sm mt-auto"
                            >
                                View profile →
                            </a>
                        </div>
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
        <div className="wrap py-12 fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px' }}>

            <div style={{ textAlign: 'center', display: 'flex', flexDirection: 'column', gap: '12px' }}>
                <h1 className="text-4xl font-extrabold tracking-tight">Browse mentors</h1>
                <p className="text-text-2" style={{ maxWidth: '480px', marginLeft: 'auto', marginRight: 'auto' }}>
                    Connect with experienced Black professionals ready to guide your career.
                </p>
            </div>

            <form
                method="GET"
                style={{ maxWidth: '640px', marginLeft: 'auto', marginRight: 'auto', width: '100%' }}
            >
                <div className="flex flex-col sm:flex-row gap-3">
                    <input
                        type="text"
                        name="search"
                        defaultValue={search}
                        placeholder="Search by name, role or skill…"
                        className="field-input flex-1"
                    />
                    <select name="industry" defaultValue={industry} className="field-input sm:w-48">
                        <option value="">All industries</option>
                        {INDUSTRIES.map(i => (
                            <option key={i} value={i}>{i}</option>
                        ))}
                    </select>
                    <button type="submit" className="btn btn-purple">Search</button>
                </div>
            </form>

            <Suspense fallback={
                <div className="text-center text-sm text-text-2 py-12">Loading mentors…</div>
            }>
                <MentorGrid search={search} industry={industry} page={page} />
            </Suspense>

        </div>
    );
}
