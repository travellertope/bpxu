import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET() {
    const jwt = (await cookies()).get('bpu_session')?.value;
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/member/ambassadorship?type=ambassadorship`, {
            headers: { Authorization: `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
        });
        const data = await res.json();
        return NextResponse.json(data, { status: res.ok ? 200 : res.status });
    } catch {
        return NextResponse.json({ requests: [] });
    }
}

export async function POST(request: NextRequest) {
    const jwt = (await cookies()).get('bpu_session')?.value;
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    try {
        const body = await request.json();
        const res = await fetch(`${WP}/wp-json/bpu/v1/member/ambassadorship`, {
            method: 'POST',
            headers: {
                Authorization: `Bearer ${jwt}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (!res.ok) return NextResponse.json({ error: data.message || 'Failed to submit' }, { status: res.status });
        return NextResponse.json(data, { status: 201 });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
