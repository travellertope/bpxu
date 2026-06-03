import { NextResponse } from 'next/server';
import { BPUApi } from '@/lib/api';
import { cookies } from 'next/headers';

export async function GET(request: Request) {
    const { searchParams } = new URL(request.url);
    const jobId = searchParams.get('jobId');
    const redirectUrl = searchParams.get('url');

    if (!jobId || !redirectUrl) {
        return NextResponse.redirect(new URL('https://blackprofessionals.uk/job-board'));
    }

    const cookieStore = await cookies();
    const wpCookie = cookieStore.getAll().find(c => c.name.startsWith('wordpress_logged_in_'));
    const wpCookieHeader = wpCookie ? `${wpCookie.name}=${wpCookie.value}` : '';

    // Await tracking before redirecting — fire-and-forget is unreliable in serverless
    // environments because the runtime is reclaimed as soon as the response is sent.
    await BPUApi.trackJobClick(parseInt(jobId), null, wpCookieHeader).catch(err => {
        console.error('Click Tracking Error:', err);
    });

    return NextResponse.redirect(redirectUrl);
}
