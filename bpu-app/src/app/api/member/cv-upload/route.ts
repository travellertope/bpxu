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
        const file = formData.get('cv_file') as File;

        if (!file) {
            return NextResponse.json({ error: 'No CV file uploaded.' }, { status: 400 });
        }

        const wpFormData = new FormData();
        wpFormData.append('cv_file', file, file.name);

        const response = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/member/cv-upload`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${jwt}`,
            },
            body: wpFormData,
        });

        const data = await response.json();

        if (!response.ok) {
            return NextResponse.json({ error: data.message || 'Failed to parse CV.' }, { status: response.status });
        }

        return NextResponse.json(data, { status: 200 });

    } catch (error) {
        console.error('CV upload proxy error:', error);
        return NextResponse.json({ error: 'Internal server error.' }, { status: 500 });
    }
}
