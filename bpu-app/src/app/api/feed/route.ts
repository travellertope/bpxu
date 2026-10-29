import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET(request: NextRequest) {
    const { searchParams } = new URL(request.url);
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    const params = new URLSearchParams();
    ['filter', 'category', 'sort', 'search', 'hashtag', 'page', 'per_page'].forEach(k => {
        const v = searchParams.get(k);
        if (v) params.set(k, v);
    });

    const headers: Record<string, string> = { 'Content-Type': 'application/json' };
    if (jwt) headers['Authorization'] = `Bearer ${jwt}`;

    try {
        const res = await fetch(`${WP_URL}/wp-json/bpu/v1/feed?${params}`, { headers });
        const data = await res.json();
        return NextResponse.json(data, { status: res.status });
    } catch (err) {
        console.error('Feed GET proxy error:', err);
        return NextResponse.json({ error: 'Internal server error.' }, { status: 500 });
    }
}

export async function POST(request: NextRequest) {
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    if (!jwt) {
        return NextResponse.json({ error: 'Unauthorized. Please log in.' }, { status: 401 });
    }

    let body: Record<string, unknown>;
    try {
        body = await request.json();
    } catch {
        return NextResponse.json({ error: 'Invalid JSON body' }, { status: 400 });
    }

    try {
        const res = await fetch(`${WP_URL}/wp-json/bpu/v1/feed`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${jwt}` },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (!res.ok) return NextResponse.json({ error: data.message || 'Could not create post.' }, { status: res.status });
        return NextResponse.json(data, { status: 201 });
    } catch (err) {
        console.error('Feed POST proxy error:', err);
        return NextResponse.json({ error: 'Internal server error.' }, { status: 500 });
    }
}
