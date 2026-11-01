import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import InterviewPrepClient from './InterviewPrepClient';

export const metadata = {
    title: 'Interview Prep | BPU Member Portal',
};

export default async function InterviewPrepPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/interview-prep');
    }

    return <InterviewPrepClient user={session.user} />;
}
