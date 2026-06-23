import { getBPUSession } from '@/lib/auth';
import { BPUApi } from '@/lib/api';
import CourseBoard from './CourseBoard';

export const metadata = {
    title: 'Courses | BPU Portal',
    description: 'Our members are empowered to achieve their career goals through exclusive free courses from The Skills Network.',
};

export default async function CoursesPage() {
    const [session, courses] = await Promise.all([
        getBPUSession(),
        BPUApi.getCourses(),
    ]);

    return (
        <div className="min-h-screen flex flex-col">
            {/* Topbar */}
            <header className="topbar">
                <div className="topbar-inner">
                    <a href="/" className="topbar-brand">
                        <img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="Black Professionals United" />
                    </a>
                    <div className="flex items-center gap-3">
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
                    className="py-20 text-center"
                    style={{
                        position: 'relative',
                        overflow: 'hidden',
                        borderBottom: '1px solid var(--border)',
                    }}
                >
                    {/* Background image */}
                    <div style={{
                        position: 'absolute', inset: 0,
                        backgroundImage: 'url(https://images.unsplash.com/photo-1522202176988-66273c2fd55f?w=1600&q=80)',
                        backgroundSize: 'cover',
                        backgroundPosition: 'center 45%',
                    }} />
                    {/* Dark overlay */}
                    <div style={{ position: 'absolute', inset: 0, background: 'rgba(10,10,20,0.62)' }} />

                    <div className="wrap" style={{ position: 'relative', zIndex: 1, display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '20px' }}>
                        <h1 className="text-4xl font-extrabold tracking-tight" style={{ color: '#fff' }}>
                            Free Courses for BPU Members
                        </h1>
                        <p className="text-lg max-w-2xl mx-auto" style={{ color: 'rgba(255,255,255,0.82)' }}>
                            Our members are empowered to achieve their career goals. The Skills Network offers our members exclusive access to free courses for skill enhancement. Whether you seek career advancement, a new path, or entrepreneurial ventures, continuous learning is the key to success.
                        </p>

                        {/* Partnership badge */}
                        <div
                            className="inline-flex items-center gap-4"
                            style={{
                                background: 'rgba(255,255,255,0.12)',
                                backdropFilter: 'blur(8px)',
                                border: '1px solid rgba(255,255,255,0.2)',
                                borderRadius: '12px',
                                padding: '10px 20px',
                            }}
                        >
                            <span className="text-xs font-semibold uppercase tracking-widest whitespace-nowrap" style={{ color: 'rgba(255,255,255,0.7)' }}>In partnership with</span>
                            <img
                                src="https://blackprofessionals.uk/wp-content/uploads/elementor/thumbs/The-Skills-Network-r9nhxuf2p4vw81jcuu758g0092znp6rk36twiaf1dm.png"
                                alt="The Skills Network"
                                style={{ height: '32px', width: 'auto', display: 'block', filter: 'brightness(0) invert(1)' }}
                            />
                        </div>

                        <p className="text-sm" style={{ color: 'rgba(255,255,255,0.55)' }}>
                            {courses.length > 0
                                ? `${courses.length} course${courses.length === 1 ? '' : 's'} available`
                                : 'Check back soon for new courses'}
                        </p>
                    </div>
                </section>

                {/* Board */}
                <section className="wrap py-10">
                    <CourseBoard courses={courses} />
                </section>
            </main>

            <footer className="py-6 text-center text-xs text-text-3 border-t border-border">
                © {new Date().getFullYear()} Black Professionals United · <a href="/" className="hover:underline">Back to Portal</a>
            </footer>
        </div>
    );
}
