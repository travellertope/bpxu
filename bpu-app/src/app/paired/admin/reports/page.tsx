import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import FinancialReports from './FinancialReports';

export default async function AdminReportsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/admin/reports');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Financial Reports</h1>
            <p className="text-text-2 mb-8">Revenue analytics and financial overview.</p>
            <FinancialReports />
        </div>
    );
}
