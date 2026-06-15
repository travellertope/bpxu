import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import JobsAdmin from './JobsAdmin';

export default async function AdminJobsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/admin/jobs');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Job Queue</h1>
            <p className="text-text-2 mb-8">View and manage all jobs on the platform.</p>
            <JobsAdmin />
        </div>
    );
}
