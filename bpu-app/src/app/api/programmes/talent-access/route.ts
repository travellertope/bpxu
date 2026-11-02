import { NextRequest, NextResponse } from 'next/server';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(request: NextRequest) {
    try {
        const formData = await request.formData();
        const res = await fetch(`${WP}/wp-json/bpu/v1/programmes/talent-access`, {
            method: 'POST',
            body: formData,
        });
        const data = await res.json().catch(() => ({}));
        return NextResponse.json(data, { status: res.ok ? 200 : res.status });
    } catch {
        return NextResponse.json({ status: 'error', message: 'Internal server error' }, { status: 500 });
    }
}
