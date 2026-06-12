import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';

export default async function MentorSettingsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/mentor/settings');
    }
    if (!session.user.roles.includes('mentor')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="wrap py-10 fade-up">
            <h1 className="text-3xl font-bold mb-2">Profile Settings</h1>
            <p className="text-text-2 mb-8">Manage your mentor profile, availability, and preferences.</p>

            <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {/* Sidebar nav */}
                <nav className="flex flex-col gap-1">
                    <span className="btn btn-ghost btn-sm text-left font-semibold" style={{ background: 'var(--purple-bg)', color: 'var(--purple)' }}>Personal Info</span>
                    <span className="btn btn-ghost btn-sm text-left">Professional</span>
                    <span className="btn btn-ghost btn-sm text-left">Mentorship</span>
                    <span className="btn btn-ghost btn-sm text-left">Availability</span>
                    <span className="btn btn-ghost btn-sm text-left">Experience</span>
                    <span className="btn btn-ghost btn-sm text-left">Education</span>
                    <span className="btn btn-ghost btn-sm text-left">Social Links</span>
                </nav>

                {/* Form area — will be wired up in Phase 1 */}
                <div className="lg:col-span-3">
                    <div className="card card-p">
                        <p className="text-text-2 text-sm">Settings form will be implemented here.</p>
                    </div>
                </div>
            </div>
        </div>
    );
}
