import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import SkillsAdmin from './SkillsAdmin';

export default async function AdminSkillsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/skills');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Category & Skill Management</h1>
            <p className="text-text-2 mb-8">Manage the skill categories and skills available to mentors and mentees.</p>
            <SkillsAdmin />
        </div>
    );
}
