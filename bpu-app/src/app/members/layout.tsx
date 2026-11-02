import { getBPUSession } from '@/lib/auth';
import { Suspense } from 'react';
import SiteHeader from '@/components/SiteHeader';
import MembersSearchBar from './MembersSearchBar';

export default async function MembersLayout({ children }: { children: React.ReactNode }) {
    const session = await getBPUSession();

    return (
        <div className="min-h-screen flex flex-col bg-hp-bg text-hp-dark">
            <SiteHeader />

            <main className="flex-1 pt-20">
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
