import { getBPUSession } from '@/lib/auth';
import { BPUApi, JobListing, CourseItem, CVReview } from '@/lib/api';
import { cookies } from 'next/headers';
import ClientDashboard from './ClientDashboard';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';
const APP_URL = process.env.NEXT_PUBLIC_APP_URL || 'https://app.blackprofessionals.uk';

/**
 * Main Portal Page (app.blackprofessionals.uk)
 * Implements Server-Side JWT SSO session validation and fetches initial profile/job/course states.
 */
export default async function MemberPortal() {
    const session = await getBPUSession();
    
    // ----------------------------------------------------
    // GUEST VIEW: If user is not authenticated in BPU ecosystem
    // ----------------------------------------------------
    if (!session.authenticated || !session.user) {
        const loginUrl = `${WP_BACKEND_URL}/?bpu_sso_handoff=1&redirect_to=${encodeURIComponent(`${APP_URL}/api/auth/callback`)}`;
        const registerUrl = `${WP_BACKEND_URL}/register`;

        return (
            <div className="flex-1 flex flex-col justify-center items-center px-6 py-20 lg:py-28 transition-all duration-300">
                {/* Spacious max-w-3xl container for premium feel */}
                <div className="w-full max-w-3xl flex flex-col gap-12 text-center animate-fadeInUp">
                    <div className="flex flex-col gap-6 items-center">
                        <span className="inline-flex items-center rounded-full bg-amber-500/10 px-4 py-1.5 text-sm font-semibold text-amber-500 ring-1 ring-inset ring-amber-500/20">
                            BPU Unified App
                        </span>
                        <h1 className="text-5xl font-extrabold tracking-tight sm:text-6xl lg:text-7xl leading-tight">
                            Unlock Your <span className="gradient-text">Potential</span>
                        </h1>
                        <p className="mt-4 text-base sm:text-lg text-text-muted max-w-xl mx-auto leading-relaxed">
                            Empowering Black professionals in the UK with elite career resources, manual CV audits, courses, and semantic mentor-mentee pairing.
                        </p>
                    </div>

                    {/* Benefit Cards with generous spacing */}
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-6 text-left">
                        <div id="benefit-cv" className="p-6 premium-card border-l-4 border-l-amber-500">
                            <div className="text-amber-500 text-lg mb-2 font-bold">📄 CV Clinic</div>
                            <div className="text-sm text-text-muted leading-relaxed">Gemini Pro parsing &amp; manual BPU professional critiques to sharpen your resume.</div>
                        </div>
                        <div id="benefit-jobs" className="p-6 premium-card border-l-4 border-l-amber-500">
                            <div className="text-amber-500 text-lg mb-2 font-bold">💼 Job Boards</div>
                            <div className="text-sm text-text-muted leading-relaxed">Direct partner posts &amp; daily automated AI-powered recommendations.</div>
                        </div>
                        <div id="benefit-courses" className="p-6 premium-card border-l-4 border-l-amber-500">
                            <div className="text-amber-500 text-lg mb-2 font-bold">🎓 Courses</div>
                            <div className="text-sm text-text-muted leading-relaxed">Accredited Tutor LMS courses &amp; career placement support.</div>
                        </div>
                        <div id="benefit-paired" className="p-6 premium-card border-l-4 border-l-amber-500">
                            <div className="text-amber-500 text-lg mb-2 font-bold">🤝 PAIRED</div>
                            <div className="text-sm text-text-muted leading-relaxed">Smart automated mentor compatibility matching powered by AI.</div>
                        </div>
                    </div>

                    {/* SSO Access Card */}
                    <div className="w-full mt-8">
                        <div id="sso-card" className="premium-card p-8 border-amber-500/20" style={{ backgroundColor: 'rgba(245, 158, 11, 0.05)' }}>
                            <h3 className="text-base font-bold mb-3">Single Sign-On (SSO) Active</h3>
                            <p className="text-sm text-text-muted mb-6 leading-relaxed max-w-lg mx-auto">
                                app.blackprofessionals.uk shares sessions with your main WordPress account. Logging in below instantly activates your portal.
                            </p>
                            <div className="flex flex-col sm:flex-row gap-4 justify-center">
                                <a 
                                    id="sso-login-btn"
                                    href={loginUrl}
                                    className="button-primary text-sm shadow-md min-h-[48px]"
                                >
                                    Login with BPU Account
                                </a>
                                <a 
                                    id="sso-register-btn"
                                    href={registerUrl}
                                    className="button-secondary text-sm min-h-[48px]"
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
    const sessionCookie = cookieStore.get('bpu_session');
    const jwtToken = sessionCookie?.value || '';

    // Parallel Server-Side Fetching (using JWT Bearer token instead of WP cookies)
    const [jobs, courses, reviews] = await Promise.all([
        BPUApi.getJobRecommendations(user.id, jwtToken),
        BPUApi.getCourses(jwtToken),
        BPUApi.getCVClinicReviews(jwtToken)
    ]);

    return (
        <ClientDashboard 
            user={user}
            initialJobs={jobs}
            initialCourses={courses}
            initialReviews={reviews}
            wpCookieHeader={jwtToken}
            wpBackendUrl={WP_BACKEND_URL}
        />
    );
}
