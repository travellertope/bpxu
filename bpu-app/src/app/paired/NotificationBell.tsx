'use client';

import { useState, useEffect, useRef, useCallback } from 'react';

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
    const diff = Math.floor((now.getTime() - date.getTime()) / 1000);
    if (diff < 60) return 'just now';
    if (diff < 3600) return `${Math.floor(diff / 60)}m ago`;
    if (diff < 86400) return `${Math.floor(diff / 3600)}h ago`;
    if (diff < 604800) return `${Math.floor(diff / 86400)}d ago`;
    return date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
}

function notificationIcon(type: string) {
    switch (type) {
        case 'booking':
            return (
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--purple)" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2" /><line x1="16" y1="2" x2="16" y2="6" /><line x1="8" y1="2" x2="8" y2="6" /><line x1="3" y1="10" x2="21" y2="10" />
                </svg>
            );
        case 'message':
            return (
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--brand)" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                </svg>
            );
        case 'review':
            return (
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#f59e0b" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                </svg>
            );
        default:
            return (
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="var(--text-3)" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <circle cx="12" cy="12" r="10" /><line x1="12" y1="16" x2="12" y2="12" /><line x1="12" y1="8" x2="12.01" y2="8" />
                </svg>
            );
    }
}

export default function NotificationBell() {
    const [notifications, setNotifications] = useState<Notification[]>([]);
    const [open, setOpen] = useState(false);
    const [loading, setLoading] = useState(false);
    const dropdownRef = useRef<HTMLDivElement>(null);
    const pollRef = useRef<ReturnType<typeof setInterval> | null>(null);

    const unreadCount = notifications.filter(n => !n.is_read).length;

    const fetchNotifications = useCallback(async () => {
        try {
            const res = await fetch('/api/paired/notifications');
            if (res.ok) {
                const data = await res.json();
                const items = (data.notifications || []).map((n: Record<string, unknown>) => ({
                    ...n,
                    is_read: !!n.read_at,
                }));
                setNotifications(items);
            }
        } catch {
            // Silently fail
        }
    }, []);

    useEffect(() => {
        fetchNotifications();
        pollRef.current = setInterval(fetchNotifications, 30000);
        return () => {
            if (pollRef.current) clearInterval(pollRef.current);
        };
    }, [fetchNotifications]);

    // Close dropdown when clicking outside
    useEffect(() => {
        function handleClickOutside(e: MouseEvent) {
            if (dropdownRef.current && !dropdownRef.current.contains(e.target as Node)) {
                setOpen(false);
            }
        }
        if (open) {
            document.addEventListener('mousedown', handleClickOutside);
        }
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, [open]);

    async function markAsRead(id: number) {
        setNotifications(prev =>
            prev.map(n => n.id === id ? { ...n, is_read: true } : n)
        );
        try {
            await fetch(`/api/paired/notifications/${id}/read`, { method: 'PUT' });
        } catch {
            // Silently fail
        }
    }

    async function markAllAsRead() {
        setLoading(true);
        setNotifications(prev => prev.map(n => ({ ...n, is_read: true })));
        try {
            await fetch('/api/paired/notifications/read-all', { method: 'POST' });
        } catch {
            // Silently fail
        } finally {
            setLoading(false);
        }
    }

    function handleNotificationClick(notification: Notification) {
        markAsRead(notification.id);
        setOpen(false);
        if (notification.link) {
            window.location.href = notification.link;
        }
    }

    return (
        <div ref={dropdownRef} style={{ position: 'relative' }}>
            <button
                onClick={() => setOpen(!open)}
                className="btn btn-ghost btn-sm"
                style={{ position: 'relative', padding: '6px' }}
                aria-label={`Notifications${unreadCount > 0 ? ` (${unreadCount} unread)` : ''}`}
            >
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                    <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9" />
                    <path d="M13.73 21a2 2 0 0 1-3.46 0" />
                </svg>
                {unreadCount > 0 && (
                    <span
                        style={{
                            position: 'absolute',
                            top: 2,
                            right: 2,
                            background: 'var(--brand)',
                            color: '#fff',
                            fontSize: '0.6rem',
                            fontWeight: 700,
                            borderRadius: '50%',
                            width: 16,
                            height: 16,
                            display: 'flex',
                            alignItems: 'center',
                            justifyContent: 'center',
                            lineHeight: 1,
                        }}
                    >
                        {unreadCount > 9 ? '9+' : unreadCount}
                    </span>
                )}
            </button>

            {open && (
                <div
                    className="card"
                    style={{
                        position: 'absolute',
                        right: 0,
                        top: '100%',
                        marginTop: 8,
                        width: 360,
                        maxHeight: 440,
                        overflowY: 'auto',
                        zIndex: 50,
                        boxShadow: '0 8px 30px rgba(0,0,0,.12)',
                    }}
                >
                    {/* Header */}
                    <div className="flex items-center justify-between p-4 border-b border-border">
                        <p className="font-semibold text-sm">Notifications</p>
                        {unreadCount > 0 && (
                            <button
                                onClick={markAllAsRead}
                                disabled={loading}
                                className="text-xs font-medium hover:underline"
                                style={{ color: 'var(--purple)', background: 'none', border: 'none', cursor: 'pointer' }}
                            >
                                Mark all as read
                            </button>
                        )}
                    </div>

                    {/* Notification List */}
                    {notifications.length === 0 ? (
                        <div className="p-6 text-center text-text-3 text-sm">
                            No notifications yet.
                        </div>
                    ) : (
                        notifications.map((n) => (
                            <button
                                key={n.id}
                                onClick={() => handleNotificationClick(n)}
                                className="w-full text-left flex items-start gap-3 p-4 hover:bg-surface transition-colors"
                                style={{
                                    background: n.is_read ? 'transparent' : 'var(--surface)',
                                    cursor: 'pointer',
                                    border: 'none',
                                    borderBottom: '1px solid var(--border)',
                                }}
                            >
                                <div className="shrink-0 mt-0.5">
                                    {notificationIcon(n.type)}
                                </div>
                                <div className="flex-1 min-w-0">
                                    <div className="flex items-center gap-2">
                                        <p className="text-sm font-semibold text-text truncate">{n.title}</p>
                                        {!n.is_read && (
                                            <span
                                                style={{
                                                    width: 8,
                                                    height: 8,
                                                    borderRadius: '50%',
                                                    background: 'var(--brand)',
                                                    flexShrink: 0,
                                                }}
                                            />
                                        )}
                                    </div>
                                    <p className="text-xs text-text-3 mt-0.5 line-clamp-2">{n.message}</p>
                                    <p className="text-xs text-text-3 mt-1">{timeAgo(n.created_at)}</p>
                                </div>
                            </button>
                        ))
                    )}
                </div>
            )}
        </div>
    );
}
