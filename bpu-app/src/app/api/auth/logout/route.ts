import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET(request: NextRequest) {
    const cookieStore = await cookies();
    
    // Clear the BPU session cookie
    cookieStore.delete('bpu_session');

    // Redirect to WordPress logout. The return URL includes ?logged_out=1 so
    // the app skips the auto-SSO bounce and shows the guest view instead.
    const returnUrl = encodeURIComponent(`${request.nextUrl.origin}/?logged_out=1`);
    const wpLogoutUrl = `${WP_BACKEND_URL}/wp-login.php?action=logout&redirect_to=${returnUrl}`;

    return NextResponse.redirect(wpLogoutUrl);
}
