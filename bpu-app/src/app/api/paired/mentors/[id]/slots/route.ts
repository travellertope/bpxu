import { NextRequest, NextResponse } from 'next/server';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET(
    request: NextRequest,
    { params }: { params: Promise<{ id: string }> }
) {
    const { id } = await params;
    const sp = request.nextUrl.searchParams;
    const date = sp.get('date');
    const duration = sp.get('duration') || '60';

    if (!date) {
        return NextResponse.json({ error: 'Date parameter is required (YYYY-MM-DD).' }, { status: 400 });
    }

    const url = new URL(`${WP}/wp-json/bpu/v1/paired/mentors/${id}/slots`);
    url.searchParams.set('date', date);
    url.searchParams.set('duration', duration);

    try {
        const res = await fetch(url.toString(), {
            headers: { 'Cache-Control': 'no-store' },
        });
        const data = await res.json();
        return NextResponse.json(data, { status: res.status });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
