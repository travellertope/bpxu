import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { cookies } from 'next/headers';
import MenteeBookings from './MenteeBookings';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export default async function MenteeBookingsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/mentee/bookings');
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
            /* fail silently */
        }
    }

    return (
        <div className="wrap py-10 fade-up">
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-3xl font-bold">My Bookings</h1>
                    <p className="text-text-2 mt-1">Track and manage your mentorship session bookings.</p>
                </div>
                <a href="/paired/mentors" className="btn btn-purple btn-sm shrink-0">
                    Find a mentor
                </a>
            </div>

            <MenteeBookings initial={bookings as never[]} />
        </div>
    );
}
