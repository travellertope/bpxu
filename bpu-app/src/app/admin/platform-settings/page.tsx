import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import PlatformSettingsForm from './PlatformSettingsForm';

export default async function AdminPlatformSettingsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/platform-settings');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Platform Settings</h1>
            <p className="text-text-2 mb-8">Configure global platform parameters.</p>
            <PlatformSettingsForm />
        </div>
    );
}
