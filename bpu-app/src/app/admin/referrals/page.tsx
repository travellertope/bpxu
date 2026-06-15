import { redirect } from 'next/navigation';
import { getBPUSession } from '@/lib/auth';
import ReferralAdmin from './ReferralAdmin';

export default async function AdminReferralsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/referrals');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired');
    }

    return <ReferralAdmin />;
}
