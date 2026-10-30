import { getBPUSession } from '@/lib/auth';
import { cookies } from 'next/headers';
import { redirect } from 'next/navigation';
import FeedClient from './FeedClient';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export const metadata = { title: 'Network Feed | BPU' };

export default async function FeedPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/feed');
    }

    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value ?? '';

    // Fetch initial connections-filter posts server-side
    let initialPosts: unknown[] = [];
    try {
        const res = await fetch(`${WP_URL}/wp-json/bpu/v1/feed?filter=connections&per_page=10`, {
            headers: { 'Content-Type': 'application/json', 'Authorization': `Bearer ${jwt}` },
            cache: 'no-store',
        });
        if (res.ok) {
            const data = await res.json();
            initialPosts = data.posts ?? [];
        }
    } catch { /* render empty, client will fetch */ }

    return (
        <FeedClient
            viewer={session.user}
            initialPosts={initialPosts as Parameters<typeof FeedClient>[0]['initialPosts']}
        />
    );
}
