import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET(
    request: NextRequest,
    { params }: { params: Promise<{ userId: string }> }
) {
    const store = await cookies();
    const jwt = store.get('bpu_session')?.value;
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });

    const { userId } = await params;
    const qs = request.nextUrl.searchParams.toString();

    try {
        const res = await fetch(
            `${WP}/wp-json/bpu/v1/paired/messages/${userId}${qs ? `?${qs}` : ''}`,
            { headers: { 'Authorization': `Bearer ${jwt}`, 'Cache-Control': 'no-store' } }
        );
        const data = await res.json();
        return NextResponse.json(data, { status: res.status });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
