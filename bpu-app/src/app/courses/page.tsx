import { getBPUSession } from '@/lib/auth';
import { BPUApi } from '@/lib/api';
import CourseBoard from './CourseBoard';

export const metadata = {
    title: 'Courses | BPU Portal',
    description: 'Browse accredited professional development courses curated for Black professionals in the UK.',
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
                    <div className="wrap">
                        <h1 className="text-4xl font-extrabold tracking-tight mb-3">
                            Professional Development Courses
                        </h1>
                        <p className="text-text-2 text-lg max-w-2xl mx-auto">
                            Discover a diverse range of courses that cover various industries and subjects, providing you with the tools to thrive in your career. From leadership development to digital marketing and everything in between, our free courses offer valuable insights and practical knowledge.
                        </p>
                        <p className="text-sm text-text-3 max-w-xl mx-auto mt-3">
                            All courses are fully accredited and recognised throughout the UK and internationally.
                        </p>
                        <p className="mt-3 text-sm text-text-3">
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
