import { getBPUSession } from '@/lib/auth';
import { BPUApi } from '@/lib/api';
import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';
import ClientDashboard from './ClientDashboard';

export default async function MemberPortal() {
  const session = await getBPUSession();

  if (!session.authenticated || !session.user) {
    redirect('/login');
  }

  const user = session.user!;
  const cookieStore = await cookies();
  const jwt = cookieStore.get('bpu_session')?.value || '';

  const [jobs, courses, reviews, events] = await Promise.all([
    BPUApi.getJobRecommendations(user.id, jwt),
    BPUApi.getEnrolledCourses(jwt),
    BPUApi.getCVClinicReviews(jwt),
    BPUApi.getRegisteredEvents(jwt),
  ]);

  return (
    <ClientDashboard
      user={user}
      initialJobs={jobs}
      initialCourses={courses}
      initialReviews={reviews}
      initialEvents={events}
      jwt={jwt}
    />
  );
}
