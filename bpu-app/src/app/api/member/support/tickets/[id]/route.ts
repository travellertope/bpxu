import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET(
    _request: NextRequest,
    { params }: { params: Promise<{ id: string }> }
) {
    const jwt = (await cookies()).get('bpu_session')?.value;
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });

    const { id } = await params;
    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/member/support/tickets/${id}`, {
            headers: { Authorization: `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
        });
        if (!res.ok) return NextResponse.json({ error: 'Ticket not found' }, { status: 404 });
        return NextResponse.json(await res.json());
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
