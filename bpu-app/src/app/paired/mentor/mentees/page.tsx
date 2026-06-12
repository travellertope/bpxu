import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';

export default async function MentorMenteesPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/mentor/mentees');
    }
    if (!session.user.roles.includes('mentor')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="wrap py-10 fade-up">
            <h1 className="text-3xl font-bold mb-2">My Mentees</h1>
            <p className="text-text-2 mb-8">All mentees who have booked sessions with you.</p>

            {/* Mentee list — will be wired up in Phase 1 */}
            <div className="card card-p text-center py-16">
                <p className="text-text-3 text-sm">No mentees yet. Share your profile to start receiving bookings.</p>
            </div>
        </div>
    );
}
