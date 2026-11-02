import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import PartnershipSubmissionsAdmin from './PartnershipSubmissionsAdmin';

export const metadata = { title: 'Partnership Enquiries | Admin' };

export default async function AdminPartnershipsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) redirect('/login?returnTo=/admin/partnerships');
    const adminRoles = ['administrator', 'bpu_editor', 'bpu_moderator'];
    if (!adminRoles.some(r => session.user!.roles.includes(r))) redirect('/dashboard');

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Partnership Enquiries</h1>
            <p className="text-text-2 mb-8">Review and respond to organisations interested in partnering with BPU.</p>
            <PartnershipSubmissionsAdmin />
        </div>
    );
}
