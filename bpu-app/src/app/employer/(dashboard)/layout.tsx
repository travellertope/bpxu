import { getBPUSession } from '@/lib/auth';
import { headers } from 'next/headers';
import { redirect } from 'next/navigation';
import EmployerShell from './EmployerShell';

export default async function EmployerDashboardLayout({ children }: { children: React.ReactNode }) {
    const session = await getBPUSession();

    if (!session.authenticated || !session.user) {
        redirect('/employer/register');
    }

    const roles: string[] = session.user.roles || [];
    if (!roles.includes('bpu_employer') && !roles.includes('administrator')) {
        redirect('/employer');
    }

    const headerList = await headers();
    const pathname = headerList.get('x-next-pathname') || headerList.get('x-invoke-path') || '';

    return (
        <EmployerShell
            currentPath={pathname}
            companyName={session.user.display_name || 'Employer'}
            userEmail={session.user.email || ''}
        >
            {children}
        </EmployerShell>
    );
}
