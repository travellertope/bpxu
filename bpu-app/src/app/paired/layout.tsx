import { Inter } from 'next/font/google';
import { getBPUSession } from '@/lib/auth';

const inter = Inter({ subsets: ['latin'], variable: '--font-sans' });

export default async function PairedLayout({ children }: { children: React.ReactNode }) {
  const session = await getBPUSession();

  const loginUrl = `/login?returnTo=/paired`;

  return (
    <div className={`${inter.variable} font-sans min-h-screen flex flex-col bg-bg text-text`}>

      {/* ── Topbar ─────────────────────────────────────── */}
      <header className="topbar">
        <div className="topbar-inner">
          <a href="/paired" className="topbar-brand">
            <span>PAIRED</span>
            <span className="text-text-2 font-medium text-base"> by BPU</span>
          </a>

          <nav className="hidden md:flex items-center gap-1">
            <a href="/paired" className="btn btn-ghost btn-sm">Home</a>
            <a href="/paired/mentors" className="btn btn-ghost btn-sm">Browse mentors</a>
            {session.authenticated && (
              <a href="/paired/dashboard" className="btn btn-ghost btn-sm">My sessions</a>
            )}
          </nav>

          <div className="flex items-center gap-3">
            {session.authenticated && session.user ? (
              <>
                <span className="text-sm text-text-2 hidden md:inline">{session.user.display_name}</span>
                <a href="/paired/dashboard" className="btn btn-purple btn-sm">Dashboard</a>
                <a href="/api/auth/logout" className="btn btn-ghost btn-sm">Sign out</a>
              </>
            ) : (
              <a href={loginUrl} className="btn btn-purple btn-sm">Sign in</a>
            )}
          </div>
        </div>
      </header>

      <main className="flex-1">{children}</main>

      {/* ── Footer ─────────────────────────────────────── */}
      <footer className="border-t border-border py-10 text-center text-sm text-text-3">
        <p className="font-semibold text-text-2">PAIRED by Black Professionals United</p>
        <p className="mt-1">Empowering careers through mentorship across the UK.</p>
      </footer>

    </div>
  );
}
