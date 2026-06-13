import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import { cookies } from 'next/headers';
import MessagingInbox from './MessagingInbox';

const WP = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

export default async function MessagesPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/messages');
    }

    const cookieStore = await cookies();
    const jwt = cookieStore.get('bpu_session')?.value;

    let conversations: Array<Record<string, unknown>> = [];

    if (jwt) {
        try {
            const res = await fetch(`${WP}/wp-json/bpu/v1/paired/messages`, {
                headers: {
                    'Authorization': `Bearer ${jwt}`,
                    'Cache-Control': 'no-store',
                },
            });
            if (res.ok) {
                const data = await res.json();
                conversations = data.conversations || [];
            }
        } catch {
            /* fail silently */
        }
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Messages</h1>
            <p className="text-text-2 mb-8">Stay connected with your mentors and mentees.</p>

            <MessagingInbox
                initialConversations={conversations as never[]}
                currentUserId={session.user.id as number}
            />
        </div>
    );
}
