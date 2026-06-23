import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

const PROXY_TIMEOUT_MS = 10_000;

export async function POST(_request: NextRequest) {
    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    if (!jwt) {
        return NextResponse.json({ error: 'Unauthorized.' }, { status: 401 });
    }

    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), PROXY_TIMEOUT_MS);

    try {
        const response = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/member/request-cv-review`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${jwt}`,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({}),
            signal: controller.signal,
        });

        clearTimeout(timeout);

        const data = await response.json();
        return NextResponse.json(data, { status: response.status });
    } catch (error) {
        clearTimeout(timeout);
        if (error instanceof Error && error.name === 'AbortError') {
            return NextResponse.json(
                { error: 'The request timed out. Please try again.' },
                { status: 504 }
            );
        }
        console.error('CV review request proxy error:', error);
        return NextResponse.json({ error: 'Internal server error.' }, { status: 500 });
    }
}
