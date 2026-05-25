import { getBPUSession } from '@/lib/auth';
import { BPUApi } from '@/lib/api';
import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';
import ClientDashboard from './ClientDashboard';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';
const APP_URL = process.env.NEXT_PUBLIC_APP_URL || 'https://app.blackprofessionals.uk';

export default async function MemberPortal({
  searchParams,
}: {
  searchParams: Promise<{ auth_error?: string; logged_out?: string }>;
}) {
  const session = await getBPUSession();
  const params = await searchParams;

  if (!session.authenticated || !session.user) {
    const ssoUrl = `${WP_URL}/?bpu_sso_handoff=1&redirect_to=${encodeURIComponent(`${APP_URL}/api/auth/callback`)}`;

    // Auto-bounce to WordPress SSO unless the user just logged out or a
    // previous attempt failed (prevents an infinite redirect loop).
    if (!params.auth_error && !params.logged_out) {
      redirect(ssoUrl);
    }

    return (
      <main className="min-h-screen flex items-center justify-center p-6 bg-bg">
        <div className="w-full max-w-sm fade-up">
          {/* Logo */}
          <div className="text-center mb-8">
            <div className="inline-flex items-center gap-2 text-2xl font-extrabold tracking-tight">
              <span className="text-brand">BPU</span>
              <span className="text-text"> Portal</span>
            </div>
            <p className="mt-2 text-sm text-text-2">Black Professionals United</p>
          </div>

          <div className="card card-p space-y-4">
            {params.auth_error && (
              <div className="alert alert-red text-sm">
                Sign-in failed — please try again.
              </div>
            )}
            {params.logged_out && (
              <div className="alert alert-green text-sm">
                You have been signed out.
              </div>
            )}

            <div className="text-center space-y-1">
              <h1 className="text-xl font-bold">Sign in to your account</h1>
              <p className="text-sm text-text-2">
                Uses your BPU WordPress account via secure SSO.
              </p>
            </div>

            <div className="divider" />

            <a href={ssoUrl} className="btn btn-amber btn-lg w-full justify-center">
              Sign in with BPU Account
            </a>

            <p className="text-center text-sm text-text-2">
              No account?{' '}
              <a href={`${WP_URL}/register`} className="font-semibold text-brand-dark hover:underline">
                Register free
              </a>
            </p>
          </div>

          <p className="mt-6 text-center text-xs text-text-3">
            Empowering Black professionals in the UK
          </p>
        </div>
      </main>
    );
  }

  // ── Authenticated: fetch data server-side ──────────────────
  const user = session.user;
  const cookieStore = await cookies();
  const jwt = cookieStore.get('bpu_session')?.value || '';

  const [jobs, courses, reviews] = await Promise.all([
    BPUApi.getJobRecommendations(user.id, jwt),
    BPUApi.getCourses(jwt),
    BPUApi.getCVClinicReviews(jwt),
  ]);

  return (
    <ClientDashboard
      user={user}
      initialJobs={jobs}
      initialCourses={courses}
      initialReviews={reviews}
      jwt={jwt}
    />
  );
}
