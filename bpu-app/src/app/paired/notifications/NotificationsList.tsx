'use client';

import { useState, useEffect } from 'react';

interface Notification {
    id: number;
    type: string;
    title: string;
    message: string;
    link?: string;
    is_read: boolean;
    created_at: string;
}

function timeAgo(dateStr: string): string {
    const now = new Date();
    const date = new Date(dateStr);
    if (isNaN(date.getTime())) return dateStr;
    const diff = Math.floor((now.getTime() - date.getTime()) / 1000);
    if (diff < 60) return 'just now';
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`;
    return date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

function notificationIcon(type: string) {
    switch (type) {
        case 'booking':
            return (
                <div style={{ width: 36, height: 36, borderRadius: '50%', background: 'var(--purple-bg)', display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--purple)" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2" /><line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" /><line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                </div>
            );
        case 'message':
            return (
                <div style={{ width: 36, height: 36, borderRadius: '50%', background: 'color-mix(in srgb, var(--brand) 10%, transparent)', display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--brand)" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                    </svg>
                </div>
            );
        case 'review':
            return (
                <div style={{ width: 36, height: 36, borderRadius: '50%', background: 'color-mix(in srgb, #f59e0b 10%, transparent)', display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                    </svg>
                </div>
            );
        case 'payment':
            return (
                <div style={{ width: 36, height: 36, borderRadius: '50%', background: 'color-mix(in srgb, #22c55e 10%, transparent)', display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#22c55e" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <line x1="12" y1="1" x2="12" y2="23" /><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                    </svg>
                </div>
            );
        default:
            return (
                <div style={{ width: 36, height: 36, borderRadius: '50%', background: 'var(--surface)', display: 'flex', alignItems: 'center', justifyContent: 'center', flexShrink: 0 }}>
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--text-3)" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                        <circle cx="12" cy="12" r="10" /><line x1="12" y1="16" x2="12" y2="12" /><line x1="12" y1="8" x2="12.01" y2="8" />
                    </svg>
                </div>
            );
    }
}

export default function NotificationsList() {
    const [notifications, setNotifications] = useState<Notification[]>([]);
    const [loading, setLoading] = useState(true);
    const [markingAll, setMarkingAll] = useState(false);

    const unreadCount = notifications.filter(n => !n.is_read).length;

    useEffect(() => {
        async function load() {
            try {
                const res = await fetch('/api/paired/notifications');
                if (res.ok) {
                    const data = await res.json();
                    setNotifications(
                        (data.notifications || []).map((n: Record<string, unknown>) => ({
                            ...n,
                            is_read: !!n.read_at,
                        }))
                    );
                }
            } catch {
                /* fail silently */
            } finally {
                setLoading(false);
            }
        }
        load();
    }, []);

    async function markAsRead(id: number) {
        setNotifications(prev => prev.map(n => n.id === id ? { ...n, is_read: true } : n));
        try {
            await fetch(`/api/paired/notifications/${id}/read`, { method: 'PUT' });
        } catch { /* */ }
    }

    async function markAllRead() {
        setMarkingAll(true);
        setNotifications(prev => prev.map(n => ({ ...n, is_read: true })));
        try {
            await fetch('/api/paired/notifications/read-all', { method: 'POST' });
        } catch { /* */ }
        finally { setMarkingAll(false); }
    }

    function handleClick(n: Notification) {
        if (!n.is_read) markAsRead(n.id);
        if (n.link) window.location.href = n.link;
    }

    if (loading) {
        return (
            <div className="text-center text-sm py-12" style={{ color: 'var(--text-2)' }}>
                Loading notifications...
            </div>
        );
    }

    return (
        <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
            {/* Header actions */}
            {notifications.length > 0 && unreadCount > 0 && (
                <div className="flex justify-end">
                    <button
                        onClick={markAllRead}
                        disabled={markingAll}
                        className="text-sm font-medium"
                        style={{ color: 'var(--purple)', background: 'none', border: 'none', cursor: 'pointer' }}
                    >
                        {markingAll ? 'Marking...' : `Mark all as read (${unreadCount})`}
                    </button>
                </div>
            )}

            {notifications.length === 0 ? (
                <div className="card card-p text-center py-16">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="var(--text-3)" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" style={{ margin: '0 auto', opacity: 0.5 }}>
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                        <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                    </svg>
                    <p className="font-semibold text-text-2 mt-4">No notifications yet</p>
                    <p className="text-sm text-text-3 mt-1">
                        When you get bookings, messages, or reviews, they will appear here.
                    </p>
                </div>
            ) : (
                <div className="card" style={{ overflow: 'hidden' }}>
                    {notifications.map((n, i) => (
                        <button
                            key={n.id}
                            onClick={() => handleClick(n)}
                            className="w-full text-left flex items-start gap-3 p-4 hover:bg-surface transition-colors"
                            style={{
                                background: n.is_read ? 'transparent' : 'var(--surface)',
                                cursor: n.link ? 'pointer' : 'default',
                                border: 'none',
                                borderBottom: i < notifications.length - 1 ? '1px solid var(--border)' : 'none',
                            }}
                        >
                            {notificationIcon(n.type)}
                            <div className="flex-1 min-w-0">
                                <div className="flex items-center gap-2">
                                    <p className="text-sm font-semibold text-text">{n.title}</p>
                                    {!n.is_read && (
                                        <span style={{
                                            width: 8, height: 8, borderRadius: '50%',
                                            background: 'var(--brand)', flexShrink: 0,
                                        }} />
                                    )}
                                </div>
                                <p className="text-sm text-text-2 mt-0.5">{n.message}</p>
                                <p className="text-xs text-text-3 mt-1">{timeAgo(n.created_at)}</p>
                            </div>
                        </button>
                    ))}
                </div>
            )}
        </div>
    );
}
