import { getBPUSession } from '@/lib/auth';

export default async function PairedLayout({ children }: { children: React.ReactNode }) {
  const session = await getBPUSession();
  const loginUrl = `/login?returnTo=/paired`;

  return (
    <div className="min-h-screen flex flex-col bg-bg text-text font-sans">

      {/* ── Topbar ─────────────────────────────────────── */}
      <header className="topbar">
        <div className="topbar-inner">

          {/* Brand */}
          <a href="/paired" className="topbar-brand">
            <img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="Black Professionals United" />
            <span className="portal-label">PAIRED</span>
          </a>

          {/* Navigation */}
          <nav className="hidden md:flex items-center gap-1 flex-1 justify-center">
            <a href="/paired" className="btn btn-ghost btn-sm">Home</a>
            <a href="/paired/mentors" className="btn btn-ghost btn-sm">Browse mentors</a>
            {session.authenticated && (
              <a href="/paired/dashboard" className="btn btn-ghost btn-sm">My sessions</a>
            )}
            {session.authenticated && session.user?.roles.includes('administrator') && (
              <a href="/paired/admin/applications" className="btn btn-ghost btn-sm">Applications</a>
            )}
          </nav>

          {/* Auth controls */}
          <div className="flex items-center gap-3">
            {session.authenticated && session.user ? (
              <>
                <span className="text-sm text-text-2 hidden md:inline">{session.user.display_name}</span>
                <a href="/paired/dashboard" className="btn btn-purple btn-sm">Dashboard</a>
                <a href="/api/auth/logout" className="btn btn-ghost btn-sm text-text-3">Sign out</a>
              </>
            ) : (
              <>
                <a href={loginUrl} className="btn btn-ghost btn-sm">Sign in</a>
                <a href={`/register?returnTo=/paired`} className="btn btn-purple btn-sm">Join free</a>
              </>
            )}
          </div>

        </div>
      </header>

      <main className="flex-1">{children}</main>

      {/* ── Footer ─────────────────────────────────────── */}
      <footer className="border-t border-border py-10 text-center text-sm text-text-3">
        <img
          src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png"
          alt="BPU"
          className="h-8 w-auto mx-auto mb-4 opacity-60"
        />
        <p className="font-semibold text-text-2">PAIRED by Black Professionals United</p>
        <p className="mt-1">Empowering careers through mentorship across the UK.</p>
        <p className="mt-4">
          <a href="/" className="hover:underline">Member Portal</a>
          {' · '}
          <a href="/jobs" className="hover:underline">Job Board</a>
          {' · '}
          <a href="/paired" className="hover:underline">PAIRED</a>
        </p>
      </footer>

    </div>
  );
}
