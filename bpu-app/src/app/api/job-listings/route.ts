import { NextRequest, NextResponse } from 'next/server';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

// Route-segment-level cache opt-out — stronger than per-fetch/response-header
// settings, since it tells Next.js at build time this route can never be
// statically optimized or cached, closing any ambiguity in the framework's
// default caching heuristics for this endpoint.
export const dynamic = 'force-dynamic';
export const fetchCache = 'force-no-store';
export const revalidate = 0;

// Renamed from /api/jobs (2026-08-10): the job board kept serving a frozen
// multi-day-old snapshot at /api/jobs despite every cache-control fix at the
// fetch, response-header, and route-segment level, and despite Cloudflare
// being fully bypassed for a direct test — consistent with a stale entry
// stuck in a CDN/edge cache keyed by that exact URL that couldn't be purged
// without dashboard access. A URL that has never been requested before has
// no stale entry to serve, so this sidesteps the problem outright. If the
// same staleness reappears at this new path, that would rule out URL-keyed
// caching entirely and point to something else.
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
