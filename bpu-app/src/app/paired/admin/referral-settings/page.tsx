import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import ReferralSettingsForm from './ReferralSettingsForm';

export default async function AdminReferralSettingsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/admin/referral-settings');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Referral Settings</h1>
            <p className="text-text-2 mb-8">Configure how the referral program works.</p>
            <ReferralSettingsForm />
        </div>
    );
}
