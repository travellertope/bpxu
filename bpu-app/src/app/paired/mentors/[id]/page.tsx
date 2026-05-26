import { getBPUSession } from '@/lib/auth';
import BookingForm from './BookingForm';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

function mentorColor(id: number): string {
    const colors = ['#6366f1', '#8b5cf6', '#ec4899', '#3b82f6', '#14b8a6', '#f59e0b', '#ef4444'];
    return colors[id % colors.length];
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
    const profile = (mentor.profile as Record<string, string>) || {};
    const color = mentorColor(mentorId);
    const title = profile.industryfield_of_expertise || profile.industry || 'Professional';
    const industry = profile.industry || '';
    const exp = profile.years_of_experience ? `${profile.years_of_experience} yrs experience` : '';
    const bio = profile.user_bio || '';
    const skills = (profile.skills_separate || '')
        .split(',')
        .map(s => s.trim())
        .filter(Boolean);
    const expertiseLine = [industry, profile.industryfield_of_expertise]
        .filter((v, i, a) => v && a.indexOf(v) === i)
        .join(' · ');

    return (
        <div className="wrap py-12 fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px' }}>

            {/* ── Profile header ─────────────────────────────── */}
            <div className="card card-p-lg">
                <div className="flex flex-col md:flex-row gap-8 items-center md:items-start">
                    <div
                        className="avatar avatar-xl text-white shrink-0"
                        style={{ background: color }}
                    >
                        {name[0]}
                    </div>

                    <div className="flex-1 text-center md:text-left space-y-4">
                        <div>
                            <h1 className="text-3xl font-extrabold">{name}</h1>
                            <p className="text-lg text-text-2 mt-1">{title}</p>
                            {(industry || exp) && (
                                <p className="text-sm text-text-3 mt-1">
                                    {[industry, exp].filter(Boolean).join(' · ')}
                                </p>
                            )}
                        </div>
                        {skills.length > 0 && (
                            <div className="flex flex-wrap gap-2 justify-center md:justify-start">
                                {skills.map(s => (
                                    <span key={s} className="badge badge-purple">{s}</span>
                                ))}
                            </div>
                        )}
                    </div>

                    <div className="shrink-0 w-full md:w-auto text-center">
                        <p className="text-xs text-text-3 mt-1">Responds within 24 h</p>
                    </div>
                </div>
            </div>

            {/* ── Body ───────────────────────────────────────── */}
            <div className="grid grid-cols-1 md:grid-cols-3 gap-6">

                <div className="md:col-span-2 space-y-6">
                    {bio ? (
                        <div className="card card-p space-y-3">
                            <p className="section-title">About</p>
                            <p className="text-sm text-text-2 leading-relaxed">{bio}</p>
                        </div>
                    ) : (
                        <div className="card card-p space-y-3">
                            <p className="section-title">About</p>
                            <p className="text-sm text-text-3">This mentor hasn&apos;t added a bio yet.</p>
                        </div>
                    )}
                    {expertiseLine && (
                        <div className="card card-p space-y-3">
                            <p className="section-title">Areas of expertise</p>
                            <p className="text-sm text-text-2">{expertiseLine}</p>
                        </div>
                    )}
                </div>

                <div>
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
