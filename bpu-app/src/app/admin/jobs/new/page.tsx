import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import AdminJobForm from '../AdminJobForm';

export default async function AdminNewJobPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/jobs/new');
    }
    const adminRoles = ['administrator', 'bpu_editor'];
    if (!adminRoles.some(r => session.user!.roles.includes(r))) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Create New Job</h1>
            <p className="text-text-2 mb-8">Create and publish a job directly as an admin.</p>
            <AdminJobForm mode="create" />
        </div>
    );
}
