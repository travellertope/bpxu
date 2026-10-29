import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(request: NextRequest) {
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    if (!jwt) {
        return NextResponse.json({ error: 'Unauthorized. Please log in.' }, { status: 401 });
    }

    let body: Record<string, unknown>;
    try {
        body = await request.json();
    } catch {
        return NextResponse.json({ error: 'Invalid JSON body' }, { status: 400 });
    }

    try {
        const res = await fetch(`${WP_URL}/wp-json/bpu/v1/member/mentorship-request`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${jwt}`,
            },
            body: JSON.stringify(body),
        });

        const data = await res.json();

        if (!res.ok) {
            return NextResponse.json({ error: data.message || 'Could not send mentorship request.' }, { status: res.status });
        }

        return NextResponse.json(data, { status: 200 });
    } catch (err) {
        console.error('Mentorship request proxy error:', err);
        return NextResponse.json({ error: 'Internal server error.' }, { status: 500 });
    }
}
