import { redirect } from 'next/navigation';
import { getBPUSession } from '@/lib/auth';
import ApplicationsAdmin from './ApplicationsAdmin';

export default async function MentorApplicationsPage() {
    const session = await getBPUSession();

    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/admin/applications');
    }

    const isAdmin = session.user.roles.includes('administrator');
    if (!isAdmin) {
        redirect('/paired');
    }

    return <ApplicationsAdmin />;
}
