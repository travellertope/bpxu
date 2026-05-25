import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET(request: NextRequest) {
    const searchParams = request.nextUrl.searchParams;
    const token = searchParams.get('token');
    const from = searchParams.get('from');

    if (!token) {
        return NextResponse.redirect(new URL('/?auth_error=missing_token', request.url));
    }

    try {
        // Exchange token for user data and JWT
        const response = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/sso/exchange`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ token }),
        });

        const data = await response.json();

        if (!response.ok || !data.success || !data.jwt) {
            console.error('SSO Exchange Failed:', data);
            return NextResponse.redirect(new URL('/?auth_error=exchange_failed', request.url));
        }

        // Set the JWT as an httpOnly session cookie
        const cookieStore = await cookies();
        cookieStore.set('bpu_session', data.jwt, {
            httpOnly: true,
            secure: process.env.NODE_ENV === 'production',
            sameSite: 'lax',
            path: '/',
            maxAge: 60 * 60 * 24, // 24 hours
        });

        // Redirect based on 'from' param
        if (from === 'paired') {
            return NextResponse.redirect(new URL('/paired', request.url));
        }

        return NextResponse.redirect(new URL('/', request.url));
    } catch (error) {
        console.error('SSO Callback Error:', error);
        return NextResponse.redirect(new URL('/?auth_error=server_error', request.url));
    }
}
