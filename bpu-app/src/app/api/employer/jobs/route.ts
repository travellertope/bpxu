import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

async function getSession() {
    const cookieStore = await cookies();
    return cookieStore.get('bpu_session')?.value ?? null;
}

export async function GET() {
    const session = await getSession();

    if (!session) {
        return NextResponse.json({ error: 'Authentication required.' }, { status: 401 });
    }

    try {
        const res = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/employer/jobs`, {
            headers: {
                'Authorization': `Bearer ${session}`,
                'Cache-Control': 'no-store',
            },
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            return NextResponse.json(
                { error: data.message || data.error || 'Failed to fetch jobs.' },
                { status: res.status }
            );
        }

        return NextResponse.json(data);
    } catch {
        return NextResponse.json({ error: 'Something went wrong.' }, { status: 500 });
    }
}

export async function POST(request: NextRequest) {
    const session = await getSession();

    if (!session) {
        return NextResponse.json({ error: 'Authentication required.' }, { status: 401 });
    }

    let body: Record<string, unknown>;
    try {
        body = await request.json();
    } catch {
        return NextResponse.json({ error: 'Invalid request body.' }, { status: 400 });
    }

    try {
        const res = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/jobs`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${session}`,
            },
            body: JSON.stringify(body),
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            return NextResponse.json(
                { error: data.message || data.error || 'Failed to create job.' },
                { status: res.status }
            );
        }

        return NextResponse.json({ success: true, ...data });
    } catch {
        return NextResponse.json({ error: 'Something went wrong.' }, { status: 500 });
    }
}
