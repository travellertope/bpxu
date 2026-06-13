import { redirect } from 'next/navigation';
import { getBPUSession } from '@/lib/auth';
import MeetingSettings from './MeetingSettings';

export default async function MeetingSettingsPage() {
    const session = await getBPUSession();

    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/mentor/meeting-settings');
    }

    if (!session.user.roles.includes('mentor')) {
        redirect('/paired');
    }

    return <MeetingSettings />;
}
