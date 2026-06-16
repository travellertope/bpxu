import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET(_request: NextRequest) {
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    if (!jwt) {
        return NextResponse.json({ error: 'Unauthorized.' }, { status: 401 });
    }

    try {
        const response = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/member/cv-clinic-history`, {
            headers: {
                'Authorization': `Bearer ${jwt}`,
                'Cache-Control': 'no-store',
            },
        });

        const data = await response.json();
        if (!response.ok) {
            return NextResponse.json({ analyses: [], prep_sessions: [] }, { status: 200 });
        }
        return NextResponse.json(data, { status: 200 });
    } catch {
        return NextResponse.json({ analyses: [], prep_sessions: [] }, { status: 200 });
    }
}
