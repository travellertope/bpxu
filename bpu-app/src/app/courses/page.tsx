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
                    className="py-14 text-center"
                    style={{
                        background: 'linear-gradient(135deg, var(--brand-bg) 0%, var(--surface) 100%)',
                        borderBottom: '1px solid var(--border)',
                    }}
                >
                    <div className="wrap" style={{ display: 'flex', flexDirection: 'column', alignItems: 'center', gap: '20px' }}>
                        <h1 className="text-4xl font-extrabold tracking-tight">
                            Free Courses for BPU Members
                        </h1>
                        <p className="text-text-2 text-lg max-w-2xl mx-auto">
                            Our members are empowered to achieve their career goals. The Skills Network offers our members exclusive access to free courses for skill enhancement. Whether you seek career advancement, a new path, or entrepreneurial ventures, continuous learning is the key to success.
                        </p>

                        {/* Partnership badge */}
                        <div
                            className="card card-p inline-flex items-center gap-4"
                            style={{ padding: '12px 20px', display: 'inline-flex' }}
                        >
                            <span className="text-xs font-semibold text-text-3 uppercase tracking-widest whitespace-nowrap">In partnership with</span>
                            <img
                                src="https://blackprofessionals.uk/wp-content/uploads/elementor/thumbs/The-Skills-Network-r9nhxuf2p4vw81jcuu758g0092znp6rk36twiaf1dm.png"
                                alt="The Skills Network"
                                style={{ height: '36px', width: 'auto', display: 'block' }}
                            />
                        </div>

                        <p className="text-sm text-text-3">
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
