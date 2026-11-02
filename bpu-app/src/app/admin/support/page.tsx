import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import SupportAdmin from './SupportAdmin';

export const metadata = { title: 'Support Tickets | Admin' };

export default async function AdminSupportPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) redirect('/login?returnTo=/admin/support');
    const adminRoles = ['administrator', 'bpu_editor', 'bpu_moderator'];
    if (!adminRoles.some(r => session.user!.roles.includes(r))) redirect('/paired/dashboard');

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Support Tickets</h1>
            <p className="text-text-2 mb-8">Manage member support requests and reply on behalf of BPU.</p>
            <SupportAdmin />
        </div>
    );
}
