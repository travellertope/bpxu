import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET(request: NextRequest) {
    const { searchParams } = new URL(request.url);
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    const params = new URLSearchParams();
    ['search', 'sector', 'city', 'featured', 'page', 'per_page'].forEach(k => {
        const v = searchParams.get(k);
        if (v) params.set(k, v);
    });

    const headers: Record<string, string> = { 'Content-Type': 'application/json' };
    if (jwt) headers['Authorization'] = `Bearer ${jwt}`;

    try {
        const res = await fetch(`${WP_URL}/wp-json/bpu/v1/members?${params}`, { headers, cache: 'no-store' });
        const data = await res.json();
        const resp = NextResponse.json(data, { status: res.status });
        const total = res.headers.get('X-WP-Total');
        const totalPages = res.headers.get('X-WP-TotalPages');
        if (total) resp.headers.set('X-WP-Total', total);
        if (totalPages) resp.headers.set('X-WP-TotalPages', totalPages);
        return resp;
    } catch (err) {
        console.error('Members GET proxy error:', err);
        return NextResponse.json({ error: 'Internal server error.' }, { status: 500 });
    }
}
