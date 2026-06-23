import { getBPUSession } from '@/lib/auth';
import { cookies } from 'next/headers';
import EmployerProfileForm from './EmployerProfileForm';
import { Employer } from '@/app/jobs/types';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

async function fetchProfile(jwt: string): Promise<Employer | null> {
    try {
        const res = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/employer/profile`, {
            headers: { Authorization: `Bearer ${jwt}` },
            cache: 'no-store',
        });
        if (!res.ok) return null;
        const data = await res.json();
        return data.profile ?? null;
    } catch {
        return null;
    }
}

export const metadata = { title: 'Company Profile | BPU Employer' };

export default async function EmployerProfilePage() {
    const session = await getBPUSession();
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value ?? '';
    const profile = await fetchProfile(jwt);

    return <EmployerProfileForm initialProfile={profile} jwt={jwt} />;
}
