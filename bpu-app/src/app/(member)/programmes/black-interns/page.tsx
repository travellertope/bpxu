import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import BlackInternsApplyClient from './BlackInternsApplyClient';

export const metadata = {
    title: 'Apply — Black Interns Programme | BPU Portal',
};

export default async function BlackInternsApplyPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/programmes/black-interns');
    }
    return <BlackInternsApplyClient user={session.user} />;
}
