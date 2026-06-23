import { NextRequest, NextResponse } from 'next/server';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET(
    request: NextRequest,
    { params }: { params: Promise<{ id: string }> }
) {
    const { id } = await params;
    const qs = request.nextUrl.searchParams.toString();

    try {
        const res = await fetch(
            `${WP}/wp-json/bpu/v1/paired/mentors/${id}/reviews${qs ? `?${qs}` : ''}`,
            { headers: { 'Cache-Control': 'no-store' } }
        );
        const data = await res.json();
        return NextResponse.json(data, { status: res.status });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
