import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(
    request: NextRequest,
    { params }: { params: Promise<{ id: string }> }
) {
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    if (!jwt) {
        return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    }

    const { id } = await params;

    let body: Record<string, unknown>;
    try {
        body = await request.json();
    } catch {
        return NextResponse.json({ error: 'Invalid request body.' }, { status: 400 });
    }

    const action = body.action;
    if (action !== 'approve' && action !== 'reject') {
        return NextResponse.json({ error: 'Invalid action. Use "approve" or "reject".' }, { status: 400 });
    }

    const endpoint = action === 'approve' ? 'mentor-approve' : 'mentor-reject';

    try {
        const res = await fetch(
            `${WP_BACKEND_URL}/wp-json/bpu/v1/paired/${endpoint}/${id}`,
            {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Authorization': `Bearer ${jwt}`,
                },
                body: JSON.stringify({ reason: body.reason || '' }),
            }
        );

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            return NextResponse.json(
                { error: data.message || `Failed to ${action} application.` },
                { status: res.status }
            );
        }

        return NextResponse.json(data);
    } catch {
        return NextResponse.json({ error: 'Something went wrong.' }, { status: 500 });
    }
}
