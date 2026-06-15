import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { cookies } from 'next/headers';
import AdminJobForm from '../../AdminJobForm';
import type { JobData } from '../../AdminJobForm';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export default async function AdminEditJobPage({
    params,
}: {
    params: Promise<{ id: string }>;
}) {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/admin/jobs');
    }
    const adminRoles = ['administrator', 'bpu_editor'];
    if (!adminRoles.some(r => session.user!.roles.includes(r))) {
        redirect('/paired/dashboard');
    }

    const { id } = await params;
    const store = await cookies();
    const jwt = store.get('bpu_session')?.value;

    let job: JobData | null = null;
    let fetchError = '';

    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/admin/jobs/${id}`, {
            headers: {
                'Authorization': `Bearer ${jwt}`,
                'Cache-Control': 'no-store',
            },
        });
        if (!res.ok) {
            fetchError = `Failed to load job (status ${res.status}).`;
        } else {
            const data = await res.json();
            job = data.job;
        }
    } catch {
        fetchError = 'Failed to load job data.';
    }

    if (fetchError || !job) {
        return (
            <div className="fade-up">
                <h1 className="text-3xl font-bold mb-2">Edit Job</h1>
                <div className="card card-p text-center py-10" style={{ color: 'var(--err)' }}>
                    {fetchError || 'Job not found.'}
                </div>
                <div className="mt-4">
                    <a href="/admin/jobs" className="btn btn-ghost">Back to Jobs</a>
                </div>
            </div>
        );
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Edit Job</h1>
            <p className="text-text-2 mb-8">Editing: {job.title}</p>
            <AdminJobForm mode="edit" initialData={job} />
        </div>
    );
}
