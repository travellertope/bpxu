import { NextResponse } from 'next/server';
import { getBPUSession } from '@/lib/auth';

export async function GET() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        return NextResponse.json({ authenticated: false });
    }
    const { id, display_name, photo_url, email, is_pro } = session.user;
    return NextResponse.json({ authenticated: true, user: { id, display_name, photo_url, email, is_pro } });
}
