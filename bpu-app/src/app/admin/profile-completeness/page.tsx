import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import ProfileCompletenessAdmin from './ProfileCompletenessAdmin';

export default async function AdminProfileCompletenessPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/profile-completeness');
    }
    const adminRoles = ['administrator', 'bpu_editor'];
    if (!adminRoles.some(r => session.user!.roles.includes(r))) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Profile Completeness</h1>
            <p className="text-text-2 mb-8">How many members are missing key profile fields, and an exportable list of who to follow up with.</p>
            <ProfileCompletenessAdmin />
        </div>
    );
}
