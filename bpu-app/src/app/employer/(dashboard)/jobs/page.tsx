import { getBPUSession } from '@/lib/auth';
import { cookies } from 'next/headers';
import EmployerDashboard from './EmployerDashboard';
import { Job } from '@/app/jobs/types';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

async function fetchEmployerJobs(jwt: string): Promise<Job[]> {
    try {
        const res = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/employer/jobs`, {
            headers: { Authorization: `Bearer ${jwt}` },
            cache: 'no-store',
        });
        if (!res.ok) return [];
        const data = await res.json();
        return data.jobs ?? [];
    } catch {
        return [];
    }
}

export const metadata = { title: 'My Jobs | BPU Employer' };

export default async function EmployerJobsPage() {
    const session = await getBPUSession();
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value ?? '';
    const jobs = await fetchEmployerJobs(jwt);

    return <EmployerDashboard initialJobs={jobs} companyName={session.user?.display_name ?? ''} />;
}
