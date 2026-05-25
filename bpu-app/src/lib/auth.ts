import { cookies } from 'next/headers';

export interface ACFProfile {
    first_name: string;
    last_name: string;
    phone_number: string;
    what_is_your_age?: {
        day: string;
        month: string;
    };
    birthday?: string;
    age_range?: string;
    what_is_your_gender?: string;
    your_sexuality?: string;
    level_of_education?: string;
    industry?: string;
    country_location?: string;
    where_in_the_uk?: string;
    location_city?: string;
    current_employment_status?: string;
    industryfield_of_expertise?: string;
    expertise_not_listed?: string;
    years_of_experience?: string;
    skills_separate?: string;
    'first-generation_immigrant'?: string;
    how_would_you_best_describe_your_ethnicity?: string;
    user_bio?: string;
    do_you_have_any_disabilities_or_accessibility_needs_we_should_be_aware_of?: string;
    other_disability?: string;
}

export interface BPUUser {
    id: number;
    username: string;
    email: string;
    display_name: string;
    roles: string[];
    profile: ACFProfile;
    cv_url?: string;
}

export interface SessionResult {
    authenticated: boolean;
    user: BPUUser | null;
}

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

/**
 * Server-side SSO Authentication Checker
 * Reads standard WordPress logged-in cookies directly from incoming requests
 * and forwards them to the AWS WordPress backend to validate the session.
 */
export async function getBPUSession(): Promise<SessionResult> {
    const cookieStore = await cookies();
    
    // Find standard WordPress logged-in cookie starting with "wordpress_logged_in_"
    const allCookies = cookieStore.getAll();
    const wpCookie = allCookies.find(c => c.name.startsWith('wordpress_logged_in_'));

    if (!wpCookie) {
        return { authenticated: false, user: null };
    }

    try {
        // Prepare Cookie Header String to forward directly to WordPress
        const cookieHeader = `${wpCookie.name}=${wpCookie.value}`;

        const response = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/sso/validate`, {
            method: 'GET',
            headers: {
                'Cookie': cookieHeader,
                'Cache-Control': 'no-store', // Always bypass HTTP cache for auth calls
            },
        });

        if (!response.ok) {
            return { authenticated: false, user: null };
        }

        const data = await response.json();
        return {
            authenticated: true,
            user: data.user as BPUUser
        };

    } catch (error) {
        console.error('SSO Session Validation Error:', error);
        return { authenticated: false, user: null };
    }
}
