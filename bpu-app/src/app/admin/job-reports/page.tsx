import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import JobBoardReports from './JobBoardReports';

export default async function AdminJobReportsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/job-reports');
    }
    const adminRoles = ['administrator', 'bpu_editor'];
    if (!adminRoles.some(r => session.user!.roles.includes(r))) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Job Board Reports</h1>
            <p className="text-text-2 mb-8">Performance stats for all jobs — impressions, clicks, applications, and CTR.</p>
            <JobBoardReports />
        </div>
    );
}
