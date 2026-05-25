import { Inter } from 'next/font/google';
import { headers } from 'next/headers';
import { getBPUSession } from '@/lib/auth';

const inter = Inter({ subsets: ['latin'], variable: '--font-inter' });

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export default async function PairedLayout({
  children,
}: {
  children: React.ReactNode;
}) {
  const session = await getBPUSession();

  // Derive the current origin from the request host so that the SSO callback
  // cookie is set on the correct domain (pairedbybpu.uk vs app.blackprofessionals.uk).
  const headersList = await headers();
  const host = headersList.get('host') || 'app.blackprofessionals.uk';
  const currentOrigin = `https://${host}`;
  const loginUrl = `${WP_BACKEND_URL}/?bpu_sso_handoff=1&redirect_to=${encodeURIComponent(`${currentOrigin}/api/auth/callback?from=paired`)}`;

  return (
    <div className={`${inter.variable} font-sans min-h-screen flex flex-col paired-page-bg`}>
      <header className="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-indigo-100 transition-all duration-300">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
          <div className="flex items-center gap-4">
            <a href="/paired" className="font-extrabold text-xl tracking-tight text-indigo-900">
              <span className="text-indigo-600">PAIRED</span> by BPU
            </a>
          </div>

          <nav className="hidden md:flex gap-6">
            <a href="/paired" className="text-sm font-semibold text-indigo-900 hover:text-indigo-600">Browse Mentors</a>
            {session.authenticated && (
              <a href="/paired/dashboard" className="text-sm font-semibold text-indigo-900 hover:text-indigo-600">My Sessions</a>
            )}
          </nav>

          <div className="flex items-center gap-5">
            {session.authenticated && session.user ? (
              <>
                <span className="text-sm font-medium hidden md:inline">Hello, {session.user.display_name}</span>
                <a href="/api/auth/logout" className="text-xs px-4 py-2 border border-indigo-200 rounded-lg font-semibold text-indigo-600 hover:bg-indigo-50 transition">
                  Sign Out
                </a>
              </>
            ) : (
              <a href={loginUrl} className="text-sm px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg font-semibold shadow-md hover:opacity-90 transition">
                Login with BPU
              </a>
            )}
          </div>
        </div>
      </header>

      <main className="flex-1">
        {children}
      </main>

      <footer className="bg-white border-t border-indigo-100 py-8 text-center text-sm text-indigo-400 mt-20">
        <p>PAIRED by Black Professionals United. Empowering careers through mentorship.</p>
      </footer>
    </div>
  );
}
