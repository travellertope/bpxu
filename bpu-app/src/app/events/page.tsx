import { getBPUSession } from '@/lib/auth';
import { BPUApi } from '@/lib/api';
import EventBoard from './EventBoard';

export const metadata = {
    title: 'Events | BPU Portal',
    description: 'Browse upcoming BPU networking events, workshops, and community meetups.',
};

export default async function EventsPage() {
    const [session, events] = await Promise.all([
        getBPUSession(),
        BPUApi.getEvents(),
    ]);

    return (
        <div className="min-h-screen flex flex-col">
            {/* Topbar */}
            <header className="topbar">
                <div className="topbar-inner">
                    <a href="/" className="topbar-brand">
                        <img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="Black Professionals United" />
                    </a>
                    <div className="flex items-center gap-3">
                        {session.authenticated
                            ? <a href="/" className="btn btn-ghost btn-sm">← Dashboard</a>
                            : <a href="/login" className="btn btn-amber btn-sm">Sign in</a>
                        }
                    </div>
                </div>
            </header>

            <main className="flex-1">
                {/* Hero */}
                <section
                    className="py-20 text-center"
                    style={{
                        position: 'relative',
                        overflow: 'hidden',
                        borderBottom: '1px solid var(--border)',
                    }}
                >
                    {/* Background image */}
                    <div style={{
                        position: 'absolute', inset: 0,
                        backgroundImage: 'url(https://images.unsplash.com/photo-1540575467063-178a50c2df87?w=1600&q=80)',
                        backgroundSize: 'cover',
                        backgroundPosition: 'center 40%',
                    }} />
                    {/* Dark overlay */}
                    <div style={{ position: 'absolute', inset: 0, background: 'rgba(10,10,20,0.60)' }} />

                    <div className="wrap" style={{ position: 'relative', zIndex: 1 }}>
                        <h1 className="text-4xl font-extrabold tracking-tight mb-4" style={{ color: '#fff' }}>
                            Upcoming Events
                        </h1>
                        <p className="text-lg max-w-2xl mx-auto" style={{ color: 'rgba(255,255,255,0.82)' }}>
                            Networking events, workshops, panel discussions, and community meetups — all curated for Black professionals in the UK.
                        </p>
                        <p className="mt-4 text-sm" style={{ color: 'rgba(255,255,255,0.55)' }}>
                            {events.length > 0 ? `${events.length} upcoming event${events.length === 1 ? '' : 's'}` : 'Check back soon for upcoming events'}
                        </p>
                    </div>
                </section>

                {/* Board */}
                <section className="wrap py-10">
                    <EventBoard events={events} />
                </section>
            </main>

            <footer className="py-6 text-center text-xs text-text-3 border-t border-border">
                © {new Date().getFullYear()} Black Professionals United · <a href="/" className="hover:underline">Back to Portal</a>
            </footer>
        </div>
    );
}
