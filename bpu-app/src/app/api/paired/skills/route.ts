import { NextResponse } from 'next/server';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function GET() {
    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/paired/skills`, {
            headers: { 'Cache-Control': 'no-store' },
        });
        const data = await res.json();
        return NextResponse.json(data, { status: res.status });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
