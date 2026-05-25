import { getBPUSession } from '@/lib/auth';
import { BPUApi, JobListing, CourseItem, CVReview } from '@/lib/api';
import { cookies } from 'next/headers';
import ClientDashboard from './ClientDashboard';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

/**
 * Main Portal Page (app.blackprofessionals.uk)
 * Implements Server-Side SSO session validation and fetches initial profile/job/course states.
 */
export default async function MemberPortal() {
    const session = await getBPUSession();
    
    // ----------------------------------------------------
    // GUEST VIEW: If user is not authenticated in BPU ecosystem
    // ----------------------------------------------------
    if (!session.authenticated || !session.user) {
        return (
            <div className="flex-1 flex flex-col justify-center items-center px-6 py-16 lg:px-8 transition-all duration-300">
                {/* Widen container to max-w-2xl for better structural flow and breathing room */}
                <div className="w-full max-w-2xl space-y-10 text-center">
                    <div className="space-y-4">
                        <span className="inline-flex items-center rounded-full bg-amber-500/10 px-3 py-1 text-xs font-semibold text-amber-500 ring-1 ring-inset ring-amber-500/20">
                            BPU Unified App
                        </span>
                        <h1 className="text-4xl font-extrabold tracking-tight sm:text-5xl md:text-6xl">
                            Unlock Your <span className="gradient-text">Potential</span>
                        </h1>
                        <p className="mt-3 text-sm sm:text-base text-text-muted max-w-lg mx-auto leading-relaxed">
                            Empowering Black professionals in the UK with elite career resources, manual CV audits, courses, and semantic mentor-mentee pairing.
                        </p>
                    </div>

                    {/* Unified Landing Benefits Cards with left border accents */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5 text-left pt-2">
                        <div className="p-5 premium-card border-l-4 border-l-amber-500">
                            <div className="text-amber-500 text-md mb-1 font-bold">📄 CV Clinic</div>
                            <div className="text-xs text-text-muted leading-relaxed">Gemini Pro parsing & manual BPU professional critiques.</div>
                        </div>
                        <div className="p-5 premium-card border-l-4 border-l-amber-500">
                            <div className="text-amber-500 text-md mb-1 font-bold">💼 Job Boards</div>
                            <div className="text-xs text-text-muted leading-relaxed">Direct partner posts & daily automated AI recommendations.</div>
                        </div>
                        <div className="p-5 premium-card border-l-4 border-l-amber-500">
                            <div className="text-amber-500 text-md mb-1 font-bold">🎓 Courses</div>
                            <div className="text-xs text-text-muted leading-relaxed">Accredited Tutor LMS courses & career placements.</div>
                        </div>
                        <div className="p-5 premium-card border-l-4 border-l-amber-500">
                            <div className="text-amber-500 text-md mb-1 font-bold">🤝 PAIRED</div>
                            <div className="text-xs text-text-muted leading-relaxed">Smart automated mentor compatibility matching.</div>
                        </div>
                    </div>

                    {/* Single Sign-On Access triggers */}
                    <div className="space-y-4 pt-4">
                        <div className="premium-card p-6 border-amber-500/20 bg-amber-500/5">
                            <h3 className="text-sm font-bold mb-2">Single Sign-On (SSO) Active</h3>
                            <p className="text-xs text-text-muted mb-5 leading-relaxed">
                                app.blackprofessionals.uk shares sessions with your main WordPress account. Logging in below instantly registers your portal.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <a 
                                    href={`${WP_BACKEND_URL}/login?redirect=https://app.blackprofessionals.uk`}
                                    className="button-primary text-sm shadow-md"
                                >
                                    Login with BPU Account
                                </a>
                                <a 
                                    href={`${WP_BACKEND_URL}/register`}
                                    className="button-secondary text-sm"
                                >
                                    Register Free Account
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        );
    }

    // ----------------------------------------------------
    // MEMBER PORTAL VIEW: Fetch backend resources server-side
    // ----------------------------------------------------
    const user = session.user;
    const cookieStore = await cookies();
    const wpCookie = cookieStore.getAll().find(c => c.name.startsWith('wordpress_logged_in_'));
    const wpCookieHeader = wpCookie ? `${wpCookie.name}=${wpCookie.value}` : '';

    // Parallel Server-Side Fetching
    const [jobs, courses, reviews] = await Promise.all([
        BPUApi.getJobRecommendations(user.id, wpCookieHeader),
        BPUApi.getCourses(wpCookieHeader),
        BPUApi.getCVClinicReviews(wpCookieHeader)
    ]);

    return (
        <ClientDashboard 
            user={user}
            initialJobs={jobs}
            initialCourses={courses}
            initialReviews={reviews}
            wpCookieHeader={wpCookieHeader}
            wpBackendUrl={WP_BACKEND_URL}
        />
    );
}
