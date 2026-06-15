import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import MentorAdmin from './MentorAdmin';

export default async function AdminMentorsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/mentors');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Mentor Management</h1>
            <p className="text-text-2 mb-8">View, edit, and manage all mentors on the platform.</p>

            <MentorAdmin />
        </div>
    );
}
