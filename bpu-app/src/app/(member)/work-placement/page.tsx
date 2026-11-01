import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import WorkPlacementClient from './WorkPlacementClient';

export const metadata = {
    title: 'Work Placements | BPU Member Portal',
};

export default async function WorkPlacementPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/work-placement');
    }

    return <WorkPlacementClient user={session.user} />;
}
