import { redirect } from 'next/navigation';
import { getBPUSession } from '@/lib/auth';
import PayoutSettings from './PayoutSettings';

export default async function PayoutSettingsPage() {
    const session = await getBPUSession();

    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/mentor/payout-settings');
    }

    if (!session.user.roles.includes('mentor')) {
        redirect('/paired');
    }

    return <PayoutSettings />;
}
