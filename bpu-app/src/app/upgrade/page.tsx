import { getBPUSession } from '@/lib/auth';

const PRO_FEATURES = [
    {
        icon: '🤖',
        title: 'AI Job Matching',
        desc: 'Upload your CV and our system automatically matches you to new roles. Get email alerts when a high-match job is posted.',
    },
    {
        icon: '📊',
        title: 'AI Compatibility Scores',
        desc: 'See a percentage match score on every mentor profile and your job recommendations.',
    },
    {
        icon: '🎯',
        title: 'Curated Mentor Carousel',
        desc: 'PAIRED shows you mentors ranked by AI-computed compatibility — not random suggestions.',
    },
    {
        icon: '📄',
        title: 'Human CV Review',
        desc: 'Request a professional critique from a BPU recruiter. Receive a scored, written review in your dashboard.',
    },
    {
        icon: '📬',
        title: 'Weekly Job Digest',
        desc: 'A personalised email every Monday with your top 5 job matches, interview tips, and career resources.',
    },
];

const FREE_FEATURES = [
    'Browse the full mentor directory',
    'View any mentor profile',
    'Book sessions with mentors',
    'Manage your session history',
    'Access all BPU courses',
    'View upcoming events',
];

export default async function UpgradePage() {
    const session = await getBPUSession();
    const isPro = session.user?.is_pro ?? false;

    return (
        <div className="min-h-screen flex flex-col">
            <header className="topbar">
                <div className="topbar-inner">
                    <a href="/" className="topbar-brand"><span>BPU</span> Portal</a>
                    <div className="flex items-center gap-3">
                        {session.authenticated
                            ? <a href="/" className="btn btn-ghost btn-sm">← Back to dashboard</a>
                            : <a href="/login" className="btn btn-amber btn-sm">Sign in</a>
                        }
                    </div>
                </div>
            </header>

            <main className="flex-1 wrap py-12 space-y-12">

                {/* Hero */}
                <div className="text-center space-y-4 max-w-2xl mx-auto">
                    {isPro && (
                        <span className="badge badge-green text-sm px-4 py-1.5">You&apos;re already a Pro member ✓</span>
                    )}
                    <h1 className="text-4xl font-bold">Upgrade to BPU Pro</h1>
                    <p className="text-text-2 text-lg leading-relaxed">
                        Unlock AI-powered job matching, human CV reviews, and personalised mentor recommendations
                        — everything you need to accelerate your career.
                    </p>
                    {!isPro && (
                        <a
                            href="https://blackprofessionals.uk/membership"
                            target="_blank"
                            rel="noopener noreferrer"
                            className="btn btn-amber"
                            style={{ fontSize: '1rem', padding: '0.75rem 2rem' }}
                        >
                            Get BPU Pro →
                        </a>
                    )}
                </div>

                {/* Pro features grid */}
                <div>
                    <h2 className="text-xl font-bold mb-6 text-center">What you get with Pro</h2>
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        {PRO_FEATURES.map(f => (
                            <div key={f.title} className="card card-p space-y-3">
                                <div className="text-3xl">{f.icon}</div>
                                <p className="font-semibold">{f.title}</p>
                                <p className="text-sm text-text-2 leading-relaxed">{f.desc}</p>
                            </div>
                        ))}
                    </div>
                </div>

                {/* Free vs Pro comparison */}
                <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 max-w-3xl mx-auto w-full">
                    <div className="card card-p space-y-4">
                        <p className="font-bold text-text-2 uppercase text-xs tracking-wide">Free membership</p>
                        <ul className="space-y-2">
                            {FREE_FEATURES.map(f => (
                                <li key={f} className="flex items-center gap-2 text-sm">
                                    <span className="text-brand">✓</span> {f}
                                </li>
                            ))}
                        </ul>
                    </div>
                    <div className="card card-p space-y-4" style={{ borderColor: 'var(--brand)' }}>
                        <p className="font-bold uppercase text-xs tracking-wide" style={{ color: 'var(--brand)' }}>Pro membership</p>
                        <p className="text-xs text-text-2">Everything in Free, plus:</p>
                        <ul className="space-y-2">
                            {PRO_FEATURES.map(f => (
                                <li key={f.title} className="flex items-center gap-2 text-sm">
                                    <span className="text-brand">★</span> {f.title}
                                </li>
                            ))}
                        </ul>
                        {!isPro && (
                            <a
                                href="https://blackprofessionals.uk/membership"
                                target="_blank"
                                rel="noopener noreferrer"
                                className="btn btn-amber btn-sm w-full text-center"
                            >
                                Upgrade now →
                            </a>
                        )}
                    </div>
                </div>

            </main>
        </div>
    );
}
