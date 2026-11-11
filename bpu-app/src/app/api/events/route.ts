import { NextRequest, NextResponse } from 'next/server';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET(request: NextRequest) {
    const { searchParams } = new URL(request.url);
    const type     = searchParams.get('type') === 'past' ? 'past' : 'upcoming';
    const page     = searchParams.get('page') || '1';
    const perPage  = searchParams.get('per_page') || '50';

    try {
        const res = await fetch(
            `${WP}/wp-json/bpu/v1/events?type=${type}&page=${page}&per_page=${perPage}`,
            { cache: 'no-store' }
        );
        const data = await res.json().catch(() => ({}));
        return NextResponse.json(data, { status: res.ok ? 200 : res.status });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
