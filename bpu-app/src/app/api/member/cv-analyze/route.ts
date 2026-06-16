import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

// Must exceed PHP's Gemini timeout (90s) to avoid cutting off a valid response.
const PROXY_TIMEOUT_MS = 100_000;

export async function POST(request: NextRequest) {
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    if (!jwt) {
        return NextResponse.json({ error: 'Unauthorized. Please log in.' }, { status: 401 });
    }

    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), PROXY_TIMEOUT_MS);

    try {
        const formData = await request.formData();

        const response = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/member/cv-analyze`, {
            method: 'POST',
            headers: { 'Authorization': `Bearer ${jwt}` },
            body: formData,
            signal: controller.signal,
        });

        clearTimeout(timeout);

        const data = await response.json();

        if (!response.ok) {
            return NextResponse.json({ error: data.message || 'Analysis failed.' }, { status: response.status });
        }

        return NextResponse.json(data, { status: 200 });
    } catch (error) {
        clearTimeout(timeout);
        if (error instanceof Error && error.name === 'AbortError') {
            return NextResponse.json(
                { error: 'The AI took too long to respond. Please try again in a moment.' },
                { status: 504 }
            );
        }
        console.error('CV analyze proxy error:', error);
        return NextResponse.json({ error: 'Internal server error.' }, { status: 500 });
    }
}
