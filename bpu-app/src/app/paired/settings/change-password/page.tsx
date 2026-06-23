import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import ChangePasswordForm from './ChangePasswordForm';

export default async function ChangePasswordPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/settings/change-password');
    }

    return (
        <div className="fade-up" style={{ maxWidth: '480px' }}>
            <h1 className="text-3xl font-bold mb-2">Change Password</h1>
            <p className="text-text-2 mb-8">Update your account password.</p>
            <ChangePasswordForm />
        </div>
    );
}
