import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

async function proxyAction(request: NextRequest, id: string, action: string) {
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    if (!jwt) {
        return NextResponse.json({ error: 'Unauthorized. Please log in.' }, { status: 401 });
    }

    let body: Record<string, unknown> = {};
    try {
        body = await request.json();
    } catch { /* empty body is fine for bookmark */ }

    try {
        const res = await fetch(`${WP_URL}/wp-json/bpu/v1/feed/${id}/${action}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${jwt}` },
            body: JSON.stringify(body),
        });
        const data = await res.json();
        if (!res.ok) return NextResponse.json({ error: data.message || 'Action failed.' }, { status: res.status });
        return NextResponse.json(data, { status: 200 });
    } catch (err) {
        console.error(`Feed ${action} proxy error:`, err);
        return NextResponse.json({ error: 'Internal server error.' }, { status: 500 });
    }
}

export async function POST(request: NextRequest, { params }: { params: Promise<{ id: string }> }) {
    const { id } = await params;
    const { searchParams } = new URL(request.url);
    const action = searchParams.get('action') || 'react';

    if (!['react', 'comment', 'bookmark', 'vote'].includes(action)) {
        return NextResponse.json({ error: 'Invalid action.' }, { status: 400 });
    }

    return proxyAction(request, id, action);
}
