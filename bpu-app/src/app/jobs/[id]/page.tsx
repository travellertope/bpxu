import { getBPUSession } from '@/lib/auth';
import { Job } from '../types';
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

    return (
        <div className="min-h-screen flex flex-col">
            {/* Topbar */}
            <header className="topbar">
                <div className="topbar-inner">
                    <a href="/" className="topbar-brand"><span>BPU</span> Portal</a>
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

                {/* Job header */}
                <div className="card card-p mb-6">
                    <div className="flex flex-wrap items-start justify-between gap-4">
                        <div>
                            <div className="flex flex-wrap items-center gap-2 mb-2">
                                {isInbound ? (
                                    <span className="badge badge-green">Apply now</span>
                                ) : (
                                    <span className="badge badge-amber">Partner role</span>
                                )}
                                {job.status === 'pending' && (
                                    <span className="badge badge-gray">Pending review</span>
                                )}
                            </div>
                            <h1 className="text-2xl font-bold mb-1">{job.title}</h1>
                            <p className="text-text-2 text-lg">{job.company}</p>
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

                {/* Two-column layout */}
                <div className="flex flex-col lg:flex-row gap-6">
                    {/* Left: Description */}
                    <div className="flex-1 min-w-0">
                        <div className="card card-p">
                            <h2 className="section-title mb-4">Job Description</h2>
                            <div
                                className="prose prose-sm max-w-none text-text leading-relaxed"
                                style={{ fontSize: '14px', lineHeight: '1.7' }}
                                dangerouslySetInnerHTML={{ __html: job.description }}
                            />
                        </div>
                    </div>

                    {/* Right: Sidebar */}
                    <div className="lg:w-80 shrink-0">
                        <div className="card card-p space-y-4 sticky top-20">
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
                                            <a
                                                href={`/login?returnTo=/jobs/${job.id}`}
                                                className="btn btn-amber w-full justify-center"
                                            >
                                                Sign in to apply
                                            </a>
                                            <a
                                                href={`/register?returnTo=/jobs/${job.id}`}
                                                className="btn btn-outline w-full justify-center text-sm"
                                            >
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
                                        <p className="text-sm text-text-3 italic">
                                            Application link not available.
                                        </p>
                                    )}
                                </>
                            )}

                            <div className="divider" />

                            <div className="space-y-1">
                                <p className="text-xs text-text-3">
                                    <strong className="text-text-2">Company:</strong> {job.company}
                                </p>
                                <p className="text-xs text-text-3">
                                    <strong className="text-text-2">Type:</strong> {job.employment_type}
                                </p>
                                <p className="text-xs text-text-3">
                                    <strong className="text-text-2">Industry:</strong> {job.industry}
                                </p>
                                {salary && (
                                    <p className="text-xs text-text-3">
                                        <strong className="text-text-2">Salary:</strong> {salary}
                                    </p>
                                )}
                            </div>
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
