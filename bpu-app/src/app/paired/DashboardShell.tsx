'use client';

import { useState } from 'react';

interface Props {
    currentPath: string;
    userName: string;
    userEmail: string;
    isMentor: boolean;
    isAdmin: boolean;
    userRoles?: string[];
    children: React.ReactNode;
    notificationBell: React.ReactNode;
}

const ICONS: Record<string, React.ReactNode> = {
    dashboard: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>,
    bookings: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>,
    messages: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>,
    heart: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/></svg>,
    bell: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>,
    referral: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><line x1="20" y1="8" x2="20" y2="14"/><line x1="23" y1="11" x2="17" y2="11"/></svg>,
    profile: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>,
    mentors: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/></svg>,
    sessions: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>,
    mentees: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>,
    settings: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>,
    apps: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>,
    chart: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>,
    dollar: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>,
    tag: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>,
    shield: <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>,
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

export default function DashboardShell({ currentPath, userName, userEmail, isMentor, isAdmin, userRoles = [], children, notificationBell }: Props) {
    const [open, setOpen] = useState(false);

    function isActive(href: string) {
        if (href === '/paired/dashboard') return currentPath === '/paired/dashboard';
        return currentPath.startsWith(href);
    }

    function hasRole(...roles: string[]) {
        return roles.some(r => userRoles.includes(r));
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

                <a href="/paired" className="dash-sidebar-brand">
                    <img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="BPU" />
                    <span className="portal-label">PAIRED</span>
                </a>

                <nav className="dash-nav">
                    <div className="dash-nav-section">
                        <div className="dash-nav-label">General</div>
                        <NavLink href="/paired/dashboard" icon="dashboard" label="Dashboard" active={isActive('/paired/dashboard')} onClick={close} />
                        <NavLink href="/paired/mentors" icon="mentors" label="Browse Mentors" active={isActive('/paired/mentors')} onClick={close} />
                    </div>

                    <div className="dash-nav-section">
                        <div className="dash-nav-label">Mentee</div>
                        <NavLink href="/paired/mentee/bookings" icon="bookings" label="My Bookings" active={isActive('/paired/mentee/bookings')} onClick={close} />
                        <NavLink href="/paired/messages" icon="messages" label="Messages" active={isActive('/paired/messages')} onClick={close} />
                        <NavLink href="/paired/favourites" icon="heart" label="Favourites" active={isActive('/paired/favourites')} onClick={close} />
                        <NavLink href="/paired/notifications" icon="bell" label="Notifications" active={isActive('/paired/notifications')} onClick={close} />
                        <NavLink href="/paired/referral" icon="referral" label="Referrals" active={isActive('/paired/referral')} onClick={close} />
                        <NavLink href="/paired/mentee/profile" icon="profile" label="Personal Profile" active={isActive('/paired/mentee/profile')} onClick={close} />
                    </div>

                    {isMentor && (
                        <div className="dash-nav-section">
                            <div className="dash-nav-label">Mentor</div>
                            <NavLink href="/paired/mentor/sessions" icon="sessions" label="Sessions" active={isActive('/paired/mentor/sessions')} onClick={close} />
                            <NavLink href="/paired/mentor/bookings" icon="bookings" label="Mentor Bookings" active={isActive('/paired/mentor/bookings')} onClick={close} />
                            <NavLink href="/paired/mentor/mentees" icon="mentees" label="Mentees" active={isActive('/paired/mentor/mentees')} onClick={close} />
                            <NavLink href="/paired/mentor/settings" icon="settings" label="Mentor Profile" active={isActive('/paired/mentor/settings')} onClick={close} />
                        </div>
                    )}

                    {isAdmin && (
                        <>
                            {/* ── Overview ── */}
                            <div className="dash-nav-section">
                                <div className="dash-nav-label">Overview</div>
                                <NavLink href="/admin/dashboard" icon="dashboard" label="Dashboard" active={isActive('/admin/dashboard')} onClick={close} />
                            </div>

                            {/* ── Job Manager ── */}
                            {hasRole('administrator', 'bpu_editor', 'bpu_moderator') && (
                                <div className="dash-nav-section">
                                    <div className="dash-nav-label">Job Manager</div>
                                    {hasRole('administrator', 'bpu_editor') && <NavLink href="/admin/jobs" icon="apps" label="Job Board" active={isActive('/admin/jobs')} onClick={close} />}
                                    <NavLink href="/admin/applications" icon="apps" label="Applications" active={isActive('/admin/applications')} onClick={close} />
                                    {hasRole('administrator', 'bpu_editor') && <NavLink href="/admin/reports" icon="chart" label="Reports" active={isActive('/admin/reports')} onClick={close} />}
                                </div>
                            )}

                            {/* ── Mentorship ── */}
                            {hasRole('administrator') && (
                                <div className="dash-nav-section">
                                    <div className="dash-nav-label">Mentorship</div>
                                    <NavLink href="/admin/mentors" icon="profile" label="Mentors" active={isActive('/admin/mentors')} onClick={close} />
                                    <NavLink href="/admin/mentees" icon="mentees" label="Mentees" active={isActive('/admin/mentees')} onClick={close} />
                                    <NavLink href="/admin/bookings" icon="bookings" label="Bookings" active={isActive('/admin/bookings')} onClick={close} />
                                    <NavLink href="/admin/kyc" icon="shield" label="KYC" active={isActive('/admin/kyc')} onClick={close} />
                                    <NavLink href="/admin/skills" icon="chart" label="Skills" active={isActive('/admin/skills')} onClick={close} />
                                </div>
                            )}

                            {/* ── Finance ── */}
                            {hasRole('administrator') && (
                                <div className="dash-nav-section">
                                    <div className="dash-nav-label">Finance</div>
                                    <NavLink href="/admin/transactions" icon="dollar" label="Transactions" active={isActive('/admin/transactions')} onClick={close} />
                                    <NavLink href="/admin/payouts" icon="chart" label="Payouts" active={isActive('/admin/payouts')} onClick={close} />
                                    <NavLink href="/admin/coupons" icon="tag" label="Coupons" active={isActive('/admin/coupons')} onClick={close} />
                                </div>
                            )}

                            {/* ── Analytics ── */}
                            {hasRole('administrator', 'bpu_editor') && (
                                <div className="dash-nav-section">
                                    <div className="dash-nav-label">Analytics</div>
                                    <NavLink href="/admin/stats" icon="chart" label="Platform Stats" active={isActive('/admin/stats')} onClick={close} />
                                </div>
                            )}

                            {/* ── Growth ── */}
                            {hasRole('administrator') && (
                                <div className="dash-nav-section">
                                    <div className="dash-nav-label">Growth</div>
                                    <NavLink href="/admin/referrals" icon="referral" label="Referrals" active={isActive('/admin/referrals')} onClick={close} />
                                    <NavLink href="/admin/referral-settings" icon="settings" label="Referral Settings" active={isActive('/admin/referral-settings')} onClick={close} />
                                </div>
                            )}

                            {/* ── Platform ── */}
                            {hasRole('administrator') && (
                                <div className="dash-nav-section">
                                    <div className="dash-nav-label">Platform</div>
                                    <NavLink href="/admin/email-templates" icon="messages" label="Email Templates" active={isActive('/admin/email-templates')} onClick={close} />
                                    <NavLink href="/admin/team" icon="profile" label="Team" active={isActive('/admin/team')} onClick={close} />
                                    <NavLink href="/admin/platform-settings" icon="settings" label="Settings" active={isActive('/admin/platform-settings')} onClick={close} />
                                </div>
                            )}
                        </>
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
                    {notificationBell}
                    <span className="text-sm text-text-2 hidden sm:inline">{userName}</span>
                </header>
                <main className="dash-main">
                    {children}
                </main>
            </div>
        </div>
    );
}
