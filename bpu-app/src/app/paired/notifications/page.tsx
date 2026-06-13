import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import NotificationsList from './NotificationsList';

export default async function NotificationsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/notifications');
    }

    return (
        <div className="wrap py-10 fade-up" style={{ maxWidth: '720px' }}>
            <h1 className="text-3xl font-bold mb-2">Notifications</h1>
            <p className="text-text-2 mb-8">Stay up to date with your mentorship activity.</p>
            <NotificationsList />
        </div>
    );
}
