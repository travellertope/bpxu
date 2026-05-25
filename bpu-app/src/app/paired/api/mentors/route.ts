import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET(request: NextRequest) {
    const searchParams = request.nextUrl.searchParams;
    const page = searchParams.get('page') || '1';
    const per_page = searchParams.get('per_page') || '12';
    const industry = searchParams.get('industry') || '';
    const search = searchParams.get('search') || '';

    const url = new URL(`${WP_BACKEND_URL}/wp-json/bpu/v1/mentors`);
    url.searchParams.append('page', page);
    url.searchParams.append('per_page', per_page);
    if (industry) url.searchParams.append('industry', industry);
    if (search) url.searchParams.append('search', search);

    try {
        const response = await fetch(url.toString(), {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Cache-Control': 'no-store'
            }
        });

        if (!response.ok) {
            return NextResponse.json({ error: 'Failed to fetch mentors from WP API' }, { status: response.status });
        }

        const data = await response.json();
        return NextResponse.json(data);
    } catch (error) {
        console.error('API /mentors proxy error:', error);
        return NextResponse.json({ error: 'Internal Server Error' }, { status: 500 });
    }
}
