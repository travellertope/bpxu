import { NextRequest, NextResponse } from 'next/server';
import { wpJobsUrl, NO_STORE } from '@/lib/wp-jobs';


// Route-segment-level cache opt-out — stronger than per-fetch/response-header
// settings, since it tells Next.js at build time this route can never be
// statically optimized or cached, closing any ambiguity in the framework's
// default caching heuristics for this endpoint.
export const dynamic = 'force-dynamic';
export const fetchCache = 'force-no-store';
export const revalidate = 0;

// Renamed from /api/jobs (2026-08-10) to escape a suspected stale edge entry.
// That did not work, and the reason is now known: the cache is not on this
// path at all, it is on the WordPress URL both paths proxy to. Renaming here
// could never have helped. /api/jobs is kept as an alias so clients running an
// older cached bundle can still refresh. See lib/wp-jobs for the cache-buster
// that actually gets past it.
export async function GET(request: NextRequest) {
    try {
        const res = await fetch(wpJobsUrl(request.nextUrl.searchParams), NO_STORE);
        const data = await res.json().catch(() => ({}));
        return NextResponse.json(data, {
            status: res.status,
            headers: { 'Cache-Control': 'no-store, must-revalidate' },
        });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
