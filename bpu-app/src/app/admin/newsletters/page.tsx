import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import NewsletterAdmin from './NewsletterAdmin';

export default async function AdminNewslettersPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/newsletters');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Newsletters</h1>
            <p className="text-text-2 mb-8">Compose and send email newsletters to members via SendGrid.</p>
            <NewsletterAdmin />
        </div>
    );
}
