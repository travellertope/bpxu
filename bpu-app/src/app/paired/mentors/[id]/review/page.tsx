import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { decodeHtml } from '@/lib/utils';
import ReviewForm from './ReviewForm';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export default async function SubmitReviewPage({
    params,
}: {
    params: Promise<{ id: string }>;
}) {
    const { id } = await params;
    const session = await getBPUSession();

    if (!session.authenticated || !session.user) {
        redirect(`/login?returnTo=/paired/mentors/${id}/review`);
    }

    let mentorName = '';
    let fetchError = '';

    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/mentors/${id}`, {
            cache: 'no-store',
        });
        if (res.ok) {
            const data = await res.json();
            const mentor = data.mentor;
            if (mentor) {
                mentorName = decodeHtml(mentor.display_name as string);
            } else {
                fetchError = 'Mentor not found.';
            }
        } else if (res.status === 404) {
            fetchError = 'Mentor not found.';
        } else {
            fetchError = 'Failed to load mentor profile.';
        }
    } catch {
        fetchError = 'Could not connect to the server.';
    }

    if (fetchError) {
        return (
            <div className="wrap py-12">
                <div className="card card-p text-center space-y-4">
                    <p className="text-text-2">{fetchError}</p>
                    <a href="/paired/mentors" className="btn btn-outline btn-sm">Back to mentors</a>
                </div>
            </div>
        );
    }

    return (
        <div className="fade-up" style={{ maxWidth: 640 }}>
            <a href={`/paired/mentors/${id}`} className="text-sm text-text-3 hover:text-brand flex items-center gap-1 mb-6" style={{ width: 'fit-content' }}>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                Back to profile
            </a>

            <h1 className="text-3xl font-bold mb-2">Review {mentorName}</h1>
            <p className="text-text-2 mb-8">Share your experience to help other mentees.</p>

            <ReviewForm mentorId={Number(id)} mentorName={mentorName} />
        </div>
    );
}
