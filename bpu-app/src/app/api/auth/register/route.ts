import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';
import { verifyRecaptcha } from '@/lib/recaptcha';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(request: NextRequest) {
    const cookieStore = await cookies();

    let body: Record<string, unknown>;
    try { body = await request.json(); } catch { return NextResponse.json({ error: 'Invalid request' }, { status: 400 }); }

    const captchaOk = await verifyRecaptcha(body.recaptcha_token as string | undefined);
    if (!captchaOk) {
        return NextResponse.json({ error: 'reCAPTCHA verification failed. Please try again.' }, { status: 400 });
    }

    // Strip the token before forwarding to WP
    const { recaptcha_token: _token, ...wpBody } = body;

    const res = await fetch(`${WP_URL}/wp-json/bpu/v1/auth/register`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(wpBody),
    });

    const data = await res.json();

    if (!res.ok || !data.jwt) {
        return NextResponse.json({ error: data.message || 'Registration failed. Please try again.' }, { status: res.status || 400 });
    }

    cookieStore.set('bpu_session', data.jwt, {
        httpOnly: true,
        secure: process.env.NODE_ENV === 'production',
        sameSite: 'lax',
        path: '/',
        maxAge: 60 * 60 * 24,
    });

    return NextResponse.json({ success: true });
}
