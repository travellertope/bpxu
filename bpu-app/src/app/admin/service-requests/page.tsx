import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import ServiceRequestsAdmin from './ServiceRequestsAdmin';

export const metadata = { title: 'Service Requests | Admin' };

export default async function AdminServiceRequestsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) redirect('/login?returnTo=/admin/service-requests');
    const adminRoles = ['administrator', 'bpu_editor', 'bpu_moderator'];
    if (!adminRoles.some(r => session.user!.roles.includes(r))) redirect('/paired/dashboard');

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Service Requests</h1>
            <p className="text-text-2 mb-8">Manage work placement, interview prep coaching, and ambassadorship applications.</p>
            <ServiceRequestsAdmin />
        </div>
    );
}
