import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import AmbassadorshipClient from './AmbassadorshipClient';

export const metadata = {
    title: 'Ambassadorship | BPU Member Portal',
};

export default async function AmbassadorshipPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/ambassadorship');
    }

    return <AmbassadorshipClient user={session.user} />;
}
