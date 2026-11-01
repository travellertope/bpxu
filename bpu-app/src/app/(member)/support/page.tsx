import { getBPUSession } from '@/lib/auth';
import { BPUApi } from '@/lib/api';
import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';
import SupportListClient from './SupportListClient';

export const metadata = {
    title: 'Member Support | BPU Member Portal',
};

export default async function SupportPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/support');
    }

    const jwt = (await cookies()).get('bpu_session')?.value ?? '';
    const tickets = await BPUApi.getSupportTickets(jwt);

    return <SupportListClient tickets={tickets} />;
}
