import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(request: NextRequest) {
    const cookieStore = await cookies();
    const wpCookie = cookieStore.getAll().find(c => c.name.startsWith('wordpress_logged_in_'));

    if (!wpCookie) {
        return NextResponse.json({ error: 'Unauthorized. No session cookie found.' }, { status: 401 });
    }

    try {
        const formData = await request.formData();
        const file = formData.get('cv_file') as File;

        if (!file) {
            return NextResponse.json({ error: 'No CV file uploaded.' }, { status: 400 });
        }

        // Re-construct Multipart Form Data for WordPress fetch call
        const wpFormData = new FormData();
        wpFormData.append('cv_file', file, file.name);

        const wpCookieHeader = `${wpCookie.name}=${wpCookie.value}`;

        const response = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/member/cv-upload`, {
            method: 'POST',
            headers: {
                'Cookie': wpCookieHeader
                // Fetch automatically configures the boundary header for FormData
            },
            body: wpFormData
        });

        const data = await response.json();

        if (!response.ok) {
            return NextResponse.json({ error: data.message || 'Failed to parse resume.' }, { status: response.status });
        }

        return NextResponse.json(data, { status: 200 });

    } catch (error) {
        console.error('Next.js CV Proxy Upload Error:', error);
        return NextResponse.json({ error: 'Internal server error occurred.' }, { status: 500 });
    }
}
