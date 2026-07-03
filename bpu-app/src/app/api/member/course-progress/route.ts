import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(request: NextRequest) {
    const store = await cookies();
    const jwt = store.get('bpu_session')?.value;
    if (!jwt) return NextResponse.json({ success: false }, { status: 401 });

    try {
        const body = await request.json();
        const res = await fetch(`${WP}/wp-json/bpu/v1/courses/progress`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${jwt}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(body),
        });
        return NextResponse.json({ success: res.ok }, { status: res.ok ? 200 : res.status });
    } catch {
        return NextResponse.json({ success: false }, { status: 500 });
    }
}
