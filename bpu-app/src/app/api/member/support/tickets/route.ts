import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET() {
    const jwt = (await cookies()).get('bpu_session')?.value;
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });

    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/member/support/tickets`, {
            headers: { Authorization: `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
        });
        if (!res.ok) return NextResponse.json({ tickets: [] });
        const data = await res.json();
        return NextResponse.json(data);
    } catch {
        return NextResponse.json({ tickets: [] });
    }
}

export async function POST(request: NextRequest) {
    const jwt = (await cookies()).get('bpu_session')?.value;
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });

    try {
        const formData = await request.formData();
        const res = await fetch(`${WP}/wp-json/bpu/v1/member/support/tickets`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${jwt}` },
            body: formData,
        });
        const data = await res.json();
        if (!res.ok) return NextResponse.json({ error: data.message || 'Failed to create ticket' }, { status: res.status });
        return NextResponse.json(data, { status: 201 });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
