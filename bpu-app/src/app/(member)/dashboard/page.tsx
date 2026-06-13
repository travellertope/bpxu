import { getBPUSession } from '@/lib/auth';
import { BPUApi } from '@/lib/api';
import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';
import DashboardOverview from './DashboardOverview';

export default async function DashboardPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/dashboard');
    }

    const user = session.user;
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value ?? '';

    const [jobs, courses, reviews, events] = await Promise.all([
        BPUApi.getJobRecommendations(user.id, jwt),
        BPUApi.getEnrolledCourses(jwt),
        BPUApi.getCVClinicReviews(jwt),
        BPUApi.getRegisteredEvents(jwt),
    ]);

    return (
        <DashboardOverview
            user={user}
            jobs={jobs}
            courses={courses}
            reviews={reviews}
            events={events}
        />
    );
}
