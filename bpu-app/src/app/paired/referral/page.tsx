import { redirect } from 'next/navigation';
import { getBPUSession } from '@/lib/auth';
import ReferralDashboard from './ReferralDashboard';

export default async function ReferralPage() {
    const session = await getBPUSession();

    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/referral');
    }

    return <ReferralDashboard />;
}
