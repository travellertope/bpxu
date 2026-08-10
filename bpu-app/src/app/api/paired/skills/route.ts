import { NextResponse } from 'next/server';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET() {
    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/paired/skills`, {
            cache: 'no-store',
            headers: { 'Cache-Control': 'no-store' },
        });
        const data = await res.json();
        return NextResponse.json(data, {
            status: res.status,
            headers: { 'Cache-Control': 'no-store, must-revalidate' },
        });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
