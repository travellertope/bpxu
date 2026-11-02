import { getBPUSession } from '@/lib/auth';
import Link from 'next/link';
import SiteHeader from '@/components/SiteHeader';
import InitiativeGrid from './InitiativeGrid';

export const metadata = {
    title: 'Membership | Black Professionals United',
    description: 'Discover a community that understands your unique journey as a Black professional in the UK. Access our full suite of career tools and support.',
};

const INITIATIVES = [
    {
        title: 'Work Placements',
        description: 'Combining the best of two groundbreaking initiatives — the Black Interns Programme and the Black Talents Programme — to create a transformative experience that will shape the trajectory of your future.',
        cta: 'Explore Opportunities',
        href: '/membership/work-placement',
        icon: (
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <rect x="2" y="7" width="20" height="14" rx="2"/>
                <path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/>
            </svg>
        ),
    },
    {
        title: 'Job Board',
        description: 'Find exclusive job opportunities from organisations that prioritise diversity, equity, and inclusion. Our job board connects you with employers who actively seek diverse talent.',
        cta: 'Browse Jobs',
        href: '/jobs',
        icon: (
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <circle cx="11" cy="11" r="8"/>
                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
        ),
    },
    {
        title: 'Interview Prep',
        description: 'Be equipped with the tools and knowledge to excel in interviews and stand out as a top candidate. Our members receive a personalised approach tailored to their specific needs and career aspirations.',
        cta: 'Start Preparing',
        href: '/membership/interview-prep',
        icon: (
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
            </svg>
        ),
    },
    {
        title: 'CV Clinic',
        description: 'Your CV is a critical tool in your job search. Get personalised feedback and guidance from our professionals to create a standout CV that highlights your skills and accomplishments.',
        cta: 'Request CV Feedback',
        href: '/membership/cv-clinic',
        icon: (
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
            </svg>
        ),
    },
    {
        title: 'Mentorship Programme',
        description: 'Gain valuable insights and guidance from experienced professionals. Connect with mentors who are eager to support your growth, development, and long-term career ambitions.',
        cta: 'Join the Programme',
        href: '/membership/mentorship',
        icon: (
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        ),
    },
    {
        title: 'Professional & Networking Events',
        description: 'Continuous growth is essential for career advancement. Attend our engaging workshops, seminars, and networking events to acquire new skills and expand your professional connections.',
        cta: 'See Upcoming Events',
        href: '/events',
        icon: (
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <rect x="3" y="4" width="18" height="18" rx="2"/>
                <line x1="16" y1="2" x2="16" y2="6"/>
                <line x1="8" y1="2" x2="8" y2="6"/>
                <line x1="3" y1="10" x2="21" y2="10"/>
            </svg>
        ),
    },
    {
        title: 'Professional Development Courses',
        description: 'Whether you seek career advancement, a new path, or entrepreneurial ventures, continuous learning is the key to success. Propel your career to new heights with our free accredited courses.',
        cta: 'Start Learning',
        href: '/courses',
        icon: (
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M22 10v6M2 10l10-5 10 5-10 5z"/>
                <path d="M6 12v5c3 3 9 3 12 0v-5"/>
            </svg>
        ),
    },
    {
        title: 'General Support',
        description: 'Get expert advice on your membership, career opportunities, and employability support. Connect with like-minded individuals who understand the unique challenges faced by Black professionals.',
        cta: 'Access Member Support',
        href: '/membership/support',
        icon: (
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <circle cx="12" cy="12" r="4"/>
                <line x1="4.93" y1="4.93" x2="9.17" y2="9.17"/>
                <line x1="14.83" y1="14.83" x2="19.07" y2="19.07"/>
                <line x1="14.83" y1="9.17" x2="19.07" y2="4.93"/>
                <line x1="4.93" y1="19.07" x2="9.17" y2="14.83"/>
            </svg>
        ),
    },
    {
        title: 'Ambassadorship',
        description: 'A special initiative for members passionate about empowering others and supporting our community\'s growth. As our membership grows, so does the need for bespoke support and guidance for fellow members.',
        cta: 'Become a BPU Ambassador',
        href: '/membership/ambassadorship',
        icon: (
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <circle cx="12" cy="8" r="6"/>
                <path d="M15.477 12.89L17 22l-5-3-5 3 1.523-9.11"/>
            </svg>
        ),
    },
];

export default async function MembershipPage() {
    const session = await getBPUSession();

    return (
        <div className="min-h-screen flex flex-col bg-hp-bg text-hp-dark">
            <SiteHeader />

            <main className="flex-1 pt-20">
                {/* Hero */}
                <section className="px-4 sm:px-6 lg:px-8 pt-10 pb-0 max-w-7xl mx-auto">
                    <div
                        className="relative w-full rounded-2xl overflow-hidden flex items-center justify-center"
                        style={{
                            minHeight: 480,
                            background: '#111317',
                            border: '2px solid #111317',
                            boxShadow: '6px 6px 0px 0px #001478',
                        }}
                    >
                        {/* Background photo */}
                        {/* eslint-disable-next-line @next/next/no-img-element */}
                        <img
                            src="https://images.unsplash.com/photo-1529156069898-49953e39b3ac?w=1400&q=80"
                            alt=""
                            aria-hidden="true"
                            style={{
                                position: 'absolute', inset: 0,
                                width: '100%', height: '100%',
                                objectFit: 'cover',
                                filter: 'grayscale(100%)',
                                opacity: 0.35,
                            }}
                        />

                        {/* Floating content box */}
                        <div
                            className="relative z-10 text-center m-6 sm:m-10 max-w-3xl w-full px-8 py-10 rounded-xl"
                            style={{
                                background: 'var(--bg)',
                                border: '2px solid #111317',
                                boxShadow: '4px 4px 0px 0px #001478',
                            }}
                        >
                            <h1
                                className="font-black uppercase tracking-tight mb-5"
                                style={{ fontSize: 'clamp(2.5rem, 6vw, 4rem)', lineHeight: 1, color: '#111317' }}
                            >
                                Membership
                            </h1>
                            <p className="text-base sm:text-lg font-semibold leading-relaxed" style={{ color: 'var(--text-2)', maxWidth: '56ch', margin: '0 auto' }}>
                                Discover a community that understands your unique journey as a Black professional in the UK.
                                We know what you need to thrive — and our platform is designed to provide essential support
                                and opportunities to help you succeed.
                            </p>
                            {!session.authenticated && (
                                <div className="flex flex-wrap gap-3 justify-center mt-7">
                                    <Link
                                        href="/register"
                                        className="btn btn-amber"
                                        style={{ border: '2px solid #111317', boxShadow: '3px 3px 0px 0px #001478' }}
                                    >
                                        Become a Member
                                    </Link>
                                    <Link href="/login" className="btn btn-outline">
                                        Sign in
                                    </Link>
                                </div>
                            )}
                        </div>
                    </div>
                </section>

                {/* Initiative grid */}
                <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
                    <InitiativeGrid initiatives={INITIATIVES} />
                </section>
            </main>

            <footer className="py-6 text-center text-xs border-t" style={{ borderColor: 'var(--border)', color: 'var(--text-3)' }}>
                © {new Date().getFullYear()} Black Professionals United ·{' '}
                <a href="/" className="hover:underline">Back to Portal</a>
            </footer>
        </div>
    );
}
