import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import NewTicketClient from './NewTicketClient';

export const metadata = {
    title: 'New Support Ticket | BPU Member Portal',
};

export default async function NewTicketPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/support/new');
    }

    return <NewTicketClient user={session.user} />;
}
