import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { cookies } from 'next/headers';
import { decodeHtml } from '@/lib/utils';
import MenteeSearch from './MenteeSearch';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

interface Mentee {
    id: number;
    display_name: string;
    email: string;
    avatar_url: string;
    booking_count: number;
    last_booking_date: string;
}

function isGravatar(url: string): boolean {
    return !url || url.includes('gravatar.com/avatar');
}

function getInitials(name: string): string {
    return name.split(' ').map(n => n[0]).join('').toUpperCase().slice(0, 2);
}

function menteeColor(id: number): string {
    const colors = ['#6366f1', '#8b5cf6', '#ec4899', '#3b82f6', '#14b8a6', '#f59e0b'];
    return colors[id % colors.length];
}

function formatDate(dateStr: string): string {
    if (!dateStr) return 'No sessions yet';
    const d = new Date(dateStr + 'T00:00:00');
    return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

export default async function MentorMenteesPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/mentor/mentees');
    }
    if (!session.user.roles.includes('mentor')) {
        redirect('/paired/dashboard');
    }

    const user = session.user;
    const store = await cookies();
    const jwt = store.get('bpu_session')?.value || '';

    let mentees: Mentee[] = [];

    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/paired/mentor/mentees`, {
            headers: { 'Authorization': `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
        });
        if (res.ok) {
            const data = await res.json();
            if (data?.mentees) mentees = data.mentees;
        }
    } catch {
        // silently fail — show empty state
    }

    return (
        <div className="fade-up">

            {/* Header */}
            <div className="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-2xl font-bold">
                        My Mentees{mentees.length > 0 && ` (${mentees.length})`}
                    </h1>
                    <p className="text-sm text-text-2 mt-1">
                        All mentees who have booked sessions with you.
                    </p>
                </div>
                <a href="/paired/dashboard" className="btn btn-outline btn-sm shrink-0">
                    Back to dashboard
                </a>
            </div>

            {mentees.length === 0 ? (
                /* Empty state */
                <div className="card card-p text-center py-16 space-y-4">
                    <div className="text-4xl">👥</div>
                    <p className="text-text-2 text-sm">
                        No mentees yet. Share your profile to start receiving bookings.
                    </p>
                    <a href={`/paired/mentors/${user.id}`} className="btn btn-purple btn-sm inline-block">
                        View my public profile
                    </a>
                </div>
            ) : (
                <MenteeSearch mentees={mentees.map(m => ({
                    id: m.id,
                    display_name: m.display_name,
                    email: m.email,
                    avatar_url: m.avatar_url,
                    booking_count: m.booking_count,
                    last_booking_date: m.last_booking_date,
                    isGravatar: isGravatar(m.avatar_url),
                    initials: getInitials(m.display_name),
                    color: menteeColor(m.id),
                    formatted_last_date: formatDate(m.last_booking_date),
                    decoded_name: decodeHtml(m.display_name),
                }))} />
            )}
        </div>
    );
}
