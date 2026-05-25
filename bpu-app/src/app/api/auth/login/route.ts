import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(request: NextRequest) {
    const cookieStore = await cookies();

    let body: { username?: string; password?: string };
    try { body = await request.json(); } catch { return NextResponse.json({ error: 'Invalid request' }, { status: 400 }); }

    if (!body.username || !body.password) {
        return NextResponse.json({ error: 'Username and password are required.' }, { status: 400 });
    }

    const res = await fetch(`${WP_URL}/wp-json/bpu/v1/auth/login`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username: body.username, password: body.password }),
    });

    const data = await res.json();

    if (!res.ok || !data.jwt) {
        return NextResponse.json({ error: data.message || 'Invalid username or password.' }, { status: res.status || 401 });
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
