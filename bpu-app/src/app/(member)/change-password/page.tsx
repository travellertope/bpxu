import ChangePasswordForm from '@/app/paired/settings/change-password/ChangePasswordForm';

export default function ChangePasswordPage() {
    return (
        <div className="fade-up" style={{ maxWidth: '480px' }}>
            <h1 className="text-3xl font-bold mb-2">Change Password</h1>
            <p className="text-text-2 mb-8">Update your account password.</p>
            <ChangePasswordForm />
        </div>
    );
}
