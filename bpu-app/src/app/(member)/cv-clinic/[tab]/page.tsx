import { getBPUSession } from '@/lib/auth';
import { BPUApi } from '@/lib/api';
import { cookies } from 'next/headers';
import { notFound } from 'next/navigation';
import CVClinicClient from '../CVClinicClient';
import { AnalysisHistoryEntry, PrepHistoryEntry } from '../cv-clinic-history-types';

const VALID_TABS = {
    'interview-prep': 'prep',
    'upload':         'upload',
    'review':         'review',
} as const;

type TabSlug = keyof typeof VALID_TABS;
type TabId   = (typeof VALID_TABS)[TabSlug] | 'analyse';

export function generateStaticParams() {
    return Object.keys(VALID_TABS).map(tab => ({ tab }));
}

export default async function CVClinicTabPage({
    params,
}: {
    params: Promise<{ tab: string }>;
}) {
    const { tab } = await params;

    if (!(tab in VALID_TABS)) {
        notFound();
    }

    const initialTab: TabId = VALID_TABS[tab as TabSlug];

    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
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
            initialTab={initialTab}
            initialAnalyses={historyRaw.analyses as AnalysisHistoryEntry[]}
            initialPrepSessions={historyRaw.prep_sessions as PrepHistoryEntry[]}
        />
    );
}
