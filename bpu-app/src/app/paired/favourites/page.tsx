import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { cookies } from 'next/headers';
import FavouriteMentors from './FavouriteMentors';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export default async function FavouritesPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/favourites');
    }

    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    let mentors: Array<Record<string, unknown>> = [];

    if (jwt) {
        try {
            const res = await fetch(`${WP}/wp-json/bpu/v1/paired/favourites`, {
                headers: {
                    'Authorization': `Bearer ${jwt}`,
                    'Cache-Control': 'no-store',
                },
            });
            if (res.ok) {
                const data = await res.json();
                mentors = data.favourites || [];
            }
        } catch {
            /* fail silently */
        }
    }

    return (
        <div className="wrap py-10 fade-up">
            <h1 className="text-3xl font-bold mb-2">Favourite Mentors</h1>
            <p className="text-text-2 mb-8">Your saved mentors for quick access.</p>

            <FavouriteMentors initialMentors={mentors as never[]} />
        </div>
    );
}
