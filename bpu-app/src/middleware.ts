import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

export function middleware(req: NextRequest) {
  const url = req.nextUrl;
  const hostname = req.headers.get('host') || '';
  const platform = hostname.includes('pairedbybpu.uk') ? 'paired' : 'bpu';

  // Permanently redirect the old app. subdomain to web.
  if (hostname.startsWith('app.blackprofessionals.uk')) {
    const destination = `https://web.blackprofessionals.uk${url.pathname}${url.search}`;
    return NextResponse.redirect(destination, { status: 301 });
  }

  if (hostname.includes('pairedbybpu.uk')) {
    // Admin tools are managed from the BPU domain; send anyone hitting
    // /admin on the PAIRED domain straight to the PAIRED dashboard
    // instead of letting it fall through to a rewritten 404.
    if (url.pathname.startsWith('/admin')) {
      return NextResponse.redirect(new URL('/paired/dashboard', url));
    }

    if (
      !url.pathname.startsWith('/paired') &&
      !url.pathname.startsWith('/_next') &&
      !url.pathname.startsWith('/api') &&
      !url.pathname.startsWith('/login') &&
      !url.pathname.startsWith('/register') &&
      !url.pathname.startsWith('/forgot-password') &&
      !url.pathname.startsWith('/reset-password') &&
      !url.pathname.includes('.')
    ) {
      url.pathname = `/paired${url.pathname === '/' ? '' : url.pathname}`;
      const rewritten = NextResponse.rewrite(url);
      rewritten.headers.set('x-bpu-platform', platform);
      return rewritten;
    }
  }

  const response = NextResponse.next();
  response.headers.set('x-next-pathname', url.pathname);
  response.headers.set('x-bpu-platform', platform);
  return response;
}

export const config = {
  matcher: ['/((?!api|_next/static|_next/image|favicon.ico|sitemap.xml|robots.txt).*)'],
};
