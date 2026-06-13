import { NextResponse } from 'next/server';
import type { NextRequest } from 'next/server';

export function middleware(req: NextRequest) {
  const url = req.nextUrl;
  const hostname = req.headers.get('host') || '';

  if (hostname.includes('pairedbybpu.uk')) {
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
      return NextResponse.rewrite(url);
    }
  }

  const response = NextResponse.next();
  response.headers.set('x-next-pathname', url.pathname);
  return response;
}

export const config = {
  matcher: ['/((?!api|_next/static|_next/image|favicon.ico|sitemap.xml|robots.txt).*)'],
};
