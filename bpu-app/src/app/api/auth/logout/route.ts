import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

export async function GET(request: NextRequest) {
    const cookieStore = await cookies();
    cookieStore.delete('bpu_session');

    // Redirect within the app — no WP bounce needed since auth is JWT-based.
    // ?logged_out=1 prevents the auto-SSO redirect on the homepage.
    return NextResponse.redirect(new URL('/?logged_out=1', request.nextUrl.origin));
}
