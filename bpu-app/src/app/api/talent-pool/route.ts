import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET(request: NextRequest) {
    const jwt = (await cookies()).get('bpu_session')?.value;
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    try {
        const { searchParams } = new URL(request.url);
        const params = new URLSearchParams();
        ['programme', 'city', 'search', 'page', 'per_page'].forEach(k => {
            const v = searchParams.get(k);
            if (v) params.set(k, v);
        });
        const res = await fetch(`${WP}/wp-json/bpu/v1/talent-pool?${params}`, {
            headers: { Authorization: `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
        });
        const data = await res.json().catch(() => ({}));
        return NextResponse.json(data, { status: res.ok ? 200 : res.status });
    } catch {
        return NextResponse.json({ members: [], total: 0 });
    }
}
