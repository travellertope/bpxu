import { redirect } from 'next/navigation';
import { getBPUSession } from '@/lib/auth';
import KYCForm from './KYCForm';

export default async function KYCPage() {
    const session = await getBPUSession();

    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/mentor/kyc');
    }

    if (!session.user.roles.includes('mentor')) {
        redirect('/paired');
    }

    return <KYCForm />;
}
