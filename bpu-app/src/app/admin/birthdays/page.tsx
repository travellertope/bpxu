import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import BirthdaysAdmin from './BirthdaysAdmin';

export default async function AdminBirthdaysPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/birthdays');
    }
    const adminRoles = ['administrator', 'bpu_editor'];
    if (!adminRoles.some(r => session.user!.roles.includes(r))) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Birthdays</h1>
            <p className="text-text-2 mb-8">A calendar of member birthdays, filterable by month and reminder-email status.</p>
            <BirthdaysAdmin />
        </div>
    );
}
