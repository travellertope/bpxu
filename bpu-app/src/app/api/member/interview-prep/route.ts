import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(request: NextRequest) {
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    if (!jwt) {
        return NextResponse.json({ error: 'Unauthorized. Please log in.' }, { status: 401 });
    }

    try {
        const formData = await request.formData();

        const response = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/member/interview-prep`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${jwt}` },
            body: formData,
        });

        const data = await response.json();

        if (!response.ok) {
            return NextResponse.json({ error: data.message || 'Failed to generate questions.' }, { status: response.status });
        }

        return NextResponse.json(data, { status: 200 });
    } catch (error) {
        console.error('Interview prep proxy error:', error);
        return NextResponse.json({ error: 'Internal server error.' }, { status: 500 });
    }
}
