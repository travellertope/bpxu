import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import MembersAdmin from './MembersAdmin';

export default async function AdminMembersPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/members');
    }
    const adminRoles = ['administrator', 'bpu_editor'];
    if (!adminRoles.some(r => session.user!.roles.includes(r))) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Members</h1>
            <p className="text-text-2 mb-8">Manage member Pro access. Grant or revoke Pro manually without requiring a subscription.</p>
            <MembersAdmin />
        </div>
    );
}
