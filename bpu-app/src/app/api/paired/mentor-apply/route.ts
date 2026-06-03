import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(request: NextRequest) {
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    if (!jwt) {
        return NextResponse.json({ error: 'You must be signed in to apply.' }, { status: 401 });
    }

    let body: Record<string, unknown>;
    try {
        body = await request.json();
    } catch {
        return NextResponse.json({ error: 'Invalid request body.' }, { status: 400 });
    }

    const required = ['job_title', 'employer', 'years_exp', 'expertise', 'availability', 'has_mentored', 'motivation'];
    for (const field of required) {
        if (!body[field]) {
            return NextResponse.json({ error: `${field} is required.` }, { status: 400 });
        }
    }

    try {
        const res = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/paired/mentor-apply`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${jwt}`,
            },
            body: JSON.stringify(body),
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            return NextResponse.json(
                { error: data.message || data.error || 'Application failed.' },
                { status: res.status }
            );
        }

        return NextResponse.json({ success: true, ...data });
    } catch {
        return NextResponse.json({ error: 'Something went wrong.' }, { status: 500 });
    }
}
