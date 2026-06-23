import { getBPUSession } from '@/lib/auth';
import { headers } from 'next/headers';
import DashboardShell from './DashboardShell';
import NotificationBell from './NotificationBell';

export default async function PairedLayout({ children }: { children: React.ReactNode }) {
  const session = await getBPUSession();
  const loginUrl = `/login?returnTo=/paired`;

  const headerList = await headers();
  const pathname = headerList.get('x-next-pathname') || headerList.get('x-invoke-path') || '';

  const isPublicPage = !session.authenticated ||
    pathname === '/paired' ||
    pathname === '/paired/apply';

  if (isPublicPage) {
    return (
      <div className="min-h-screen flex flex-col bg-bg text-text font-sans">
        <header className="topbar">
          <div className="topbar-inner">
            <a href="/paired" className="topbar-brand">
              <img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="Black Professionals United" />
              <span className="portal-label">PAIRED</span>
            </a>
            <nav className="hidden md:flex items-center gap-1 flex-1 justify-center">
              <a href="/paired" className="btn btn-ghost btn-sm">Home</a>
              <a href="/paired/mentors" className="btn btn-ghost btn-sm">Browse mentors</a>
            </nav>
            <div className="flex items-center gap-3">
              {session.authenticated && session.user ? (
                <>
                  <NotificationBell />
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
        <footer className="border-t border-border py-10 text-center text-sm text-text-3">
          <img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="BPU" className="h-8 w-auto mx-auto mb-4 opacity-60" />
          <p className="font-semibold text-text-2">PAIRED by Black Professionals United</p>
          <p className="mt-1">Empowering careers through mentorship across the UK.</p>
        </footer>
      </div>
    );
  }

  const user = session.user!;
  const roles = Array.isArray(user.roles) ? user.roles : Object.values(user.roles);
  const isMentor = roles.includes('mentor');
  const isAdmin = roles.includes('administrator') || roles.includes('bpu_editor') || roles.includes('bpu_moderator');

  return (
    <DashboardShell
      currentPath={pathname}
      userName={user.display_name || 'User'}
      userEmail={user.email || ''}
      isMentor={isMentor}
      isAdmin={isAdmin}
      userRoles={roles as string[]}
      notificationBell={<NotificationBell />}
    >
      {children}
    </DashboardShell>
  );
}
