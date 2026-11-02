import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import ExperiencedTalentApplyClient from './ExperiencedTalentApplyClient';

export const metadata = {
    title: 'Apply — Experienced Talent Programme | BPU Portal',
};

export default async function ExperiencedTalentApplyPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/programmes/experienced-talent');
    }
    return <ExperiencedTalentApplyClient user={session.user} />;
}
