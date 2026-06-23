import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { cookies } from 'next/headers';
import MentorSettingsForm from './MentorSettingsForm';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

async function fetchWithAuth(path: string) {
    const store = await cookies();
    const jwt = store.get('bpu_session')?.value;
    if (!jwt) return null;
    try {
        const res = await fetch(`${WP}${path}`, {
            headers: {
                'Authorization': `Bearer ${jwt}`,
                'Cache-Control': 'no-store',
            },
        });
        if (!res.ok) return null;
        return await res.json();
    } catch {
        return null;
    }
}

export default async function MentorSettingsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/mentor/settings');
    }
    if (!session.user.roles.includes('mentor')) {
        redirect('/paired/dashboard');
    }

    const [profileData, availabilityData, experiencesData, educationData, skillsData] =
        await Promise.all([
            fetchWithAuth('/wp-json/bpu/v1/paired/mentor/profile'),
            fetchWithAuth('/wp-json/bpu/v1/paired/mentor/availability'),
            fetchWithAuth('/wp-json/bpu/v1/paired/mentor/experiences'),
            fetchWithAuth('/wp-json/bpu/v1/paired/mentor/education'),
            fetchWithAuth('/wp-json/bpu/v1/paired/skills'),
        ]);

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Profile Settings</h1>
            <p className="text-text-2 mb-8">Manage your mentor profile, availability, and preferences.</p>

            <MentorSettingsForm
                profile={profileData?.profile || {}}
                displayName={profileData?.display_name || session.user.display_name}
                email={profileData?.email || session.user.email}
                avatarUrl={profileData?.avatar_url || ''}
                availability={availabilityData || { schedule: [], holidays: [], timezone: 'Europe/London' }}
                experiences={experiencesData?.experiences || []}
                education={educationData?.education || []}
                skillsOptions={skillsData?.skills || {}}
            />
        </div>
    );
}
