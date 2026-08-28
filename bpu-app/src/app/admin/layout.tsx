import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { headers } from 'next/headers';
import DashboardShell from '@/app/paired/DashboardShell';
import NotificationBell from '@/app/paired/NotificationBell';

// Admin sections that only make sense on one platform's domain. Reaching
// one of these by direct URL on the wrong domain redirects to the shared
// Overview page rather than exposing it — the sidebar already hides these
// links on the wrong domain (see DashboardShell), this closes the gap for
// anyone who navigates or bookmarks the URL directly.
const PAIRED_ONLY_ADMIN_PREFIXES = [
  '/admin/mentors', '/admin/mentees', '/admin/bookings', '/admin/kyc', '/admin/skills',
  '/admin/transactions', '/admin/payouts', '/admin/coupons', '/admin/reports',
  '/admin/stats', '/admin/referrals', '/admin/referral-settings',
];
const BPU_ONLY_ADMIN_PREFIXES = [
  '/admin/jobs', '/admin/applications', '/admin/employers', '/admin/employer-accounts', '/admin/job-reports',
  '/admin/members', '/admin/birthdays', '/admin/profile-completeness',
];

export default async function AdminLayout({ children }: { children: React.ReactNode }) {
  const session = await getBPUSession();
  if (!session.authenticated || !session.user) {
    redirect('/login?returnTo=/admin/dashboard');
  }

  const headerList = await headers();
  const pathname = headerList.get('x-next-pathname') || headerList.get('x-invoke-path') || '';
  const platform = (headerList.get('x-bpu-platform') as 'paired' | 'bpu') || 'bpu';

  const user = session.user;
  const roles = Array.isArray(user.roles) ? user.roles : Object.values(user.roles);
  const isMentor = (roles as string[]).includes('mentor');
  const isAdmin = (roles as string[]).includes('administrator') || (roles as string[]).includes('bpu_editor') || (roles as string[]).includes('bpu_moderator');

  if (!isAdmin) {
    redirect('/paired/dashboard');
  }

  const wrongPlatform = platform === 'bpu'
    ? PAIRED_ONLY_ADMIN_PREFIXES.some(p => pathname.startsWith(p))
    : BPU_ONLY_ADMIN_PREFIXES.some(p => pathname.startsWith(p));
  if (wrongPlatform) {
    redirect('/admin/dashboard');
  }

  return (
    <DashboardShell
      currentPath={pathname}
      userName={user.display_name || 'User'}
      userEmail={user.email || ''}
      isMentor={isMentor}
      isAdmin={isAdmin}
      userRoles={roles as string[]}
      platform={platform}
      notificationBell={<NotificationBell />}
    >
      {children}
    </DashboardShell>
  );
}
