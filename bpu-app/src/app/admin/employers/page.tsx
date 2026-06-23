import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import EmployersAdmin from './EmployersAdmin';

export default async function AdminEmployersPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/employers');
    }
    const adminRoles = ['administrator', 'bpu_editor'];
    if (!adminRoles.some(r => session.user!.roles.includes(r))) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Employers</h1>
            <p className="text-text-2 mb-8">View all employer accounts and their job listings.</p>
            <EmployersAdmin />
        </div>
    );
}
