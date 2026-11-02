import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import ProgrammeApplicationsAdmin from './ProgrammeApplicationsAdmin';

export const metadata = { title: 'Programme Applications | Admin' };

export default async function ProgrammeApplicationsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) redirect('/login?returnTo=/admin/programme-applications');
    const adminRoles = ['administrator', 'bpu_editor', 'bpu_moderator'];
    if (!adminRoles.some(r => session.user!.roles.includes(r))) redirect('/paired/dashboard');

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Programme Applications</h1>
            <p className="text-text-2 mb-8">Review and manage applications for the Black Interns, Experienced Talent, and #NextGen programmes.</p>
            <ProgrammeApplicationsAdmin />
        </div>
    );
}
