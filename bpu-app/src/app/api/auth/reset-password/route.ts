import { NextRequest, NextResponse } from 'next/server';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(request: NextRequest) {
    let body: { token?: string; password?: string };
    try { body = await request.json(); } catch { return NextResponse.json({ error: 'Invalid request.' }, { status: 400 }); }

    if (!body.token || !body.password) {
        return NextResponse.json({ error: 'Token and password are required.' }, { status: 400 });
    }

    if (body.password.length < 8) {
        return NextResponse.json({ error: 'Password must be at least 8 characters.' }, { status: 400 });
    }

    try {
        const res = await fetch(`${WP_URL}/wp-json/bpu/v1/auth/reset-password`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ token: body.token, password: body.password }),
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            return NextResponse.json({ error: data.message || 'Reset failed.' }, { status: res.status });
        }

        return NextResponse.json({ success: true, message: data.message });
    } catch {
        return NextResponse.json({ error: 'Something went wrong. Please try again.' }, { status: 500 });
    }
}
