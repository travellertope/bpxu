import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(
    request: NextRequest,
    { params }: { params: Promise<{ id: string }> }
) {
    const store = await cookies();
    const jwt = store.get('bpu_session')?.value;
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });

    const { id } = await params;

    let body: Record<string, unknown>;
    try {
        body = await request.json();
    } catch {
        return NextResponse.json({ error: 'Invalid request body.' }, { status: 400 });
    }

    const validStatuses = ['confirmed', 'cancelled', 'completed'];
    if (!body.status || !validStatuses.includes(body.status as string)) {
        return NextResponse.json(
            { error: 'Invalid status. Use: confirmed, cancelled, or completed.' },
            { status: 400 }
        );
    }

    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/bookings/${id}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${jwt}`,
            },
            body: JSON.stringify({ status: body.status }),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
            return NextResponse.json(
                { error: data.message || 'Failed to update booking status.' },
                { status: res.status }
            );
        }
        return NextResponse.json(data);
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
