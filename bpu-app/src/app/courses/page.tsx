import { getBPUSession } from '@/lib/auth';
import { BPUApi } from '@/lib/api';
import CourseBoard from './CourseBoard';

export const metadata = {
    title: 'Professional Courses | BPU Portal',
    description: 'Free accredited courses for BPU members — skill enhancement with The Skills Network.',
};

export default async function CoursesPage() {
    const [session, courses] = await Promise.all([
        getBPUSession(),
        BPUApi.getCourses(),
    ]);

    return (
        <div className="min-h-screen flex flex-col" style={{ background: 'var(--bg)' }}>
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
                {/* Dark hero */}
                <section
                    className="py-16 px-4 text-center"
                    style={{ background: '#111317', borderBottom: '3px solid #111317' }}
                >
                    <div className="max-w-5xl mx-auto flex flex-col items-center gap-5">
                        <div
                            className="inline-block text-white text-xs font-bold uppercase px-3 py-1 rounded tracking-widest"
                            style={{ background: 'var(--brand)', border: '1px solid rgba(255,255,255,0.25)' }}
                        >
                            UK Level 2 Accredited
                        </div>
                        <h1
                            className="font-black uppercase tracking-tight text-white"
                            style={{ fontSize: 'clamp(2.25rem, 5vw, 3.5rem)', lineHeight: 1.05 }}
                        >
                            Professional<br />Development Courses
                        </h1>
                        <p className="text-lg max-w-3xl mx-auto font-medium leading-relaxed" style={{ color: 'rgba(255,255,255,0.7)' }}>
                            Whether you seek career advancement, a new path, or entrepreneurial ventures —
                            continuous learning is the key to success. Propel your career forward with free
                            accredited courses exclusively for BPU members.
                        </p>

                        {/* Partnership badge */}
                        <div
                            className="inline-flex items-center gap-4"
                            style={{
                                background: 'rgba(255,255,255,0.10)',
                                backdropFilter: 'blur(8px)',
                                border: '1px solid rgba(255,255,255,0.18)',
                                borderRadius: 12,
                                padding: '10px 20px',
                            }}
                        >
                            <span className="text-xs font-semibold uppercase tracking-widest whitespace-nowrap" style={{ color: 'rgba(255,255,255,0.6)' }}>
                                In partnership with
                            </span>
                            <img
                                src="https://blackprofessionals.uk/wp-content/uploads/elementor/thumbs/The-Skills-Network-r9nhxuf2p4vw81jcuu758g0092znp6rk36twiaf1dm.png"
                                alt="The Skills Network"
                                style={{ height: 30, width: 'auto', display: 'block', filter: 'brightness(0) invert(1)' }}
                            />
                        </div>

                        <p className="text-sm" style={{ color: 'rgba(255,255,255,0.45)' }}>
                            {courses.length > 0
                                ? `${courses.length} course${courses.length === 1 ? '' : 's'} available`
                                : 'Check back soon for new courses'}
                        </p>
                    </div>
                </section>

                {/* Board — owns its floating filter and grid */}
                <CourseBoard courses={courses} />
            </main>

            <footer className="py-6 text-center text-xs border-t" style={{ borderColor: 'var(--border)', color: 'var(--text-3)' }}>
                © {new Date().getFullYear()} Black Professionals United ·{' '}
                <a href="/" className="hover:underline">Back to Portal</a>
            </footer>
        </div>
    );
}
