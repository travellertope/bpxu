import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(req: NextRequest) {
    const store = await cookies();
    const jwt = store.get('bpu_session')?.value;
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });

    try {
        const contentType = req.headers.get('content-type') || 'multipart/form-data';
        const body = await req.arrayBuffer();
        const res = await fetch(`${WP}/wp-json/bpu/v1/paired/mentor/kyc/submit`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${jwt}`,
                'Content-Type': contentType,
                'Cache-Control': 'no-store',
            },
            body: body,
        });
        const data = await res.json();
        return NextResponse.json(data, { status: res.status });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
