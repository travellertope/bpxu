import { cookies } from 'next/headers';
import { jwtVerify, JWTPayload } from 'jose';

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

/** JWT payload shape expected from WordPress SSO token */
interface BPUJWTPayload extends JWTPayload {
    user_id: number;
    email: string;
    display_name: string;
    username?: string;
    roles: string[];
    profile?: ACFProfile;
    cv_url?: string;
}

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';
const JWT_SECRET = process.env.BPU_JWT_SECRET || '';

/**
 * Server-side JWT-based SSO Authentication
 * Reads `bpu_session` httpOnly cookie, verifies the JWT signature using jose,
 * and extracts user data from the payload. For full profile data, optionally
 * fetches from WP REST API using the JWT as a Bearer token.
 */
export async function getBPUSession(): Promise<SessionResult> {
    const cookieStore = await cookies();
    const sessionCookie = cookieStore.get('bpu_session');

    if (!sessionCookie?.value) {
        return { authenticated: false, user: null };
    }

    try {
        if (!JWT_SECRET) {
            console.error('BPU_JWT_SECRET environment variable is not set.');
            return { authenticated: false, user: null };
        }

        // Verify and decode the JWT using jose
        const secret = new TextEncoder().encode(JWT_SECRET);
        const { payload } = await jwtVerify(sessionCookie.value, secret) as { payload: BPUJWTPayload };

        // Build user from JWT payload
        const user: BPUUser = {
            id: payload.user_id,
            username: payload.username || payload.email,
            email: payload.email,
            display_name: payload.display_name,
            roles: payload.roles || [],
            profile: payload.profile || {
                first_name: payload.display_name?.split(' ')[0] || '',
                last_name: payload.display_name?.split(' ').slice(1).join(' ') || '',
                phone_number: '',
            },
            cv_url: payload.cv_url,
        };

        // Optionally fetch full profile from WP REST API for complete ACF data
        if (!payload.profile) {
            try {
                const profileResponse = await fetch(
                    `${WP_BACKEND_URL}/wp-json/bpu/v1/sso/profile`,
                    {
                        headers: {
                            'Authorization': `Bearer ${sessionCookie.value}`,
                            'Cache-Control': 'no-store',
                        },
                    }
                );

                if (profileResponse.ok) {
                    const profileData = await profileResponse.json();
                    if (profileData.profile) {
                        user.profile = profileData.profile;
                    }
                    if (profileData.cv_url) {
                        user.cv_url = profileData.cv_url;
                    }
                }
            } catch {
                // Profile fetch is optional — proceed with JWT-extracted data
                console.warn('Optional profile fetch from WP REST API failed; using JWT payload data.');
            }
        }

        return {
            authenticated: true,
            user,
        };
    } catch (error) {
        console.error('JWT Session Verification Error:', error);
        return { authenticated: false, user: null };
    }
}
