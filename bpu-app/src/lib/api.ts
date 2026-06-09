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
    excerpt: string;
    provider: string;
    category: string;
    categories: string[];
    tags: string[];
    learn_more_url: string;
    image: string;
    duration: string;
    level: string;
    status?: 'Not Started' | 'In Progress' | 'Enrolled' | 'Completed';
    progress?: number;
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

    static scoreMentorMatch(
        memberProfile: Record<string, string>,
        mentorProfile: Record<string, string>,
    ): number {
        let score = 0;
        const uInd = (memberProfile.industry || '').toLowerCase();
        const uField = (memberProfile.industryfield_of_expertise || '').toLowerCase();
        const uSkills = (memberProfile.skills_separate || '').toLowerCase();
        const mInd = (mentorProfile.industry || '').toLowerCase();
        const mField = (mentorProfile.industryfield_of_expertise || '').toLowerCase();
        const mSkills = (mentorProfile.skills_separate || '').toLowerCase();

        if (uInd && mInd && uInd === mInd) score += 30;
        if (uField && mField && uField === mField) score += 20;

        const userSkillWords = uSkills.split(/[,\s]+/).filter(w => w.length > 2);
        const mentorSkillWords = new Set(mSkills.split(/[,\s]+/).filter(w => w.length > 2));
        let skillScore = 0;
        for (const w of userSkillWords) {
            if (mentorSkillWords.has(w)) skillScore += 5;
        }
        score += Math.min(20, skillScore);

        return Math.min(99, Math.max(0, score));
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
    static async getCourses(): Promise<CourseItem[]> {
        try {
            const response = await fetch(
                `${WP_BACKEND_URL}/wp-json/bpu/v1/courses?per_page=50`,
                { cache: 'no-store' }
            );
            if (!response.ok) return [];
            const data = await response.json();
            return (data.courses || []).map((c: Record<string, unknown>) => ({
                id: c.id as number,
                title: c.title as string,
                excerpt: (c.excerpt as string) || '',
                provider: (c.provider as string) || 'BPU Partner',
                category: (c.category as string) || 'Professional Development',
                categories: (c.categories as string[]) || [],
                tags: (c.tags as string[]) || [],
                learn_more_url: (c.learn_more_url as string) || '#',
                image: (c.image as string) || '',
                duration: (c.duration as string) || '',
                level: (c.level as string) || '',
                status: 'Not Started' as const,
                progress: 0,
            }));
        } catch (error) {
            console.error('Failed to fetch courses:', error);
            return [];
        }
    }

    static async getEnrolledCourses(jwt: string): Promise<CourseItem[]> {
        try {
            const response = await fetch(
                `${WP_BACKEND_URL}/wp-json/bpu/v1/member/enrolled-courses`,
                { cache: 'no-store', headers: { 'Authorization': `Bearer ${jwt}` } }
            );
            if (!response.ok) return [];
            const data = await response.json();
            return (data.courses || []).map((c: Record<string, unknown>) => ({
                id: c.id as number,
                title: c.title as string,
                excerpt: (c.excerpt as string) || '',
                provider: (c.provider as string) || 'BPU Partner',
                category: (c.category as string) || 'Professional Development',
                categories: (c.categories as string[]) || [],
                tags: (c.tags as string[]) || [],
                learn_more_url: (c.learn_more_url as string) || '#',
                image: (c.image as string) || '',
                duration: (c.duration as string) || '',
                level: (c.level as string) || '',
                status: (c.status as CourseItem['status']) || 'Enrolled',
                progress: (c.progress as number) || 0,
            }));
        } catch {
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
    static async getEvents(perPage = 50): Promise<EventItem[]> {
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

    static async getRegisteredEvents(jwt: string): Promise<EventItem[]> {
        try {
            const response = await fetch(
                `${WP_BACKEND_URL}/wp-json/bpu/v1/member/registered-events`,
                { cache: 'no-store', headers: { 'Authorization': `Bearer ${jwt}` } }
            );
            if (!response.ok) return [];
            const data = await response.json();
            return data.events || [];
        } catch {
            return [];
        }
    }

    /**
     * Save Pro member email preferences (weekly digest opt-in, target role).
     */
    static async updatePreferences(prefs: { weekly_emails?: boolean; target_role?: string }): Promise<boolean> {
        try {
            const res = await fetch('/api/member/preferences', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(prefs),
            });
            return res.ok;
        } catch {
            return false;
        }
    }

    /**
     * Submit a Pro CV review request to the BPU team.
     */
    static async requestCVReview(): Promise<{ success: boolean; request_id?: number; error?: string }> {
        try {
            const res = await fetch('/api/member/request-cv-review', { method: 'POST' });
            const data = await res.json();
            if (!res.ok) return { success: false, error: data.message || data.error || 'Request failed.' };
            return { success: true, request_id: data.request_id };
        } catch {
            return { success: false, error: 'Network error.' };
        }
    }
}
