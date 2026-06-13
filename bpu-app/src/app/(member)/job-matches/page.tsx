import { getBPUSession } from '@/lib/auth';
import { BPUApi } from '@/lib/api';
import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';
import JobMatchesClient from './JobMatchesClient';

export default async function JobMatchesPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/job-matches');
    }

    const user = session.user;
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value ?? '';

    const jobs = await BPUApi.getJobRecommendations(user.id, jwt);
    const isPro = user.is_pro || false;

    return <JobMatchesClient jobs={jobs} isPro={isPro} />;
}
