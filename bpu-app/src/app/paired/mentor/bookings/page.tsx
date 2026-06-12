import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';

export default async function MentorBookingsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/mentor/bookings');
    }
    if (!session.user.roles.includes('mentor')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="wrap py-10 fade-up">
            <h1 className="text-3xl font-bold mb-2">Bookings</h1>
            <p className="text-text-2 mb-8">Manage all your mentorship session bookings.</p>

            {/* Filter tabs */}
            <div className="flex gap-2 mb-6">
                <span className="badge badge-purple">All</span>
                <span className="badge">Pending</span>
                <span className="badge">Confirmed</span>
                <span className="badge">Completed</span>
                <span className="badge">Cancelled</span>
            </div>

            {/* Bookings list — will be wired up in Phase 1 */}
            <div className="card card-p text-center py-16">
                <p className="text-text-3 text-sm">No bookings yet.</p>
            </div>
        </div>
    );
}
