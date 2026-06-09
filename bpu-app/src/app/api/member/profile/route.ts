import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET() {
    const cookieStore = await cookies();
    const session = cookieStore.get('bpu_session');

    if (!session?.value) {
        return NextResponse.json({ error: 'Not authenticated' }, { status: 401 });
    }

    const response = await fetch(`${WP_URL}/wp-json/bpu/v1/sso/profile`, {
        method: 'GET',
        headers: { 'Authorization': `Bearer ${session.value}` },
        cache: 'no-store',
    });

    const data = await response.json();

    if (!response.ok) {
        return NextResponse.json({ error: data?.message || 'Failed to fetch profile' }, { status: response.status });
    }

    return NextResponse.json(data);
}

export async function POST(request: NextRequest) {
    const cookieStore = await cookies();
    const session = cookieStore.get('bpu_session');

    if (!session?.value) {
        return NextResponse.json({ error: 'Not authenticated' }, { status: 401 });
    }

    let body: Record<string, unknown>;
    try {
        body = await request.json();
    } catch {
        return NextResponse.json({ error: 'Invalid JSON body' }, { status: 400 });
    }

    const response = await fetch(`${WP_URL}/wp-json/bpu/v1/member/profile`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${session.value}`,
        },
        body: JSON.stringify(body),
    });

    const data = await response.json();

    if (!response.ok) {
        return NextResponse.json(
            { error: data?.message || 'Failed to update profile' },
            { status: response.status }
        );
    }

    return NextResponse.json(data);
}
