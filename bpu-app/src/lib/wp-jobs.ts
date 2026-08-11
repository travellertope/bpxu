const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

/**
 * Builds the WordPress job-listing URL with a cache-buster.
 *
 * Something in front of WordPress caches /wp-json/bpu/v1/jobs by URL and
 * ignores the no-store headers the connector sends: on 11 Aug that endpoint
 * was still serving a 9 Aug snapshot — 291 roles, including listings that
 * expired on the 9th — while a freshly-added REST route on the same site
 * returned current data. That is why renaming the Next.js route from
 * /api/jobs to /api/job-listings changed nothing: both proxy to this same
 * WordPress URL, and the cache is keyed on that, not on our path.
 *
 * A unique parameter per request misses that cache every time. WordPress
 * ignores unregistered REST args, so it does not affect the query.
 *
 * This is a workaround, not the cure — the cache in front of WordPress still
 * needs to stop caching this endpoint. It keeps the board correct meanwhile.
 */
export function wpJobsUrl(params?: URLSearchParams | Record<string, string>): string {
    const search = new URLSearchParams(params as Record<string, string> | undefined);
    search.set('_cb', `${Date.now().toString(36)}${Math.random().toString(36).slice(2, 8)}`);
    return `${WP}/wp-json/bpu/v1/jobs?${search}`;
}

/** Fetch options that opt out of every caching layer we control. */
export const NO_STORE: RequestInit = {
    cache: 'no-store',
    headers: { 'Cache-Control': 'no-cache, no-store', Pragma: 'no-cache' },
};
