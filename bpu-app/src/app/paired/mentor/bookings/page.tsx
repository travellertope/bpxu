import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { cookies } from 'next/headers';
import BookingManager from './BookingManager';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export default async function MentorBookingsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/mentor/bookings');
    }
    if (!session.user.roles.includes('mentor')) {
        redirect('/paired/dashboard');
    }

    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    let bookings: Array<Record<string, unknown>> = [];

    if (jwt) {
        try {
            const res = await fetch(`${WP}/wp-json/bpu/v1/bookings?per_page=100`, {
                headers: {
                    'Authorization': `Bearer ${jwt}`,
                    'Cache-Control': 'no-store',
                },
            });
            if (res.ok) {
                const data = await res.json();
                bookings = data.data || data.bookings || [];
            }
        } catch {
            /* fail silently — client will show empty state */
        }
    }

    return (
        <div className="wrap py-10 fade-up">
            <h1 className="text-3xl font-bold mb-2">Bookings</h1>
            <p className="text-text-2 mb-8">Manage all your mentorship session bookings.</p>

            <BookingManager initial={bookings as never[]} />
        </div>
    );
}
