import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { cookies } from 'next/headers';
import PlatformStats from './PlatformStats';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export default async function AdminStatsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/admin/stats');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    let stats: Record<string, unknown> = {};

    if (jwt) {
        try {
            const res = await fetch(`${WP}/wp-json/bpu/v1/paired/admin/stats`, {
                headers: {
                    'Authorization': `Bearer ${jwt}`,
                    'Cache-Control': 'no-store',
                },
            });
            if (res.ok) {
                stats = await res.json();
            }
        } catch {
            /* fail silently */
        }
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Platform Analytics</h1>
            <p className="text-text-2 mb-8">Overview of the PAIRED mentorship platform.</p>

            <PlatformStats initialStats={stats as never} />
        </div>
    );
}
