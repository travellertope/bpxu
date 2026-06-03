import { getBPUSession } from '@/lib/auth';
import { Job } from './types';
import JobBoard from './JobBoard';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

async function fetchJobs(): Promise<Job[]> {
    try {
        const res = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/jobs`, {
            cache: 'no-store',
        });
        if (!res.ok) return [];
        const data = await res.json();
        return Array.isArray(data) ? data : (data.jobs ?? []);
    } catch {
        return [];
    }
}

export const metadata = {
    title: 'Job Board | BPU Portal',
    description: 'Find your next career opportunity. Browse jobs curated for Black professionals in the UK.',
};

export default async function JobsPage() {
    const [session, jobs] = await Promise.all([getBPUSession(), fetchJobs()]);

    return (
        <div className="min-h-screen flex flex-col">
            {/* Topbar */}
            <header className="topbar">
                <div className="topbar-inner">
                    <a href="/" className="topbar-brand"><img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="Black Professionals United" /></a>
                    <div className="flex items-center gap-3">
                        <a href="/employer" className="btn btn-ghost btn-sm">
                            Post a job
                        </a>
                        {session.authenticated
                            ? <a href="/" className="btn btn-ghost btn-sm">← Dashboard</a>
                            : <a href="/login" className="btn btn-amber btn-sm">Sign in</a>
                        }
                    </div>
                </div>
            </header>

            <main className="flex-1">
                {/* Hero */}
                <section
                    className="py-14 text-center"
                    style={{
                        background: 'linear-gradient(135deg, var(--brand-bg) 0%, var(--surface) 100%)',
                        borderBottom: '1px solid var(--border)',
                    }}
                >
                    <div className="wrap">
                        <h1 className="text-4xl font-extrabold tracking-tight mb-3">
                            Find your next opportunity
                        </h1>
                        <p className="text-text-2 text-lg max-w-xl mx-auto">
                            Jobs curated for Black professionals in the UK — both direct applications and partner employer roles.
                        </p>
                    </div>
                </section>

                {/* Job board */}
                <section className="wrap py-10">
                    <JobBoard initialJobs={jobs} />
                </section>
            </main>

            <footer className="py-6 text-center text-xs text-text-3 border-t border-border">
                © {new Date().getFullYear()} Black Professionals United · <a href="/" className="hover:underline">Back to Portal</a>
            </footer>
        </div>
    );
}
