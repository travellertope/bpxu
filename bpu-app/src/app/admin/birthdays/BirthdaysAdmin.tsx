'use client';

import { useState, useEffect, useCallback, useMemo } from 'react';

interface BirthdayRow {
    id: number;
    name: string;
    email: string;
    month: number;
    day: number;
    turns: number;
    is_today: boolean;
    occurred: boolean;
    sent_this_year: boolean;
    last_sent: string;
}

interface Summary {
    today: number;
    sent: number;
    missed: number;
    upcoming: number;
}

interface BirthdayData {
    year: number;
    month: number;
    rows: BirthdayRow[];
    summary: Summary;
}

const STATUS_OPTIONS = [
    { value: 'all', label: 'All' },
    { value: 'today', label: "Today's birthdays" },
    { value: 'sent', label: 'Sent' },
    { value: 'missed', label: 'Missed / not sent' },
    { value: 'upcoming', label: 'Upcoming' },
];

const WEEKDAYS = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

function monthName(month: number): string {
    return new Date(2000, month - 1, 1).toLocaleString('en-GB', { month: 'long' });
}

function parseSqlDate(s: string): Date {
    return new Date(s.replace(' ', 'T'));
}

export default function BirthdaysAdmin() {
    const now = new Date();

    const [month, setMonth] = useState(now.getMonth() + 1);
    const [status, setStatus] = useState('all');
    const [search, setSearch] = useState('');
    const [activeSearch, setActiveSearch] = useState('');

    const [data, setData] = useState<BirthdayData | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [sendingIds, setSendingIds] = useState<Set<number>>(new Set());

    const fetchBirthdays = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const params = new URLSearchParams();
            params.set('month', String(month));
            params.set('status', status);
            if (activeSearch) params.set('search', activeSearch);

            const res = await fetch(`/api/paired/admin/birthdays?${params}`);
            const json = await res.json();
            if (!res.ok) throw new Error(json.message || json.error || 'Failed to load birthdays.');
            setData(json);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load birthdays.');
        } finally {
            setLoading(false);
        }
    }, [month, status, activeSearch]);

    useEffect(() => {
        fetchBirthdays();
    }, [fetchBirthdays]);

    function handleSearchSubmit(e: React.FormEvent) {
        e.preventDefault();
        setActiveSearch(search);
    }

    async function handleSend(userId: number) {
        setSendingIds(prev => new Set(prev).add(userId));
        try {
            const res = await fetch('/api/paired/admin/birthdays/send', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ user_id: userId }),
            });
            const json = await res.json();
            if (!res.ok) throw new Error(json.message || json.error || 'Failed to send email.');
            await fetchBirthdays();
        } catch (e) {
            alert(e instanceof Error ? e.message : 'Failed to send email.');
        } finally {
            setSendingIds(prev => {
                const next = new Set(prev);
                next.delete(userId);
                return next;
            });
        }
    }

    const year = data?.year ?? now.getFullYear();
    const rows = useMemo(() => data?.rows ?? [], [data]);

    const byDay = useMemo(() => {
        const map = new Map<number, BirthdayRow[]>();
        rows.forEach(r => {
            const arr = map.get(r.day) || [];
            arr.push(r);
            map.set(r.day, arr);
        });
        return map;
    }, [rows]);

    const daysInMonth = new Date(year, month, 0).getDate();
    const leadingBlanks = (new Date(year, month - 1, 1).getDay() + 6) % 7;
    const isCurrentMonth = month === now.getMonth() + 1 && year === now.getFullYear();
    const todayDate = now.getDate();

    function goToMonth(m: number) {
        setMonth(((m - 1 + 12) % 12) + 1);
    }

    function statusBadge(r: BirthdayRow) {
        if (r.is_today && r.sent_this_year) {
            const time = r.last_sent ? ` at ${parseSqlDate(r.last_sent).toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' })}` : '';
            return <span className="badge badge-green">Sent today{time}</span>;
        }
        if (r.is_today && !r.sent_this_year) {
            return <span className="badge badge-amber">Pending today</span>;
        }
        if (r.occurred && r.sent_this_year) {
            const on = r.last_sent ? ` on ${parseSqlDate(r.last_sent).toLocaleDateString('en-GB', { day: 'numeric', month: 'short' })}` : '';
            return <span className="badge badge-green">Sent{on}</span>;
        }
        if (r.occurred && !r.sent_this_year) {
            return <span className="badge badge-red">Missed</span>;
        }
        return <span className="badge badge-gray">Upcoming</span>;
    }

    return (
        <div className="space-y-5">
            {/* Filters */}
            <form onSubmit={handleSearchSubmit} className="card card-p space-y-4">
                <p className="text-sm font-semibold">Filters</p>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div>
                        <label className="text-xs text-text-3 mb-1 block">Month</label>
                        <select className="field-input w-full" value={month} onChange={e => setMonth(Number(e.target.value))}>
                            {Array.from({ length: 12 }, (_, i) => i + 1).map(m => (
                                <option key={m} value={m}>{monthName(m)}</option>
                            ))}
                        </select>
                    </div>
                    <div>
                        <label className="text-xs text-text-3 mb-1 block">Email status</label>
                        <select className="field-input w-full" value={status} onChange={e => setStatus(e.target.value)}>
                            {STATUS_OPTIONS.map(o => (
                                <option key={o.value} value={o.value}>{o.label}</option>
                            ))}
                        </select>
                    </div>
                    <div>
                        <label className="text-xs text-text-3 mb-1 block">Search</label>
                        <input
                            type="text"
                            className="field-input w-full"
                            placeholder="Name or email"
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                        />
                    </div>
                </div>
                <div className="flex flex-wrap gap-2">
                    <button type="submit" className="btn btn-purple btn-sm">Apply Filters</button>
                    <button type="button" className="btn btn-ghost btn-sm" onClick={() => goToMonth(month - 1)}>‹ Prev month</button>
                    <button type="button" className="btn btn-ghost btn-sm" onClick={() => goToMonth(now.getMonth() + 1)}>This month</button>
                    <button type="button" className="btn btn-ghost btn-sm" onClick={() => goToMonth(month + 1)}>Next month ›</button>
                </div>
            </form>

            {loading ? (
                <div className="text-center text-sm text-text-2 py-12">Loading birthdays...</div>
            ) : error ? (
                <div className="card card-p text-center py-10" style={{ color: 'var(--err)' }}>{error}</div>
            ) : !data ? null : (
                <>
                    {/* Summary cards */}
                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div className="card card-p text-center">
                            <p className="text-xs text-text-3 mb-1">Today</p>
                            <p className="text-2xl font-bold" style={{ color: 'var(--purple)' }}>{data.summary.today}</p>
                        </div>
                        <div className="card card-p text-center">
                            <p className="text-xs text-text-3 mb-1">Sent</p>
                            <p className="text-2xl font-bold" style={{ color: 'var(--ok)' }}>{data.summary.sent}</p>
                        </div>
                        <div className="card card-p text-center">
                            <p className="text-xs text-text-3 mb-1">Missed / Not Sent</p>
                            <p className="text-2xl font-bold" style={{ color: 'var(--err)' }}>{data.summary.missed}</p>
                        </div>
                        <div className="card card-p text-center">
                            <p className="text-xs text-text-3 mb-1">Upcoming</p>
                            <p className="text-2xl font-bold text-text-2">{data.summary.upcoming}</p>
                        </div>
                    </div>

                    {/* Calendar */}
                    <div className="card card-p">
                        <h2 className="text-lg font-semibold mb-4">{monthName(month)} {year}</h2>
                        <div className="grid grid-cols-7 gap-1.5">
                            {WEEKDAYS.map(dow => (
                                <div key={dow} className="text-center text-[11px] font-bold uppercase text-text-3 py-1">{dow}</div>
                            ))}
                            {Array.from({ length: leadingBlanks }, (_, i) => <div key={`blank-${i}`} />)}
                            {Array.from({ length: daysInMonth }, (_, i) => i + 1).map(day => {
                                const dayRows = byDay.get(day) || [];
                                const isToday = isCurrentMonth && day === todayDate;
                                const hasMissed = dayRows.some(r => r.occurred && !r.sent_this_year && !r.is_today);
                                const borderColor = isToday ? 'var(--purple)' : hasMissed ? 'var(--err)' : 'var(--border)';
                                return (
                                    <div
                                        key={day}
                                        className="rounded-md p-1.5"
                                        style={{
                                            minHeight: 72,
                                            border: `1.5px solid ${borderColor}`,
                                            background: isToday ? 'var(--purple-bg)' : 'var(--surface)',
                                        }}
                                    >
                                        <div className="text-xs font-bold text-text-2">{day}</div>
                                        {dayRows.map(r => {
                                            const dot = r.sent_this_year ? 'var(--ok)' : r.occurred ? 'var(--err)' : 'var(--text-3)';
                                            return (
                                                <div key={r.id} title={`${r.name} — ${r.email}`} className="text-[11px] mt-0.5 truncate flex items-center gap-1">
                                                    <span className="inline-block rounded-full shrink-0" style={{ width: 6, height: 6, background: dot }} />
                                                    <span className="truncate">{r.name}</span>
                                                </div>
                                            );
                                        })}
                                    </div>
                                );
                            })}
                        </div>
                        <div className="flex flex-wrap gap-4 mt-3 text-xs text-text-3">
                            <span className="flex items-center gap-1"><span className="inline-block rounded-full" style={{ width: 8, height: 8, background: 'var(--ok)' }} /> Sent</span>
                            <span className="flex items-center gap-1"><span className="inline-block rounded-full" style={{ width: 8, height: 8, background: 'var(--err)' }} /> Missed / not yet sent</span>
                            <span className="flex items-center gap-1"><span className="inline-block rounded-full" style={{ width: 8, height: 8, background: 'var(--text-3)' }} /> Upcoming</span>
                        </div>
                    </div>

                    {rows.length === 0 ? (
                        <div className="card card-p text-center py-10">
                            <p className="font-semibold text-text-2">No members match your filters</p>
                        </div>
                    ) : (
                        <>
                            <p className="text-sm text-text-3">{rows.length} member{rows.length !== 1 ? 's' : ''}</p>

                            {/* Desktop table */}
                            <div className="card hidden md:block" style={{ overflow: 'auto' }}>
                                <table style={{ width: '100%', borderCollapse: 'collapse', minWidth: 700 }}>
                                    <thead>
                                        <tr style={{ borderBottom: '1px solid var(--border)', background: 'var(--surface)' }}>
                                            <th className="text-left text-xs font-semibold text-text-3 p-3 whitespace-nowrap">Name</th>
                                            <th className="text-left text-xs font-semibold text-text-3 p-3 whitespace-nowrap">Email</th>
                                            <th className="text-left text-xs font-semibold text-text-3 p-3 whitespace-nowrap">Birthday</th>
                                            <th className="text-center text-xs font-semibold text-text-3 p-3 whitespace-nowrap">Turns</th>
                                            <th className="text-left text-xs font-semibold text-text-3 p-3 whitespace-nowrap">Email status</th>
                                            <th className="text-left text-xs font-semibold text-text-3 p-3 whitespace-nowrap">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {rows.map(r => (
                                            <tr key={r.id} style={{ borderBottom: '1px solid var(--border)' }}>
                                                <td className="p-3 text-sm font-semibold">{r.name}</td>
                                                <td className="p-3 text-sm text-text-2">{r.email}</td>
                                                <td className="p-3 text-sm text-text-2 whitespace-nowrap">
                                                    {new Date(year, r.month - 1, r.day).toLocaleDateString('en-GB', { day: 'numeric', month: 'short' })}
                                                </td>
                                                <td className="p-3 text-sm text-center">{r.turns}</td>
                                                <td className="p-3">{statusBadge(r)}</td>
                                                <td className="p-3">
                                                    <button
                                                        className="btn btn-ghost btn-sm"
                                                        disabled={sendingIds.has(r.id)}
                                                        onClick={() => handleSend(r.id)}
                                                    >
                                                        {sendingIds.has(r.id) ? 'Sending…' : r.sent_this_year ? 'Resend' : 'Send now'}
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            {/* Mobile cards */}
                            <div className="md:hidden space-y-3">
                                {rows.map(r => (
                                    <div key={r.id} className="card card-p space-y-3">
                                        <div className="flex items-start justify-between gap-2">
                                            <div>
                                                <p className="text-sm font-semibold">{r.name}</p>
                                                <p className="text-xs text-text-3">{r.email}</p>
                                            </div>
                                            {statusBadge(r)}
                                        </div>
                                        <div className="flex items-center justify-between">
                                            <p className="text-xs text-text-2">
                                                {new Date(year, r.month - 1, r.day).toLocaleDateString('en-GB', { day: 'numeric', month: 'short' })} · Turns {r.turns}
                                            </p>
                                            <button
                                                className="btn btn-ghost btn-sm"
                                                disabled={sendingIds.has(r.id)}
                                                onClick={() => handleSend(r.id)}
                                            >
                                                {sendingIds.has(r.id) ? 'Sending…' : r.sent_this_year ? 'Resend' : 'Send now'}
                                            </button>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </>
                    )}
                </>
            )}
        </div>
    );
}
