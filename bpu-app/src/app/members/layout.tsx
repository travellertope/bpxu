import { getBPUSession } from '@/lib/auth';
import { Suspense } from 'react';
import MembersSearchBar from './MembersSearchBar';

export default async function MembersLayout({ children }: { children: React.ReactNode }) {
    const session = await getBPUSession();

    return (
        <div className="min-h-screen flex flex-col">
            <header className="topbar">
                <div className="topbar-inner">
                    <a href="/" className="topbar-brand">
                        {/* eslint-disable-next-line @next/next/no-img-element */}
                        <img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="Black Professionals UK" />
                    </a>
                    <div className="flex items-center gap-3 flex-1 justify-end">
                        <Suspense>
                            <MembersSearchBar />
                        </Suspense>
                        <nav className="flex items-center gap-2">
                            <a href="/members" className="btn btn-ghost btn-sm hidden sm:inline-flex">Directory</a>
                            {session.authenticated ? (
                                <>
                                    <a href="/dashboard" className="btn btn-ghost btn-sm hidden md:inline-flex">Dashboard</a>
                                    <a href="/feed" className="btn btn-ghost btn-sm hidden sm:inline-flex">Network Feed</a>
                                </>
                            ) : (
                                <>
                                    <a href="/register" className="btn btn-ghost btn-sm hidden sm:inline-flex">Join</a>
                                    <a href="/login" className="btn btn-amber btn-sm">Sign in</a>
                                </>
                            )}
                        </nav>
                    </div>
                </div>
            </header>

            <main className="flex-1">
                {children}
            </main>

            <footer className="py-6 text-center text-xs text-text-3 border-t border-border">
                © {new Date().getFullYear()} Black Professionals United ·{' '}
                <a href="/members" className="hover:underline">Member Directory</a> ·{' '}
                <a href="/" className="hover:underline">Portal Home</a>
            </footer>
        </div>
    );
}
