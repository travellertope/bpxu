import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import TalentPoolClient from './TalentPoolClient';

export const metadata = {
    title: 'Talent Pool | BPU Portal',
};

export default async function TalentPoolPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/talent-pool');
    }
    return <TalentPoolClient user={session.user} />;
}
