import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET(request: NextRequest) {
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    if (!jwt) {
        return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });
    }

    const status = request.nextUrl.searchParams.get('status') || 'pending';

    try {
        const res = await fetch(
            `${WP_BACKEND_URL}/wp-json/bpu/v1/paired/mentor-applications?status=${encodeURIComponent(status)}`,
            {
                headers: { 'Authorization': `Bearer ${jwt}` },
                cache: 'no-store',
            }
        );

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            return NextResponse.json(
                { error: data.message || 'Failed to fetch applications.' },
                { status: res.status }
            );
        }

        return NextResponse.json(data);
    } catch {
        return NextResponse.json({ error: 'Something went wrong.' }, { status: 500 });
    }
}
