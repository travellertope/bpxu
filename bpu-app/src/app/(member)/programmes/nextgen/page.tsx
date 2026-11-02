import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import NextGenApplyClient from './NextGenApplyClient';

export const metadata = {
    title: 'Apply — #NextGen Apprenticeship | BPU Portal',
};

export default async function NextGenApplyPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/programmes/nextgen');
    }
    return <NextGenApplyClient user={session.user} />;
}
