import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import JobApplicationsAdmin from './JobApplicationsAdmin';

export default async function AdminApplicationsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/applications');
    }
    const adminRoles = ['administrator', 'bpu_editor', 'bpu_moderator'];
    if (!adminRoles.some(r => session.user!.roles.includes(r))) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Job Applications</h1>
            <p className="text-text-2 mb-8">Review and manage all job applications across the platform.</p>
            <JobApplicationsAdmin />
        </div>
    );
}
