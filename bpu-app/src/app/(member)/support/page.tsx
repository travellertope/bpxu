import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import SupportClient from './SupportClient';

export const metadata = {
    title: 'Member Support | BPU Member Portal',
};

export default async function SupportPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/support');
    }

    return <SupportClient user={session.user} />;
}
