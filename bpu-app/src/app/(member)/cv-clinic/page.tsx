import { getBPUSession } from '@/lib/auth';
import { BPUApi } from '@/lib/api';
import { cookies } from 'next/headers';
import CVClinicClient from './CVClinicClient';
import { AnalysisHistoryEntry, PrepHistoryEntry } from './cv-clinic-history-types';

export default async function CVClinicPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        // layout already handles redirect, but guard just in case
        const { redirect } = await import('next/navigation');
        redirect('/login?returnTo=/cv-clinic');
    }

    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value ?? '';
    const [reviews, historyRaw] = await Promise.all([
        BPUApi.getCVClinicReviews(jwt),
        BPUApi.getCVClinicHistory(jwt),
    ]);

    return (
        <CVClinicClient
            user={session.user!}
            reviews={reviews}
            initialTab="analyse"
            initialAnalyses={historyRaw.analyses as AnalysisHistoryEntry[]}
            initialPrepSessions={historyRaw.prep_sessions as PrepHistoryEntry[]}
        />
    );
}
