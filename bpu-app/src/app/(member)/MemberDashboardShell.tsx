'use client';

import { useState } from 'react';

interface Props {
    currentPath: string;
    userName: string;
    userEmail: string;
    isPro: boolean;
    isAdmin: boolean;
    children: React.ReactNode;
}

const ICONS: Record<string, React.ReactNode> = {
    dashboard: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>,
    apps: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>,
    book: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>,
    bookings: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>,
    chart: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>,
    profile: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>,
    shield: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>,
    star: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>,
    settings: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>,
    signout: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>,
};

function NavLink({ href, icon, label, active, onClick }: { href: string; icon: string; label: string; active: boolean; onClick?: () => void }) {
    return (
        <a href={href} className={`dash-nav-item ${active ? 'active' : ''}`} onClick={onClick}>
            {ICONS[icon] || null}
            {label}
        </a>
    );
}

export default function MemberDashboardShell({ currentPath, userName, userEmail, isPro, isAdmin, children }: Props) {
    const [open, setOpen] = useState(false);

    function isActive(href: string) {
        if (href === '/dashboard') return currentPath === '/dashboard';
        return currentPath.startsWith(href);
    }

    const close = () => setOpen(false);

    return (
        <div className="dash">
            {/* Overlay */}
            <div className={`dash-overlay ${open ? 'open' : ''}`} onClick={close} />

            {/* Sidebar */}
            <aside className={`dash-sidebar ${open ? 'open' : ''}`}>
                <button className="dash-sidebar-close" onClick={close} aria-label="Close menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>

                <a href="/dashboard" className="dash-sidebar-brand">
                    <img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="BPU" />
                    <span className="portal-label">BPU</span>
                </a>

                <nav className="dash-nav">
                    <div className="dash-nav-section">
                        <div className="dash-nav-label">General</div>
                        <NavLink href="/dashboard" icon="dashboard" label="Dashboard" active={isActive('/dashboard')} onClick={close} />
                        <NavLink href="/jobs" icon="apps" label="Job Board" active={isActive('/jobs')} onClick={close} />
                        <NavLink href="/courses" icon="book" label="Courses" active={isActive('/courses')} onClick={close} />
                        <NavLink href="/events" icon="bookings" label="Events" active={isActive('/events')} onClick={close} />
                    </div>

                    <div className="dash-nav-section">
                        <div className="dash-nav-label">Career Tools</div>
                        <NavLink href="/cv-clinic" icon="apps" label="CV Clinic" active={isActive('/cv-clinic')} onClick={close} />
                        <NavLink href="/job-matches" icon="chart" label="Job Matches" active={isActive('/job-matches')} onClick={close} />
                    </div>

                    <div className="dash-nav-section">
                        <div className="dash-nav-label">Profile</div>
                        <NavLink href="/profile" icon="profile" label="My Profile" active={isActive('/profile')} onClick={close} />
                        <NavLink href="/change-password" icon="shield" label="Change Password" active={isActive('/change-password')} onClick={close} />
                        <NavLink href="/upgrade" icon="star" label="Upgrade to Pro" active={isActive('/upgrade')} onClick={close} />
                    </div>

                    {isAdmin && (
                        <div className="dash-nav-section">
                            <div className="dash-nav-label">Admin</div>
                            <NavLink href="/paired/admin/dashboard" icon="settings" label="Site Admin" active={isActive('/paired/admin/dashboard')} onClick={close} />
                        </div>
                    )}
                </nav>

                <div className="dash-sidebar-footer">
                    <div style={{ flex: 1, minWidth: 0 }}>
                        <div className="dash-sidebar-footer-name">{userName}</div>
                        <div className="dash-sidebar-footer-email">{userEmail}</div>
                    </div>
                    <a href="/api/auth/logout" className="btn btn-ghost btn-sm" style={{ padding: '4px', flexShrink: 0 }} aria-label="Sign out">
                        {ICONS.signout}
                    </a>
                </div>
            </aside>

            {/* Main content */}
            <div className="dash-content">
                <header className="dash-topbar">
                    <button className="dash-topbar-hamburger" onClick={() => setOpen(true)} aria-label="Open menu">
                        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                            <line x1="3" y1="12" x2="21" y2="12"/>
                            <line x1="3" y1="6" x2="21" y2="6"/>
                            <line x1="3" y1="18" x2="21" y2="18"/>
                        </svg>
                    </button>
                    <div className="flex-1" />
                    <span className="text-sm text-text-2 hidden sm:inline">{userName}</span>
                </header>
                <main className="dash-main">
                    {children}
                </main>
            </div>
        </div>
    );
}
