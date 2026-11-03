import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import SafeSpaceRequestsAdmin from './SafeSpaceRequestsAdmin';

export const metadata = { title: 'Safe Space Requests | Admin' };

export default async function AdminSafeSpacePage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) redirect('/login?returnTo=/admin/safe-space');
    const adminRoles = ['administrator', 'bpu_editor', 'bpu_moderator'];
    if (!adminRoles.some(r => session.user!.roles.includes(r))) redirect('/dashboard');

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Safe Space Requests</h1>
            <p className="text-text-2 mb-8">Manage confidential support requests submitted through the Safe Space programme.</p>
            <SafeSpaceRequestsAdmin />
        </div>
    );
}
