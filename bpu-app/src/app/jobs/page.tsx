import { Job } from './types';
import SiteHeader from '@/components/SiteHeader';
import JobBoard from './JobBoard';
import { wpJobsUrl, NO_STORE } from '@/lib/wp-jobs';

async function fetchJobs(): Promise<{ jobs: Job[]; total: number }> {
    try {
        const res = await fetch(wpJobsUrl({ per_page: '20' }), NO_STORE);
        if (!res.ok) return { jobs: [], total: 0 };
        const data = await res.json();
        const jobs = Array.isArray(data) ? data : (data.jobs ?? []);
        return { jobs, total: data.total ?? jobs.length };
    } catch {
        return { jobs: [], total: 0 };
    }
}

export const dynamic = 'force-dynamic';
export const fetchCache = 'force-no-store';
export const revalidate = 0;

export const metadata = {
    title: 'Job Board | BPU Portal',
    description: 'Find your next career opportunity. Browse jobs curated for Black professionals in the UK.',
};

export default async function JobsPage() {
    const { jobs, total } = await fetchJobs();

    return (
        <div className="min-h-screen flex flex-col bg-hp-bg text-hp-dark">
            <SiteHeader />

            <main className="flex-1 pt-20">
                {/* Dark hero */}
                <section
                    className="py-16 px-4 text-center"
                    style={{ background: '#111317', borderBottom: '3px solid #111317' }}
                >
                    <div className="max-w-5xl mx-auto">
                        <div
                            className="inline-block text-white text-xs font-bold uppercase px-3 py-1 rounded mb-5 tracking-widest"
                            style={{ background: 'var(--brand)', border: '1px solid rgba(255,255,255,0.25)' }}
                        >
                            Verified Inclusive Employers
                        </div>
                        <h1
                            className="font-black uppercase tracking-tight mb-4 text-white"
                            style={{ fontSize: 'clamp(2.25rem, 5vw, 3.5rem)', lineHeight: 1.05 }}
                        >
                            Find Your Next<br />Opportunity
                        </h1>
                        <p className="text-lg max-w-3xl mx-auto font-medium leading-relaxed" style={{ color: 'rgba(255,255,255,0.7)' }}>
                            Hundreds of jobs from organisations that prioritise diversity, equity, and inclusion —
                            ensuring you have access to opportunities that value your unique skills and experiences.
                        </p>
                    </div>
                </section>

                {/* Board — owns its floating filter and grid */}
                <JobBoard initialJobs={jobs} initialTotal={total} />
            </main>

            <footer className="py-6 text-center text-xs border-t" style={{ borderColor: 'var(--border)', color: 'var(--text-3)' }}>
                © {new Date().getFullYear()} Black Professionals United Kingdom ·{' '}
                <a href="/" className="hover:underline">Back to Portal</a>
            </footer>
        </div>
    );
}
