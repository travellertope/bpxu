import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import PayoutsAdmin from './PayoutsAdmin';

export default async function AdminPayoutsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/payouts');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Payout Management</h1>
            <p className="text-text-2 mb-8">Review completed session payouts for mentors.</p>
            <PayoutsAdmin />
        </div>
    );
}
