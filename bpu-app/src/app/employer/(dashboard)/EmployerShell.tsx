'use client';

import { useState } from 'react';

interface Props {
    currentPath: string;
    companyName: string;
    userEmail: string;
    children: React.ReactNode;
}

const ICONS: Record<string, React.ReactNode> = {
    apps: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>,
    referral: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>,
    profile: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>,
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

export default function EmployerShell({ currentPath, companyName, userEmail, children }: Props) {
    const [open, setOpen] = useState(false);

    function isActive(href: string) {
        if (href === '/employer/jobs') return currentPath === '/employer/jobs';
        return currentPath.startsWith(href);
    }

    const close = () => setOpen(false);

    return (
        <div className="dash">
            <div className={`dash-overlay ${open ? 'open' : ''}`} onClick={close} />

            <aside className={`dash-sidebar ${open ? 'open' : ''}`}>
                <button className="dash-sidebar-close" onClick={close} aria-label="Close menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>

                <a href="/employer" className="dash-sidebar-brand">
                    <img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="BPU" />
                    <span className="portal-label">Employer Portal</span>
                </a>

                <nav className="dash-nav">
                    <div className="dash-nav-section">
                        <div className="dash-nav-label">Dashboard</div>
                        <NavLink href="/employer/jobs" icon="apps" label="My Jobs" active={isActive('/employer/jobs')} onClick={close} />
                        <NavLink href="/employer/jobs/new" icon="referral" label="Post New Job" active={isActive('/employer/jobs/new')} onClick={close} />
                        <NavLink href="/employer/profile" icon="profile" label="Company Profile" active={isActive('/employer/profile')} onClick={close} />
                    </div>
                </nav>

                <div className="dash-sidebar-footer">
                    <div style={{ flex: 1, minWidth: 0 }}>
                        <div className="dash-sidebar-footer-name">{companyName}</div>
                        <div className="dash-sidebar-footer-email">{userEmail}</div>
                    </div>
                    <a href="/api/auth/logout" className="btn btn-ghost btn-sm" style={{ padding: '4px', flexShrink: 0 }} aria-label="Sign out">
                        {ICONS.signout}
                    </a>
                </div>
            </aside>

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
                    <span className="text-sm text-text-2 hidden sm:inline">{companyName}</span>
                </header>
                <main className="dash-main">
                    {children}
                </main>
            </div>
        </div>
    );
}
