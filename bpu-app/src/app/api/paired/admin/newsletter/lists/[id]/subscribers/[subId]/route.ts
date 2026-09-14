import { NextRequest, NextResponse } from 'next/server';
import { cookies } from 'next/headers';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export async function DELETE(
    _request: NextRequest,
    { params }: { params: Promise<{ id: string; subId: string }> }
) {
    const store = await cookies();
    const jwt = store.get('bpu_session')?.value;
    if (!jwt) return NextResponse.json({ error: 'Unauthorized' }, { status: 401 });

    const { id, subId } = await params;

    try {
        const res = await fetch(`${WP}/wp-json/bpu/v1/paired/admin/newsletter/lists/${id}/subscribers/${subId}`, {
            method: 'DELETE',
            cache: 'no-store',
            headers: { 'Authorization': `Bearer ${jwt}`, 'Cache-Control': 'no-store' },
        });
        const data = await res.json().catch(() => ({}));
        return NextResponse.json(data, {
            status: res.status,
            headers: { 'Cache-Control': 'no-store, must-revalidate' },
        });
    } catch {
        return NextResponse.json({ error: 'Internal server error' }, { status: 500 });
    }
}
