import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import TeamAdmin from './TeamAdmin';

export default async function AdminTeamPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/team');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Team Management</h1>
            <p className="text-text-2 mb-8">Manage admin team members and their roles.</p>
            <TeamAdmin />
        </div>
    );
}
