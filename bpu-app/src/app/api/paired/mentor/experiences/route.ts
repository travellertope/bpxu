import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

async function getJwt(): Promise<string | null> {
    const store = await cookies();
    return store.get('bpu_session')?.value || null;
}

export async function GET() {
    const jwt = await getJwt();
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });

    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/paired/mentor/experiences`, {
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

    let body: Record<string, unknown>;
    try {
        body = await request.json();
    } catch {
        return NextResponse.json({ error: 'Invalid request body.' }, { status: 400 });
    }

    if (!body.title || !body.company) {
        return NextResponse.json({ error: 'Job title and company are required.' }, { status: 400 });
    }

    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/paired/mentor/experiences`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${jwt}`,
            },
            body: JSON.stringify(body),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            return NextResponse.json({ error: data.message || 'Failed to create experience.' }, { status: res.status });
        }
        return NextResponse.json(data, { status: 201 });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
