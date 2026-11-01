import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(
    request: NextRequest,
    { params }: { params: Promise<{ id: string }> }
) {
    const jwt = (await cookies()).get('bpu_session')?.value;
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });

    const { id } = await params;
    try {
        const formData = await request.formData();
        const res = await fetch(`${WP}/wp-json/bpu/v1/member/support/tickets/${id}/reply`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${jwt}` },
            body: formData,
        });
        const data = await res.json();
        if (!res.ok) return NextResponse.json({ error: data.message || 'Failed to post reply' }, { status: res.status });
        return NextResponse.json(data, { status: 201 });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
