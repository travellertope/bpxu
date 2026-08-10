import { NextRequest, NextResponse } from 'next/server';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET(request: NextRequest) {
    const qs = request.nextUrl.searchParams.toString();
    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/jobs${qs ? `?${qs}` : ''}`, {
            cache: 'no-store',
            headers: { 'Cache-Control': 'no-store' },
        });
        const data = await res.json().catch(() => ({}));
        return NextResponse.json(data, { status: res.status });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
