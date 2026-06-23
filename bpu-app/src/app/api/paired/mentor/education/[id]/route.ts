import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

async function getJwt(): Promise<string | null> {
    const store = await cookies();
    return store.get('bpu_session')?.value || null;
}

export async function PUT(
    request: NextRequest,
    { params }: { params: Promise<{ id: string }> }
) {
    const jwt = await getJwt();
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });

    const { id } = await params;

    let body: Record<string, unknown>;
    try {
        body = await request.json();
    } catch {
        return NextResponse.json({ error: 'Invalid request body.' }, { status: 400 });
    }

    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/paired/mentor/education/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${jwt}`,
            },
            body: JSON.stringify(body),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            return NextResponse.json({ error: data.message || 'Failed to update education.' }, { status: res.status });
        }
        return NextResponse.json(data);
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}

export async function DELETE(
    _request: NextRequest,
    { params }: { params: Promise<{ id: string }> }
) {
    const jwt = await getJwt();
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });

    const { id } = await params;

    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/paired/mentor/education/${id}`, {
            method: 'DELETE',
            headers: { 'Authorization': `Bearer ${jwt}` },
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            return NextResponse.json({ error: data.message || 'Failed to delete education.' }, { status: res.status });
        }
        return NextResponse.json(data);
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
