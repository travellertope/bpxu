import { getBPUSession } from '@/lib/auth';
import { headers } from 'next/headers';
import { redirect } from 'next/navigation';
import MemberDashboardShell from './MemberDashboardShell';

export default async function MemberLayout({ children }: { children: React.ReactNode }) {
  const session = await getBPUSession();

  if (!session.authenticated || !session.user) {
    redirect('/login?returnTo=/dashboard');
  }

  const user = session.user;
  const isAdmin = user.roles.includes('administrator');

  if (!isAdmin && user.roles.includes('bpu_employer')) {
    redirect('/employer/jobs');
  }

  const headerList = await headers();
  const pathname = headerList.get('x-next-pathname') || headerList.get('x-invoke-path') || '';

  const isPro = user.is_pro || false;

  return (
    <MemberDashboardShell
      currentPath={pathname}
      userName={user.display_name || 'User'}
      userEmail={user.email || ''}
      isPro={isPro}
      isAdmin={isAdmin}
    >
      {children}
    </MemberDashboardShell>
  );
}
