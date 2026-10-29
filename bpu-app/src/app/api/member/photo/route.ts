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
        const file = formData.get('photo') as File | null;

        if (!file) {
            return NextResponse.json({ error: 'No photo file received.' }, { status: 400 });
        }

        if (file.size > 5 * 1024 * 1024) {
            return NextResponse.json({ error: 'Photo must be under 5 MB.' }, { status: 400 });
        }

        const wpForm = new FormData();
        wpForm.append('photo', file, file.name);

        const res = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/member/photo`, {
            method: 'POST',
            headers: { Authorization: `Bearer ${jwt}` },
            body: wpForm,
        });

        const data = await res.json();

        if (!res.ok) {
            return NextResponse.json({ error: data.message || 'Upload failed.' }, { status: res.status });
        }

        return NextResponse.json(data, { status: 200 });
    } catch (err) {
        console.error('Photo upload proxy error:', err);
        return NextResponse.json({ error: 'Internal server error.' }, { status: 500 });
    }
}
