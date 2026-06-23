import { getBPUSession } from '@/lib/auth';
import { headers } from 'next/headers';
import { redirect } from 'next/navigation';
import MemberDashboardShell from './MemberDashboardShell';

export default async function MemberLayout({ children }: { children: React.ReactNode }) {
  const session = await getBPUSession();

  if (!session.authenticated || !session.user) {
    redirect('/login?returnTo=/dashboard');
  }

  const headerList = await headers();
  const pathname = headerList.get('x-next-pathname') || headerList.get('x-invoke-path') || '';

  const user = session.user;
  const isPro = user.is_pro || false;
  const isAdmin = user.roles.includes('administrator');

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
