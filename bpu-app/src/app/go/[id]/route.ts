import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

/**
 * Server-side redirect route for outbound job clicks.
 * Tracking happens here on the server — no JS required, works with
 * right-click → open in new tab, ad blockers, etc.
 */
export async function GET(
    request: NextRequest,
    { params }: { params: Promise<{ id: string }> }
) {
    const { id } = await params;
    const jobId = parseInt(id, 10);

    if (!jobId) {
        return NextResponse.redirect(new URL('/jobs', request.url));
    }

    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    const authHeaders: HeadersInit = {};
    if (jwt) authHeaders['Authorization'] = `Bearer ${jwt}`;

    // Track the click and get the apply URL atomically
    let applyUrl = '';
    try {
        const trackRes = await fetch(
            `${WP_BACKEND_URL}/wp-json/bpu/v1/jobs/${jobId}/click`,
            { method: 'POST', headers: authHeaders }
        );
        if (trackRes.ok) {
            const data = await trackRes.json();
            applyUrl = data.apply_url || '';
        }
    } catch { /* non-blocking */ }

    // Fallback: fetch the job directly if click endpoint didn't return apply_url
    if (!applyUrl) {
        try {
            const jobRes = await fetch(
                `${WP_BACKEND_URL}/wp-json/bpu/v1/jobs/${jobId}?skip_impression=1`,
                { headers: authHeaders, cache: 'no-store' }
            );
            if (jobRes.ok) {
                const data = await jobRes.json();
                applyUrl = data.job?.apply_url || '';
            }
        } catch { /* non-blocking */ }
    }

    if (!applyUrl) {
        return NextResponse.redirect(new URL(`/jobs/${jobId}`, request.url));
    }

    // Security: only redirect to http/https URLs
    try {
        const parsed = new URL(applyUrl);
        if (parsed.protocol !== 'https:' && parsed.protocol !== 'http:') {
            return NextResponse.redirect(new URL('/jobs', request.url));
        }
    } catch {
        return NextResponse.redirect(new URL('/jobs', request.url));
    }

    return NextResponse.redirect(applyUrl, { status: 302 });
}
