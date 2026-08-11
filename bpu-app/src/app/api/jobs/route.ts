import { NextRequest, NextResponse } from 'next/server';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export const dynamic = 'force-dynamic';
export const fetchCache = 'force-no-store';
export const revalidate = 0;

/**
 * Alias of /api/job-listings.
 *
 * This path was removed when the listing endpoint was renamed, on the theory
 * that a new URL would escape a stuck edge cache. But any browser holding a
 * cached copy of the /jobs HTML still runs the JS bundle from that build,
 * which fetches THIS path — so removing it turned those clients' refresh into
 * a permanent 404, freezing their board on whatever snapshot they had.
 * Keeping the alias lets them recover on their own.
 */
export async function GET(request: NextRequest) {
    const qs = request.nextUrl.searchParams.toString();
    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/jobs${qs ? `?${qs}` : ''}`, {
            cache: 'no-store',
            headers: { 'Cache-Control': 'no-store' },
        });
        const data = await res.json().catch(() => ({}));
        return NextResponse.json(data, {
            status: res.status,
            headers: { 'Cache-Control': 'no-store, must-revalidate' },
        });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
