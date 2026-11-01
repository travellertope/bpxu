import { notFound } from 'next/navigation';
import Link from 'next/link';
import { getBPUSession } from '@/lib/auth';
import { PAGES } from './content';

export function generateStaticParams() {
    return Object.keys(PAGES).map(slug => ({ slug }));
}

export async function generateMetadata({
    params,
}: {
    params: Promise<{ slug: string }>;
}) {
    const { slug } = await params;
    const page = PAGES[slug];
    if (!page) return {};
    return {
        title: `${page.title} | BPU Membership`,
        description: page.description,
    };
}

const STEP_ICONS = [
    /* 1 */
    <svg key="1" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <line x1="12" y1="5" x2="12" y2="19"/><polyline points="19 12 12 19 5 12"/>
    </svg>,
    /* 2 */
    <svg key="2" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <polyline points="20 6 9 17 4 12"/>
    </svg>,
    /* 3 */
    <svg key="3" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <circle cx="12" cy="12" r="10"/><polyline points="12 8 12 12 14 14"/>
    </svg>,
    /* 4 */
    <svg key="4" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>
    </svg>,
];

const FEATURE_ICONS = [
    <svg key="a" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <polyline points="20 6 9 17 4 12"/>
    </svg>,
    <svg key="b" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <polyline points="20 6 9 17 4 12"/>
    </svg>,
    <svg key="c" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <polyline points="20 6 9 17 4 12"/>
    </svg>,
    <svg key="d" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
        <polyline points="20 6 9 17 4 12"/>
    </svg>,
];

export default async function MembershipSlugPage({
    params,
}: {
    params: Promise<{ slug: string }>;
}) {
    const { slug } = await params;
    const content = PAGES[slug];
    if (!content) notFound();

    const session = await getBPUSession();
    const isAuth = session.authenticated;

    const primaryCta = isAuth
        ? { label: content.cta.label, href: content.cta.href }
        : content.guestCta
            ? { label: content.guestCta.label, href: content.guestCta.href }
            : { label: 'Sign in to Get Started', href: `/login?returnTo=/membership/${slug}` };

    return (
        <div className="min-h-screen flex flex-col" style={{ background: 'var(--bg)' }}>
            {/* Topbar */}
            <header className="topbar">
                <div className="topbar-inner">
                    <a href="/" className="topbar-brand">
                        <img
                            src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png"
                            alt="Black Professionals United"
                        />
                    </a>
                    <div className="flex items-center gap-3">
                        <Link href="/membership" className="btn btn-ghost btn-sm">
                            ← Membership
                        </Link>
                        {isAuth
                            ? <a href="/" className="btn btn-ghost btn-sm">Dashboard</a>
                            : <a href="/login" className="btn btn-amber btn-sm">Sign in</a>
                        }
                    </div>
                </div>
            </header>

            <main className="flex-1">
                {/* Dark hero */}
                <section
                    className="py-16 px-4 text-center"
                    style={{ background: '#111317', borderBottom: '3px solid #111317' }}
                >
                    <div className="max-w-4xl mx-auto">
                        <div
                            className="inline-block text-white text-xs font-bold uppercase px-3 py-1 rounded mb-5 tracking-widest"
                            style={{ background: 'var(--brand)', border: '1px solid rgba(255,255,255,0.25)' }}
                        >
                            {content.badge}
                        </div>
                        <h1
                            className="font-black uppercase tracking-tight mb-5 text-white"
                            style={{ fontSize: 'clamp(2rem, 5vw, 3.25rem)', lineHeight: 1.05 }}
                        >
                            {content.title}
                        </h1>
                        <p
                            className="text-lg font-medium leading-relaxed mx-auto"
                            style={{ color: 'rgba(255,255,255,0.7)', maxWidth: '52ch' }}
                        >
                            {content.description}
                        </p>

                        {/* Hero CTA */}
                        <div className="flex flex-wrap gap-3 justify-center mt-8">
                            <Link
                                href={primaryCta.href}
                                className="btn btn-amber"
                                style={{ border: '2px solid rgba(255,255,255,0.4)', fontSize: 14, padding: '10px 24px' }}
                            >
                                {primaryCta.label}
                            </Link>
                            {!isAuth && (
                                <Link href="/register" className="btn btn-outline" style={{ color: '#fff', borderColor: 'rgba(255,255,255,0.3)', background: 'transparent' }}>
                                    Create Free Account
                                </Link>
                            )}
                        </div>
                    </div>
                </section>

                {/* How it works */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div className="mb-10">
                        <p className="text-xs font-black uppercase tracking-widest mb-2" style={{ color: 'var(--brand)' }}>
                            The Process
                        </p>
                        <h2 className="font-black uppercase tracking-tight text-2xl sm:text-3xl" style={{ color: '#111317' }}>
                            How It Works
                        </h2>
                    </div>

                    <div className={`grid gap-6 ${content.steps.length === 4 ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4' : 'grid-cols-1 sm:grid-cols-3'}`}>
                        {content.steps.map((step, i) => (
                            <div
                                key={step.title}
                                className="relative flex flex-col gap-4 p-6"
                                style={{
                                    background: '#fff',
                                    border: '2px solid #111317',
                                    borderRadius: 12,
                                    boxShadow: '4px 4px 0px 0px #001478',
                                }}
                            >
                                {/* Step number */}
                                <div className="flex items-center gap-3">
                                    <div
                                        className="shrink-0 flex items-center justify-center rounded-lg font-black text-white"
                                        style={{
                                            width: 40, height: 40,
                                            background: 'var(--brand)',
                                            border: '2px solid #111317',
                                            fontSize: 16,
                                        }}
                                    >
                                        {i + 1}
                                    </div>
                                    {/* Connector line (not on last step) */}
                                    {i < content.steps.length - 1 && (
                                        <div className="hidden lg:block flex-1 h-0.5" style={{ background: '#111317', opacity: 0.15 }} />
                                    )}
                                </div>
                                <h3 className="font-black text-sm uppercase tracking-tight" style={{ color: '#111317' }}>
                                    {step.title}
                                </h3>
                                <p className="text-sm font-medium leading-relaxed" style={{ color: 'var(--text-2)' }}>
                                    {step.description}
                                </p>
                                {/* Corner icon */}
                                <div className="absolute top-5 right-5 opacity-10" style={{ color: '#111317' }}>
                                    {STEP_ICONS[i] ?? STEP_ICONS[0]}
                                </div>
                            </div>
                        ))}
                    </div>
                </section>

                {/* What you get */}
                <section style={{ background: '#111317', borderTop: '2px solid #111317', borderBottom: '2px solid #111317' }}>
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                        <div className="mb-10">
                            <p className="text-xs font-black uppercase tracking-widest mb-2" style={{ color: 'var(--brand)' }}>
                                Benefits
                            </p>
                            <h2 className="font-black uppercase tracking-tight text-2xl sm:text-3xl text-white">
                                What You Get
                            </h2>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            {content.features.map((feature, i) => (
                                <div
                                    key={feature.title}
                                    className="flex gap-4 p-5"
                                    style={{
                                        background: 'rgba(255,255,255,0.06)',
                                        border: '1px solid rgba(255,255,255,0.12)',
                                        borderRadius: 12,
                                    }}
                                >
                                    <div
                                        className="shrink-0 flex items-center justify-center rounded-lg"
                                        style={{
                                            width: 38, height: 38,
                                            background: 'var(--brand)',
                                            border: '1px solid rgba(255,255,255,0.2)',
                                            color: '#fff',
                                        }}
                                    >
                                        {FEATURE_ICONS[i] ?? FEATURE_ICONS[0]}
                                    </div>
                                    <div>
                                        <h3 className="font-black text-sm uppercase tracking-tight mb-1 text-white">
                                            {feature.title}
                                        </h3>
                                        <p className="text-sm font-medium leading-relaxed" style={{ color: 'rgba(255,255,255,0.6)' }}>
                                            {feature.description}
                                        </p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* CTA strip */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
                    <div
                        className="rounded-2xl px-8 py-12"
                        style={{
                            background: '#fff',
                            border: '2px solid #111317',
                            boxShadow: '6px 6px 0px 0px #001478',
                        }}
                    >
                        <p className="text-xs font-black uppercase tracking-widest mb-3" style={{ color: 'var(--brand)' }}>
                            Get Started
                        </p>
                        <h2 className="font-black uppercase tracking-tight text-2xl sm:text-3xl mb-4" style={{ color: '#111317' }}>
                            Ready to Begin?
                        </h2>
                        <p className="text-base font-medium mb-8 mx-auto" style={{ color: 'var(--text-2)', maxWidth: '44ch' }}>
                            {isAuth
                                ? `You're already a BPU member — jump straight into ${content.title}.`
                                : `Join BPU for free and unlock access to ${content.title} and all our other member services.`}
                        </p>
                        <div className="flex flex-wrap gap-4 justify-center">
                            <Link
                                href={primaryCta.href}
                                className="btn btn-amber btn-lg"
                                style={{ border: '2px solid #111317', boxShadow: '4px 4px 0px 0px #001478' }}
                            >
                                {primaryCta.label}
                            </Link>
                            {!isAuth && (
                                <Link href="/register" className="btn btn-outline btn-lg">
                                    Create Free Account
                                </Link>
                            )}
                        </div>

                        {/* Back to membership */}
                        <p className="mt-8 text-xs" style={{ color: 'var(--text-3)' }}>
                            Looking for something else?{' '}
                            <Link href="/membership" className="font-bold underline" style={{ color: 'var(--text-2)' }}>
                                Browse all membership services →
                            </Link>
                        </p>
                    </div>
                </section>
            </main>

            <footer className="py-6 text-center text-xs border-t" style={{ borderColor: 'var(--border)', color: 'var(--text-3)' }}>
                © {new Date().getFullYear()} Black Professionals United ·{' '}
                <a href="/" className="hover:underline">Back to Portal</a>
            </footer>
        </div>
    );
}
