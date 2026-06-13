import { redirect } from 'next/navigation';
import { getBPUSession } from '@/lib/auth';
import KYCAdmin from './KYCAdmin';

export default async function AdminKYCPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/admin/kyc');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired');
    }

    return <KYCAdmin />;
}
