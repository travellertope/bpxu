import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { cookies } from 'next/headers';
import MenteeProfileForm from './MenteeProfileForm';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export default async function MenteeProfilePage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/mentee/profile');
    }

    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    let profile: Record<string, unknown> = {};

    if (jwt) {
        try {
            const res = await fetch(`${WP}/wp-json/bpu/v1/paired/mentee/profile`, {
                headers: {
                    'Authorization': `Bearer ${jwt}`,
                    'Cache-Control': 'no-store',
                },
            });
            if (res.ok) {
                const data = await res.json();
                profile = data.profile || {};
            }
        } catch {
            /* fail silently */
        }
    }

    return (
        <div className="fade-up" style={{ maxWidth: 720 }}>
            <a href="/paired/dashboard" className="text-sm text-text-3 hover:text-brand flex items-center gap-1 mb-6" style={{ width: 'fit-content' }}>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Back to dashboard
            </a>

            <h1 className="text-3xl font-bold mb-2">Profile Settings</h1>
            <p className="text-text-2 mb-8">Update your mentee profile to help mentors understand your goals.</p>

            <MenteeProfileForm initialProfile={profile as never} />
        </div>
    );
}
