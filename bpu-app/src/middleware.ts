import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

export function middleware(req: NextRequest) {
    const url = req.nextUrl;
    const hostname = req.headers.get('host') || '';

    // If the request comes from pairedbybpu.uk (or www.), rewrite to /paired route group
    if (hostname.includes('pairedbybpu.uk')) {
        // If the path doesn't already start with /paired (or it's not a static/api asset), rewrite it
        if (
            !url.pathname.startsWith('/paired') && 
            !url.pathname.startsWith('/_next') &&
            !url.pathname.startsWith('/api') &&
            !url.pathname.includes('.')
        ) {
            url.pathname = `/paired${url.pathname === '/' ? '' : url.pathname}`;
            return NextResponse.rewrite(url);
        }
    }

    return NextResponse.next();
}

export const config = {
    matcher: [
        /*
         * Match all request paths except for the ones starting with:
         * - api (API routes)
         * - _next/static (static files)
         * - _next/image (image optimization files)
         * - favicon.ico, sitemap.xml, robots.txt (metadata files)
         */
        '/((?!api|_next/static|_next/image|favicon.ico|sitemap.xml|robots.txt).*)',
    ],
};
