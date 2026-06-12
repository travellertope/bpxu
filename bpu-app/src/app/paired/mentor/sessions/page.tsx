import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';

export default async function MentorSessionsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/mentor/sessions');
    }
    if (!session.user.roles.includes('mentor')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="wrap py-10 fade-up">
            <div className="flex items-center justify-between mb-8">
                <div>
                    <h1 className="text-3xl font-bold">My Session Types</h1>
                    <p className="text-text-2 mt-1">Create and manage the session types you offer to mentees.</p>
                </div>
                <button className="btn btn-purple">+ New Session Type</button>
            </div>

            {/* Session list — will be wired up in Phase 1 */}
            <div className="card card-p text-center py-16">
                <p className="text-text-3 text-sm">No session types yet. Create your first one to start receiving bookings.</p>
            </div>
        </div>
    );
}
