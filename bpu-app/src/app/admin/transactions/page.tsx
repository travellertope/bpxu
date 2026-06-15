import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import TransactionsAdmin from './TransactionsAdmin';

export default async function AdminTransactionsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/transactions');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Transaction History</h1>
            <p className="text-text-2 mb-8">All payment transactions across the platform.</p>
            <TransactionsAdmin />
        </div>
    );
}
