import { getBPUSession } from '@/lib/auth';
import { Job, Employer } from '../types';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import OutboundApplyButton from './OutboundApplyButton';
import ApplyWizardTrigger from './ApplyWizardTrigger';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

async function fetchJob(id: string, skipImpression = false): Promise<Job | null> {
    try {
        const qs = skipImpression ? '?skip_impression=1' : '';
        const res = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/jobs/${id}${qs}`, {
            cache: 'no-store',
        });
        if (res.status === 404) return null;
        if (!res.ok) return null;
        const data = await res.json();
        return data.job ?? null;
    } catch {
        return null;
    }
}

function formatSalary(min?: number, max?: number): string | null {
    if (!min && !max) return null;
    const fmt = (n: number) =>
        n >= 1000 ? `£${(n / 1000).toFixed(0)}k` : `£${n.toLocaleString()}`;
    if (min && max) return `${fmt(min)} – ${fmt(max)}`;
    if (min) return `From ${fmt(min)}`;
    if (max) return `Up to ${fmt(max)}`;
    return null;
}

function formatDate(dateStr: string): string {
    try {
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'long', year: 'numeric' });
    } catch {
        return dateStr;
    }
}

function ApplyCard({ job, isInbound, session, employer, salary }: {
    job: Job;
    isInbound: boolean;
    session: Awaited<ReturnType<typeof import('@/lib/auth').getBPUSession>>;
    employer: Employer | null;
    salary: string | null;
}) {
    return (
        <div className="card card-p space-y-4">
            <h2 className="section-title">Apply for this role</h2>

            {isInbound ? (
                <>
                    {session.authenticated ? (
                        <ApplyWizardTrigger job={job} user={session.user!} />
                    ) : (
                        <div className="space-y-3">
                            <p className="text-sm text-text-2">
                                You need to be signed in to apply for this role.
                            </p>
                            <a href={`/login?returnTo=/jobs/${job.id}`} className="btn btn-amber w-full justify-center">
                                Sign in to apply
                            </a>
                            <a href={`/register?returnTo=/jobs/${job.id}`} className="btn btn-outline w-full justify-center text-sm">
                                Create a free account
                            </a>
                        </div>
                    )}
                </>
            ) : (
                <>
                    <div className="alert alert-amber text-sm">
                        This role is hosted by a partner employer.
                    </div>
                    <p className="text-sm text-text-2">
                        Clicking below will take you to the employer&apos;s site to complete your application.
                    </p>
                    {job.apply_url ? (
                        <OutboundApplyButton jobId={job.id} />
                    ) : (
                        <p className="text-sm text-text-3 italic">Application link not available.</p>
                    )}
                </>
            )}

            <div className="divider" />

            <div className="space-y-1.5">
                <p className="text-xs text-text-3">
                    <strong className="text-text-2">Company:</strong>{' '}
                    {employer?.website
                        ? <a href={employer.website} target="_blank" rel="noopener noreferrer" className="hover:underline text-brand">{job.company}</a>
                        : job.company
                    }
                </p>
                <p className="text-xs text-text-3"><strong className="text-text-2">Type:</strong> {job.employment_type}</p>
                <p className="text-xs text-text-3"><strong className="text-text-2">Industry:</strong> {job.industry}</p>
                {salary && <p className="text-xs text-text-3"><strong className="text-text-2">Salary:</strong> {salary}</p>}
                {job.remote && <p className="text-xs text-text-3"><strong className="text-text-2">Location:</strong> Remote</p>}
                {employer?.twitter && (
                    <p className="text-xs text-text-3">
                        <strong className="text-text-2">Twitter/X:</strong>{' '}
                        <a href={`https://x.com/${employer.twitter.replace(/^@/, '')}`} target="_blank" rel="noopener noreferrer" className="hover:underline text-brand">
                            @{employer.twitter.replace(/^@/, '')}
                        </a>
                    </p>
                )}
            </div>
        </div>
    );
}

export async function generateMetadata({ params }: { params: Promise<{ id: string }> }) {
    const { id } = await params;
    const job = await fetchJob(id, true);
    if (!job) return { title: 'Job not found | BPU Portal' };
    return {
        title: `${job.title} at ${job.company} | BPU Jobs`,
        description: `${job.employment_type} role at ${job.company} in ${job.location}.`,
    };
}

export default async function JobDetailPage({ params }: { params: Promise<{ id: string }> }) {
    const { id } = await params;
    const [session, job] = await Promise.all([getBPUSession(), fetchJob(id)]);

    if (!job) notFound();

    const salary = formatSalary(job.salary_min, job.salary_max);
    const isInbound = job.job_type === 'inbound';
    const employer: Employer | null = job.employer ?? null;

    const hasDescription  = !!job.description?.trim();
    const hasAboutSection = !!(employer && (employer.description || employer.video));

    return (
        <div className="min-h-screen flex flex-col">
            {/* Topbar */}
            <header className="topbar">
                <div className="topbar-inner">
                    <a href="/" className="topbar-brand"><img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="Black Professionals United" /></a>
                    <div className="flex items-center gap-3">
                        {session.authenticated
                            ? <a href="/" className="btn btn-ghost btn-sm">← Dashboard</a>
                            : <a href="/login" className="btn btn-amber btn-sm">Sign in</a>
                        }
                    </div>
                </div>
            </header>

            <main className="flex-1 wrap py-8">
                {/* Back link */}
                <Link
                    href="/jobs"
                    className="inline-flex items-center gap-1.5 text-sm text-text-2 hover:text-text mb-6"
                >
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/>
                    </svg>
                    Back to job board
                </Link>

                {/* ── Always two-column: left content + right apply card ── */}
                <div className="flex flex-col lg:flex-row gap-6 items-start">

                    {/* Left: Job header + Description + About Company */}
                    <div className="flex-1 min-w-0 space-y-6">

                        {/* Job header */}
                        <div className="card card-p">
                            <div className="flex flex-wrap items-start gap-4 mb-4">
                                {/* Company logo */}
                                {employer?.logo_url ? (
                                    <div
                                        className="shrink-0 rounded-xl overflow-hidden border border-border bg-surface"
                                        style={{ width: 72, height: 72 }}
                                    >
                                        {/* eslint-disable-next-line @next/next/no-img-element */}
                                        <img
                                            src={employer.logo_url}
                                            alt={employer.name}
                                            style={{ width: '100%', height: '100%', objectFit: 'contain' }}
                                        />
                                    </div>
                                ) : (
                                    <div
                                        className="shrink-0 rounded-xl flex items-center justify-center text-xl font-bold border border-border"
                                        style={{ width: 72, height: 72, background: 'var(--brand-bg)', color: 'var(--brand)' }}
                                    >
                                        {job.company.split(/\s+/).slice(0, 2).map(w => w[0]?.toUpperCase() ?? '').join('')}
                                    </div>
                                )}
                                <div className="flex-1 min-w-0">
                                    <div className="flex flex-wrap items-center gap-2 mb-2">
                                        {isInbound ? (
                                            <span className="badge badge-green">Apply now</span>
                                        ) : (
                                            <span className="badge badge-amber">Partner role</span>
                                        )}
                                        {job.remote && <span className="badge badge-gray">Remote</span>}
                                        {job.featured && <span className="badge badge-amber">Featured</span>}
                                        {job.filled && <span className="badge badge-gray">Position filled</span>}
                                        {job.status === 'pending' && (
                                            <span className="badge badge-gray">Pending review</span>
                                        )}
                                    </div>
                                    <h1 className="text-2xl font-bold mb-1">{job.title}</h1>
                                    <p className="text-text-2 text-lg">{job.company}</p>
                                    {employer?.tagline && (
                                        <p className="text-sm text-text-3 mt-0.5">{employer.tagline}</p>
                                    )}
                                </div>
                            </div>

                            <div className="divider my-4" />

                            <div className="flex flex-wrap gap-x-6 gap-y-2 text-sm text-text-2">
                                <span className="flex items-center gap-1.5">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                                    </svg>
                                    {job.location}
                                </span>
                                <span className="flex items-center gap-1.5">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                    </svg>
                                    {job.employment_type}
                                </span>
                                <span className="flex items-center gap-1.5">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                                    </svg>
                                    {job.industry}
                                </span>
                                {salary && (
                                    <span className="flex items-center gap-1.5">
                                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                            <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
                                        </svg>
                                        {salary}
                                    </span>
                                )}
                                <span className="flex items-center gap-1.5 text-text-3">
                                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                                    </svg>
                                    Posted {formatDate(job.date_posted)}
                                </span>
                                {job.expires && (
                                    <span className="flex items-center gap-1.5 text-text-3">
                                        Closes {formatDate(job.expires)}
                                    </span>
                                )}
                            </div>
                        </div>
                        {hasDescription && (
                            <div className="card card-p">
                                <h2 className="section-title mb-4">Job Description</h2>
                                <div
                                    className="job-description"
                                    dangerouslySetInnerHTML={{ __html: job.description }}
                                />
                            </div>
                        )}

                        {hasAboutSection && (
                            <div className="card card-p">
                                <div className="flex items-center gap-3 mb-4">
                                    {employer!.logo_url && (
                                        <div
                                            className="shrink-0 rounded-lg overflow-hidden border border-border bg-surface"
                                            style={{ width: 40, height: 40 }}
                                        >
                                            {/* eslint-disable-next-line @next/next/no-img-element */}
                                            <img src={employer!.logo_url} alt={employer!.name} style={{ width: '100%', height: '100%', objectFit: 'contain' }} />
                                        </div>
                                    )}
                                    <h2 className="section-title">About {employer!.name}</h2>
                                </div>
                                {employer!.description && (
                                    <div
                                        className="job-description mb-4"
                                        dangerouslySetInnerHTML={{ __html: employer!.description }}
                                    />
                                )}
                                {employer!.video && (
                                    <div className="mt-4 rounded-lg overflow-hidden" style={{ aspectRatio: '16/9' }}>
                                        <iframe
                                            src={employer!.video}
                                            className="w-full h-full"
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowFullScreen
                                            title={`${employer!.name} video`}
                                        />
                                    </div>
                                )}
                            </div>
                        )}

                        {!hasDescription && !hasAboutSection && (
                            <div className="card card-p text-sm text-text-3 italic">
                                No additional details provided for this role.
                            </div>
                        )}
                    </div>

                    {/* Right: Apply card — sticks to top */}
                    <div className="lg:w-80 shrink-0 w-full">
                        <div className="sticky top-20">
                            <ApplyCard
                                job={job}
                                isInbound={isInbound}
                                session={session}
                                employer={employer}
                                salary={salary}
                            />
                        </div>
                    </div>
                </div>
            </main>

            <footer className="py-6 text-center text-xs text-text-3 border-t border-border mt-8">
                © {new Date().getFullYear()} Black Professionals United &middot;{' '}
                <Link href="/jobs" className="hover:underline">Job Board</Link>
                {' · '}
                <a href="/" className="hover:underline">Portal</a>
            </footer>
        </div>
    );
}
