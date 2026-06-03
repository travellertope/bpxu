import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(request: NextRequest) {
    const cookieStore = await cookies();

    let body: Record<string, unknown>;
    try {
        body = await request.json();
    } catch {
        return NextResponse.json({ error: 'Invalid request body.' }, { status: 400 });
    }

    try {
        const res = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/employer/register`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            return NextResponse.json(
                { error: data.message || data.error || 'Registration failed.' },
                { status: res.status }
            );
        }

        if (data.jwt) {
            cookieStore.set('bpu_session', data.jwt, {
                httpOnly: true,
                secure: process.env.NODE_ENV === 'production',
                sameSite: 'lax',
                path: '/',
                maxAge: 60 * 60 * 24 * 7,
            });
        }

        return NextResponse.json({ success: true, ...data });
    } catch {
        return NextResponse.json({ error: 'Something went wrong. Please try again.' }, { status: 500 });
    }
}
