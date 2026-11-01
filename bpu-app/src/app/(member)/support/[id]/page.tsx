import { getBPUSession } from '@/lib/auth';
import { BPUApi } from '@/lib/api';
import { cookies } from 'next/headers';
import { redirect, notFound } from 'next/navigation';
import TicketThreadClient from './TicketThreadClient';

export default async function TicketThreadPage({
    params,
}: {
    params: Promise<{ id: string }>;
}) {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/support');
    }

    const { id } = await params;
    const jwt = (await cookies()).get('bpu_session')?.value ?? '';
    const ticket = await BPUApi.getSupportTicketDetail(id, jwt);
    if (!ticket) notFound();

    return <TicketThreadClient ticket={ticket} user={session.user} />;
}
