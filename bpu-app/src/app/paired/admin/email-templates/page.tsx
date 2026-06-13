import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import EmailTemplatesAdmin from './EmailTemplatesAdmin';

export default async function AdminEmailTemplatesPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/admin/email-templates');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Email Templates</h1>
            <p className="text-text-2 mb-8">Customise the emails sent by the platform.</p>
            <EmailTemplatesAdmin />
        </div>
    );
}
