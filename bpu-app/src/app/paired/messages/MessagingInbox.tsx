'use client';

import { useState, useEffect, useRef, useCallback } from 'react';
import { decodeHtml } from '@/lib/utils';

interface Conversation {
    user_id: number;
    display_name: string;
    avatar_url?: string;
    last_message?: string;
    last_message_at?: string;
    unread_count?: number;
}

interface Message {
    id: number;
    from_user_id: number;
    to_user_id: number;
    message: string;
    created_at: string;
    is_mine?: boolean;
}

interface Props {
    initialConversations: Conversation[];
    currentUserId: number;
}

function initialsColor(id: number): string {
    const colors = ['#6366f1', '#8b5cf6', '#ec4899', '#3b82f6', '#14b8a6', '#f59e0b', '#ef4444'];
    return colors[id % colors.length];
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

export default function MessagingInbox({ initialConversations, currentUserId }: Props) {
    const [conversations, setConversations] = useState<Conversation[]>(initialConversations);
    const [selectedUserId, setSelectedUserId] = useState<number | null>(null);
    const [messages, setMessages] = useState<Message[]>([]);
    const [loadingMessages, setLoadingMessages] = useState(false);
    const [messageText, setMessageText] = useState('');
    const [sending, setSending] = useState(false);
    const [error, setError] = useState('');
    const threadRef = useRef<HTMLDivElement>(null);
    const pollRef = useRef<ReturnType<typeof setInterval> | null>(null);

    const scrollToBottom = useCallback(() => {
        if (threadRef.current) {
            threadRef.current.scrollTop = threadRef.current.scrollHeight;
        }
    }, []);

    const loadThread = useCallback(async (userId: number) => {
        setLoadingMessages(true);
        setError('');
        try {
            const res = await fetch(`/api/paired/messages/${userId}`);
            if (res.ok) {
                const data = await res.json();
                setMessages(data.messages || []);
                setTimeout(scrollToBottom, 100);
            } else {
                setError('Failed to load messages.');
            }
        } catch {
            setError('Could not load messages.');
        } finally {
            setLoadingMessages(false);
        }
    }, [scrollToBottom]);

    // Poll for new messages
    useEffect(() => {
        if (pollRef.current) clearInterval(pollRef.current);
        if (selectedUserId) {
            pollRef.current = setInterval(() => {
                loadThread(selectedUserId);
            }, 10000);
        }
        return () => {
            if (pollRef.current) clearInterval(pollRef.current);
        };
    }, [selectedUserId, loadThread]);

    function selectContact(userId: number) {
        setSelectedUserId(userId);
        loadThread(userId);
        // Mark as read in local state
        setConversations(prev =>
            prev.map(c => c.user_id === userId ? { ...c, unread_count: 0 } : c)
        );
    }

    async function handleSend(e: React.FormEvent) {
        e.preventDefault();
        if (!messageText.trim() || !selectedUserId) return;

        setSending(true);
        setError('');

        try {
            const res = await fetch('/api/paired/messages', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    to_user_id: selectedUserId,
                    message: messageText.trim(),
                }),
            });
            if (res.ok) {
                const data = await res.json();
                const newMsg = data.message;
                if (newMsg) {
                    setMessages(prev => [...prev, newMsg]);
                } else {
                    await loadThread(selectedUserId);
                }
                setMessageText('');
                setTimeout(scrollToBottom, 100);
                // Update conversation preview
                setConversations(prev =>
                    prev.map(c =>
                        c.user_id === selectedUserId
                            ? { ...c, last_message: messageText.trim(), last_message_time: new Date().toISOString() }
                            : c
                    )
                );
            } else {
                const data = await res.json();
                setError(data.message || 'Failed to send message.');
            }
        } catch {
            setError('Something went wrong.');
        } finally {
            setSending(false);
        }
    }

    const selectedContact = conversations.find(c => c.user_id === selectedUserId);

    return (
        <div className="card" style={{ overflow: 'hidden', minHeight: 500 }}>
            <div className="flex flex-col md:flex-row" style={{ height: 600 }}>

                {/* Contact List */}
                <div
                    className="border-b md:border-b-0 md:border-r border-border overflow-y-auto"
                    style={{ width: '100%', maxHeight: 200, flexShrink: 0 }}
                    // On desktop, override width
                >
                    <style>{`
                        @media (min-width: 768px) {
                            .msg-contacts { width: 320px !important; max-height: none !important; }
                        }
                    `}</style>
                    <div className="msg-contacts" style={{ width: '100%', maxHeight: 200 }}>
                        {conversations.length === 0 ? (
                            <div className="p-6 text-center text-text-3 text-sm">
                                No conversations yet.
                            </div>
                        ) : (
                            conversations.map((c) => (
                                <button
                                    key={c.user_id}
                                    onClick={() => selectContact(c.user_id)}
                                    className="w-full text-left flex items-center gap-3 p-4 hover:bg-surface transition-colors"
                                    style={{
                                        background: selectedUserId === c.user_id ? 'var(--surface)' : 'transparent',
                                        borderBottom: '1px solid var(--border)',
                                        cursor: 'pointer',
                                        border: 'none',
                                        borderBlockEnd: '1px solid var(--border)',
                                    }}
                                >
                                    {c.avatar_url ? (
                                        <img
                                            src={c.avatar_url}
                                            alt={decodeHtml(c.display_name)}
                                            className="rounded-full shrink-0 object-cover"
                                            style={{ width: 40, height: 40 }}
                                        />
                                    ) : (
                                        <div
                                            className="avatar shrink-0 text-white"
                                            style={{
                                                background: initialsColor(c.user_id),
                                                width: 40,
                                                height: 40,
                                                fontSize: '0.875rem',
                                            }}
                                        >
                                            {decodeHtml(c.display_name)?.[0] || '?'}
                                        </div>
                                    )}
                                    <div className="flex-1 min-w-0">
                                        <div className="flex items-center justify-between gap-2">
                                            <p className="text-sm font-semibold text-text truncate">
                                                {decodeHtml(c.display_name)}
                                            </p>
                                            {c.last_message_at && (
                                                <span className="text-xs text-text-3 shrink-0">
                                                    {timeAgo(c.last_message_at)}
                                                </span>
                                            )}
                                        </div>
                                        <div className="flex items-center gap-2">
                                            <p className="text-xs text-text-3 truncate flex-1">
                                                {c.last_message || 'No messages yet'}
                                            </p>
                                            {(c.unread_count ?? 0) > 0 && (
                                                <span
                                                    className="shrink-0 text-xs text-white font-bold rounded-full flex items-center justify-center"
                                                    style={{
                                                        background: 'var(--brand)',
                                                        width: 20,
                                                        height: 20,
                                                        fontSize: '0.65rem',
                                                    }}
                                                >
                                                    {c.unread_count}
                                                </span>
                                            )}
                                        </div>
                                    </div>
                                </button>
                            ))
                        )}
                    </div>
                </div>

                {/* Message Thread */}
                <div className="flex-1 flex flex-col min-w-0">
                    {!selectedUserId ? (
                        <div className="flex-1 flex items-center justify-center text-text-3 text-sm p-6">
                            Select a conversation to start messaging.
                        </div>
                    ) : (
                        <>
                            {/* Thread Header */}
                            <div className="p-4 border-b border-border flex items-center gap-3">
                                {selectedContact?.avatar_url ? (
                                    <img
                                        src={selectedContact.avatar_url}
                                        alt=""
                                        className="rounded-full object-cover"
                                        style={{ width: 32, height: 32 }}
                                    />
                                ) : (
                                    <div
                                        className="avatar text-white"
                                        style={{
                                            background: initialsColor(selectedUserId),
                                            width: 32,
                                            height: 32,
                                            fontSize: '0.75rem',
                                        }}
                                    >
                                        {selectedContact ? decodeHtml(selectedContact.display_name)?.[0] : '?'}
                                    </div>
                                )}
                                <p className="font-semibold text-sm">
                                    {selectedContact ? decodeHtml(selectedContact.display_name) : ''}
                                </p>
                            </div>

                            {/* Messages */}
                            <div
                                ref={threadRef}
                                className="flex-1 overflow-y-auto p-4 space-y-3"
                                style={{ background: 'var(--bg)' }}
                            >
                                {loadingMessages ? (
                                    <p className="text-center text-text-3 text-sm py-8">Loading messages...</p>
                                ) : messages.length === 0 ? (
                                    <p className="text-center text-text-3 text-sm py-8">No messages yet. Start the conversation!</p>
                                ) : (
                                    messages.map((msg) => {
                                        const isSent = msg.from_user_id === currentUserId;
                                        return (
                                            <div
                                                key={msg.id}
                                                className="flex"
                                                style={{ justifyContent: isSent ? 'flex-end' : 'flex-start' }}
                                            >
                                                <div
                                                    className="rounded-xl px-4 py-2"
                                                    style={{
                                                        maxWidth: '75%',
                                                        background: isSent ? 'var(--purple)' : 'var(--surface)',
                                                        color: isSent ? '#fff' : 'var(--text)',
                                                        borderBottomRightRadius: isSent ? 4 : 16,
                                                        borderBottomLeftRadius: isSent ? 16 : 4,
                                                    }}
                                                >
                                                    <p className="text-sm whitespace-pre-wrap">{msg.message}</p>
                                                    <p
                                                        className="text-xs mt-1"
                                                        style={{
                                                            opacity: 0.6,
                                                            textAlign: isSent ? 'right' : 'left',
                                                        }}
                                                    >
                                                        {new Date(msg.created_at).toLocaleTimeString('en-GB', {
                                                            hour: '2-digit',
                                                            minute: '2-digit',
                                                        })}
                                                    </p>
                                                </div>
                                            </div>
                                        );
                                    })
                                )}
                            </div>

                            {/* Error */}
                            {error && (
                                <div className="px-4 py-2">
                                    <div className="alert alert-red text-sm">{error}</div>
                                </div>
                            )}

                            {/* Input Bar */}
                            <form onSubmit={handleSend} className="p-4 border-t border-border flex gap-2">
                                <textarea
                                    className="field-input flex-1"
                                    rows={1}
                                    value={messageText}
                                    onChange={(e) => setMessageText(e.target.value)}
                                    onKeyDown={(e) => {
                                        if (e.key === 'Enter' && !e.shiftKey) {
                                            e.preventDefault();
                                            handleSend(e);
                                        }
                                    }}
                                    placeholder="Type a message..."
                                    style={{ resize: 'none', minHeight: 40 }}
                                />
                                <button
                                    type="submit"
                                    className="btn btn-purple shrink-0"
                                    disabled={sending || !messageText.trim()}
                                >
                                    {sending ? (
                                        <span className="text-sm">...</span>
                                    ) : (
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                                            <line x1="22" y1="2" x2="11" y2="13" />
                                            <polygon points="22 2 15 22 11 13 2 9 22 2" />
                                        </svg>
                                    )}
                                </button>
                            </form>
                        </>
                    )}
                </div>
            </div>
        </div>
    );
}
