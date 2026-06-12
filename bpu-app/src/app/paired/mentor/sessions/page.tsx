import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { cookies } from 'next/headers';
import SessionManager from './SessionManager';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export default async function MentorSessionsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/mentor/sessions');
    }
    if (!session.user.roles.includes('mentor')) {
        redirect('/paired/dashboard');
    }

    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    let sessions: Array<Record<string, unknown>> = [];

    if (jwt) {
        try {
            const res = await fetch(`${WP}/wp-json/bpu/v1/paired/mentor/sessions`, {
                headers: {
                    'Authorization': `Bearer ${jwt}`,
                    'Cache-Control': 'no-store',
                },
            });
            if (res.ok) {
                const data = await res.json();
                sessions = data.sessions || data.data || [];
            }
        } catch {
            /* fail silently — client will show empty state */
        }
    }

    return (
        <div className="wrap py-10 fade-up">
            <div className="flex items-center justify-between mb-8">
                <div>
                    <h1 className="text-3xl font-bold">My Session Types</h1>
                    <p className="text-text-2 mt-1">Create and manage the session types you offer to mentees.</p>
                </div>
            </div>

            <SessionManager initial={sessions as never[]} />
        </div>
    );
}
