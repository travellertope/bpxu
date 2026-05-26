import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

async function getJwt(): Promise<string | null> {
    const store = await cookies();
    return store.get('bpu_session')?.value || null;
}

export async function GET(request: NextRequest) {
    const jwt = await getJwt();
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });

    const sp = request.nextUrl.searchParams;
    const url = new URL(`${WP_BACKEND_URL}/wp-json/bpu/v1/bookings`);
    url.searchParams.set('per_page', sp.get('per_page') || '50');
    url.searchParams.set('page', sp.get('page') || '1');

    try {
        const res = await fetch(url.toString(), {
            headers: { 'Authorization': `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
        });
        const data = await res.json();
        return NextResponse.json(data, { status: res.status });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}

export async function POST(request: NextRequest) {
    const jwt = await getJwt();
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });

    const body = await request.json();
    try {
        const res = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/bookings`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${jwt}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        return NextResponse.json(data, { status: res.status });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
