import { getBPUSession } from '@/lib/auth';
import ProfileClient from './ProfileClient';

export default async function ProfilePage() {
  const session = await getBPUSession();
  // Layout already handles redirect for unauthenticated users
  const user = session.user!;

  return <ProfileClient user={user} />;
}
