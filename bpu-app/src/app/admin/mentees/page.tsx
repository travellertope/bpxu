import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import MenteeAdmin from './MenteeAdmin';

export default async function AdminMenteesPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/mentees');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Mentee Management</h1>
            <p className="text-text-2 mb-8">Search and manage all mentees on the platform.</p>
            <MenteeAdmin />
        </div>
    );
}
