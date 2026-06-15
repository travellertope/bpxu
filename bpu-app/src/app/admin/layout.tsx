import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { headers } from 'next/headers';
import DashboardShell from '@/app/paired/DashboardShell';
import NotificationBell from '@/app/paired/NotificationBell';

export default async function AdminLayout({ children }: { children: React.ReactNode }) {
  const session = await getBPUSession();
  if (!session.authenticated || !session.user) {
    redirect('/login?returnTo=/admin/dashboard');
  }

  const headerList = await headers();
  const pathname = headerList.get('x-next-pathname') || headerList.get('x-invoke-path') || '';

  const user = session.user;
  const roles = Array.isArray(user.roles) ? user.roles : Object.values(user.roles);
  const isMentor = (roles as string[]).includes('mentor');
  const isAdmin = (roles as string[]).includes('administrator') || (roles as string[]).includes('bpu_editor') || (roles as string[]).includes('bpu_moderator');

  if (!isAdmin) {
    redirect('/paired/dashboard');
  }

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
