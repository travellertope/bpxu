import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import EmployerAccountsAdmin from './EmployerAccountsAdmin';

export default async function AdminEmployerAccountsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) redirect('/login?returnTo=/admin/employer-accounts');
    if (!session.user!.roles.includes('administrator')) redirect('/paired/dashboard');
    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Employer Accounts</h1>
            <p className="text-text-2 mb-8">Link WordPress users to their employer account so they can manage their company profile.</p>
            <EmployerAccountsAdmin />
        </div>
    );
}
