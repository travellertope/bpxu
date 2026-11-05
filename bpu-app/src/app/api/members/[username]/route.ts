import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET(
    _req: NextRequest,
    { params }: { params: Promise<{ username: string }> }
) {
    const { username } = await params;
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    const headers: Record<string, string> = {};
    if (jwt) headers['Authorization'] = `Bearer ${jwt}`;

    try {
        const res = await fetch(
            `${WP_URL}/wp-json/bpu/v1/member/${encodeURIComponent(username)}`,
            { headers, cache: 'no-store' }
        );
        const data = await res.json();
        return NextResponse.json(data, { status: res.status });
    } catch {
        return NextResponse.json({ message: 'Member not found.' }, { status: 404 });
    }
}
