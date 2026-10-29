import { getBPUSession } from '@/lib/auth';
import MembersDirectoryClient from './MembersDirectoryClient';

export const metadata = {
  title: 'Member Directory | Black Professionals UK',
  description: 'Discover Black professionals across the UK — search by name, sector, and skills.',
};

export default async function MembersDirectoryPage() {
  const session = await getBPUSession();
  return (
    <MembersDirectoryClient
      viewer={session.user ?? null}
      isAuthenticated={session.authenticated}
    />
  );
}
