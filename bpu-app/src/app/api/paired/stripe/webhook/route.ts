import { NextRequest, NextResponse } from 'next/server';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(req: NextRequest) {
    try {
        const rawBody = await req.text();
        const res = await fetch(`${WP}/wp-json/bpu/v1/paired/stripe/webhook`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Cache-Control': 'no-store',
                ...(req.headers.get('stripe-signature')
                    ? { 'stripe-signature': req.headers.get('stripe-signature')! }
                    : {}),
            },
            body: rawBody,
        });
        const data = await res.json();
        return NextResponse.json(data, { status: res.status });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
