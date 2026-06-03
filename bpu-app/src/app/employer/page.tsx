import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';

export const metadata = {
    title: 'Employer Portal | BPU Jobs',
    description: 'Post jobs and find talent from the BPU community.',
};

export default async function EmployerPage() {
    const session = await getBPUSession();

    // If logged in as employer (has employer role), redirect to dashboard
    if (session.authenticated && session.user) {
        const roles = session.user.roles ?? [];
        if (roles.includes('employer') || roles.includes('administrator')) {
            redirect('/employer/jobs');
        }
    }

    return (
        <div className="min-h-screen flex flex-col">
            <header className="topbar">
                <div className="topbar-inner">
                    <a href="/" className="topbar-brand"><img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="Black Professionals United" /></a>
                    <div className="flex items-center gap-3">
                        <a href="/jobs" className="btn btn-ghost btn-sm">Browse jobs</a>
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
                    className="py-16 text-center"
                    style={{
                        background: 'linear-gradient(135deg, var(--brand-bg) 0%, var(--surface) 100%)',
                        borderBottom: '1px solid var(--border)',
                    }}
                >
                    <div className="wrap">
                        <h1 className="text-4xl font-extrabold tracking-tight mb-3">
                            Hire from the BPU community
                        </h1>
                        <p className="text-text-2 text-lg max-w-xl mx-auto mb-8">
                            Reach thousands of Black professionals in the UK. Post jobs, track applications, and find the talent you need.
                        </p>
                        <div className="flex flex-col sm:flex-row items-center justify-center gap-4">
                            <a href="/employer/register" className="btn btn-amber btn-lg">
                                Register as an employer
                            </a>
                            <a href="/login?returnTo=/employer/jobs" className="btn btn-outline btn-lg">
                                Sign in to existing account
                            </a>
                        </div>
                    </div>
                </section>

                {/* Features */}
                <section className="wrap py-14">
                    <h2 className="text-2xl font-bold text-center mb-10">
                        Why post on BPU?
                    </h2>
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        {[
                            {
                                icon: '🎯',
                                title: 'Targeted audience',
                                desc: 'Your job reaches professionals who are actively building their careers in the UK.',
                            },
                            {
                                icon: '📊',
                                title: 'Real-time analytics',
                                desc: 'Track impressions, clicks, and applications from your employer dashboard.',
                            },
                            {
                                icon: '⚡',
                                title: 'Easy to post',
                                desc: 'Create and publish a job listing in minutes. Both direct and partner roles supported.',
                            },
                        ].map(f => (
                            <div key={f.title} className="card card-p space-y-3">
                                <div className="text-3xl">{f.icon}</div>
                                <p className="font-semibold">{f.title}</p>
                                <p className="text-sm text-text-2 leading-relaxed">{f.desc}</p>
                            </div>
                        ))}
                    </div>
                </section>
            </main>

            <footer className="py-6 text-center text-xs text-text-3 border-t border-border">
                © {new Date().getFullYear()} Black Professionals United ·{' '}
                <a href="/" className="hover:underline">Back to Portal</a>
            </footer>
        </div>
    );
}
