import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import BookingsAdmin from './BookingsAdmin';

export default async function AdminBookingsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/admin/bookings');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Booking Management</h1>
            <p className="text-text-2 mb-8">View and manage all bookings on the platform.</p>
            <BookingsAdmin />
        </div>
    );
}
