export interface JobListing {
    id: number;
    title: string;
    company: string;
    location: string;
    type: string;
    apply_url: string;
    match_score?: number;
    date_posted: string;
}

export interface CourseItem {
    id: number;
    title: string;
    provider: string;
    category: string;
    learn_more_url: string;
    status?: 'Not Started' | 'In Progress' | 'Completed';
}

export interface CVReview {
    id: number;
    title: string;
    critique: string;
    score: number | null;
    cv_link: string;
    date: string;
    reviewer: string;
}

export interface EventItem {
    id: number;
    title: string;
    description: string;
    start_date: string;
    end_date: string;
    venue: string;
    cost: string;
    url: string;
    image: string;
    is_virtual: boolean;
    register_url: string;
}

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

/**
 * Headless API integration utility for app.blackprofessionals.uk
 */
export class BPUApi {

    /**
     * Get Job Recommendations based on candidate CV & ACF profiles (Vertex AI Matches)
     */
    static async getJobRecommendations(userId: number, jwt: string): Promise<JobListing[]> {
        try {
            // Fetch user profile for scoring
            let profile: Record<string, string> = {};
            try {
                const profileRes = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/sso/profile`, {
                    headers: { 'Authorization': `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
                });
                if (profileRes.ok) {
                    const pd = await profileRes.json();
                    profile = pd.profile || {};
                }
            } catch { /* scoring will use defaults */ }

            const response = await fetch(`${WP_BACKEND_URL}/wp-json/wp/v2/job_listing?per_page=10`, {
                headers: { 'Cache-Control': 'no-store' },
            });
            if (!response.ok) return [];

            const jobs = await response.json();

            return jobs
                .map((job: Record<string, unknown>) => {
                    const meta = (job.meta as Record<string, string>) || {};
                    const jobType = (meta._job_type || '').toLowerCase();
                    const jobTitle = ((job.title as { rendered: string })?.rendered || '').toLowerCase();

                    const score = BPUApi.scoreJobMatch(profile, jobType, jobTitle);

                    return {
                        id: job.id as number,
                        title: (job.title as { rendered: string })?.rendered || '',
                        company: meta._company_name || 'Partner Organisation',
                        location: meta._job_location || 'United Kingdom',
                        type: meta._job_type || 'Full-time',
                        apply_url: meta._application || '#',
                        match_score: score,
                        date_posted: new Date(job.date as string).toLocaleDateString('en-GB'),
                    };
                })
                .sort((a: JobListing, b: JobListing) => (b.match_score ?? 0) - (a.match_score ?? 0))
                .slice(0, 5);
        } catch (error) {
            console.error('Failed to fetch job recommendations:', error);
            return [];
        }
    }

    private static scoreJobMatch(
        profile: Record<string, string>,
        jobType: string,
        jobTitle: string,
    ): number {
        let score = 60; // baseline

        const userIndustry   = (profile.industry || '').toLowerCase();
        const userField      = (profile.industryfield_of_expertise || '').toLowerCase();
        const userSkills     = (profile.skills_separate || '').toLowerCase();
        const userStatus     = (profile.current_employment_status || '').toLowerCase();
        const userExp        = parseInt(profile.years_of_experience || '0', 10);

        // Industry / field keyword overlap
        const industryWords = userIndustry.split(/\W+/).filter(w => w.length > 3);
        const fieldWords    = userField.split(/\W+/).filter(w => w.length > 3);
        const skillWords    = userSkills.split(/[,\s]+/).filter(w => w.length > 2);
        const target        = `${jobType} ${jobTitle}`;

        for (const w of industryWords) { if (target.includes(w)) score += 8; }
        for (const w of fieldWords)    { if (target.includes(w)) score += 6; }
        for (const w of skillWords)    { if (target.includes(w)) score += 4; }

        // Experience bonus
        if (userExp >= 5)  score += 6;
        if (userExp >= 10) score += 4;

        // Employment fit
        if (userStatus.includes('looking') || userStatus.includes('student')) score += 5;

        return Math.min(99, Math.max(50, score));
    }

    /**
     * Track Job Click Event and increment click logs in legacy WordPress tracking tables
     */
    static async trackJobClick(jobId: number, userId: number | null, wpCookieHeader?: string): Promise<boolean> {
        try {
            const headers: HeadersInit = { 'Content-Type': 'application/json' };
            if (wpCookieHeader) {
                headers['Cookie'] = wpCookieHeader;
            }

            const response = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/track-click`, {
                method: 'POST',
                headers,
                body: JSON.stringify({
                    job_id: jobId,
                    user_id: userId || 0
                })
            });
            return response.ok;
        } catch (error) {
            console.error('Failed to log job click tracker:', error);
            return false;
        }
    }

    /**
     * Get Courses Directory (Headless Tutor LMS List)
     */
    static async getCourses(_unused?: string): Promise<CourseItem[]> {
        try {
            const response = await fetch(
                `${WP_BACKEND_URL}/wp-json/bpu/v1/courses?per_page=12`,
                { cache: 'no-store' }
            );
            if (!response.ok) return [];
            const data = await response.json();
            return (data.courses || []).map((c: Record<string, unknown>) => ({
                id: c.id as number,
                title: c.title as string,
                provider: (c.provider as string) || 'BPU Partner',
                category: (c.category as string) || 'Professional Development',
                learn_more_url: (c.learn_more_url as string) || '#',
                status: 'Not Started' as const,
            }));
        } catch (error) {
            console.error('Failed to fetch courses:', error);
            return [];
        }
    }

    /**
     * Track Course Progress / Learn More Clicks in Tutor LMS
     */
    static async trackCourseProgress(courseId: number, wpCookieHeader: string): Promise<boolean> {
        try {
            const response = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/courses/progress`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Cookie': wpCookieHeader
                },
                body: JSON.stringify({ course_id: courseId })
            });
            return response.ok;
        } catch (error) {
            console.error('Failed to log course progress redirect:', error);
            return false;
        }
    }

    /**
     * Get Manual CV Clinic Critique Reviews from WordPress backend
     */
    static async getCVClinicReviews(jwt: string): Promise<CVReview[]> {
        try {
            const response = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/member/cv-reviews`, {
                headers: { 'Authorization': `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
            });
            if (!response.ok) return [];
            const data = await response.json();
            return data.reviews || [];
        } catch (error) {
            console.error('Failed to fetch CV Clinic reviews list:', error);
            return [];
        }
    }

    /**
     * Get upcoming events from The Events Calendar via BPU connector
     */
    static async getEvents(perPage = 12): Promise<EventItem[]> {
        try {
            const response = await fetch(
                `${WP_BACKEND_URL}/wp-json/bpu/v1/events?per_page=${perPage}`,
                { cache: 'no-store' }
            );
            if (!response.ok) return [];
            const data = await response.json();
            return data.events || [];
        } catch (error) {
            console.error('Failed to fetch events:', error);
            return [];
        }
    }
}
