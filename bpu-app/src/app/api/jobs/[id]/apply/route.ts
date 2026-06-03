import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function POST(
    request: NextRequest,
    { params }: { params: Promise<{ id: string }> }
) {
    const { id } = await params;
    const cookieStore = await cookies();
    const session = cookieStore.get('bpu_session');

    if (!session?.value) {
        return NextResponse.json({ error: 'Authentication required.' }, { status: 401 });
    }

    try {
        const formData = await request.formData();

        const res = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/jobs/${id}/apply`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${session.value}`,
            },
            body: formData,
        });

        const data = await res.json().catch(() => ({}));

        if (!res.ok) {
            return NextResponse.json(
                { error: data.message || data.error || 'Application submission failed.' },
                { status: res.status }
            );
        }

        return NextResponse.json({ success: true, ...data });
    } catch {
        return NextResponse.json({ error: 'Something went wrong. Please try again.' }, { status: 500 });
    }
}
