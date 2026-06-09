'use client';

import { useState, useMemo } from 'react';
import { EventItem } from '@/lib/api';

type ModeFilter = 'all' | 'online' | 'in-person';

function formatMonthYear(dateStr: string): string {
    try {
        return new Date(dateStr).toLocaleDateString('en-GB', { month: 'long', year: 'numeric' });
    } catch {
        return '';
    }
}

function formatDay(dateStr: string): string {
    try {
        return new Date(dateStr).toLocaleDateString('en-GB', { weekday: 'short', day: 'numeric' });
    } catch {
        return '';
    }
}

function formatTime(dateStr: string): string {
    try {
        return new Date(dateStr).toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' });
    } catch {
        return '';
    }
}

function EventCard({ ev }: { ev: EventItem }) {
    const day  = ev.start_date ? formatDay(ev.start_date) : '';
    const time = ev.start_date ? formatTime(ev.start_date) : '';
    const endTime = ev.end_date ? formatTime(ev.end_date) : '';
    const timeRange = time ? (endTime && endTime !== time ? `${time} – ${endTime}` : time) : '';

    return (
        <div className="card card-lift overflow-hidden flex flex-col sm:flex-row">
            {ev.image && (
                <div className="shrink-0 sm:w-48">
                    <img
                        src={ev.image}
                        alt={ev.title}
                        style={{ width: '100%', height: '100%', minHeight: '140px', objectFit: 'cover' }}
                    />
                </div>
            )}
            <div className="flex-1 min-w-0 p-5 flex flex-col gap-3">
                {/* Badges row */}
                <div className="flex items-center gap-2 flex-wrap">
                    <span className="badge badge-purple" style={{ fontSize: '11px' }}>
                        {ev.is_virtual ? 'Online' : 'In Person'}
                    </span>
                    <span
                        className={`badge ${ev.cost === 'Free' || !ev.cost ? 'badge-green' : 'badge-amber'}`}
                        style={{ fontSize: '11px' }}
                    >
                        {ev.cost === 'Free' || !ev.cost ? 'Free' : ev.cost}
                    </span>
                </div>

                {/* Title */}
                <p className="font-semibold text-base leading-snug">{ev.title}</p>

                {/* Date / time / venue */}
                <div className="space-y-1">
                    {day && (
                        <p className="text-sm text-text-2 flex items-center gap-1.5">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {day}{timeRange ? ` · ${timeRange}` : ''}
                        </p>
                    )}
                    {ev.venue && (
                        <p className="text-sm text-text-2 flex items-center gap-1.5 truncate">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                            {ev.venue}
                        </p>
                    )}
                </div>

                {/* Description */}
                {ev.description && (
                    <p className="text-sm text-text-2 leading-relaxed" style={{ display: '-webkit-box', WebkitLineClamp: 2, WebkitBoxOrient: 'vertical', overflow: 'hidden' }}>
                        {ev.description}
                    </p>
                )}

                <div className="mt-auto pt-1">
                    <a
                        href={ev.register_url || ev.url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="btn btn-amber btn-sm inline-flex"
                    >
                        Register →
                    </a>
                </div>
            </div>
        </div>
    );
}

interface Props {
    events: EventItem[];
}

export default function EventBoard({ events }: Props) {
    const [search, setSearch]     = useState('');
    const [mode, setMode]         = useState<ModeFilter>('all');
    const [freeOnly, setFreeOnly] = useState(false);

    const filtered = useMemo(() => {
        return events.filter(ev => {
            if (search && !ev.title.toLowerCase().includes(search.toLowerCase()) && !ev.description.toLowerCase().includes(search.toLowerCase())) return false;
            if (mode === 'online' && !ev.is_virtual) return false;
            if (mode === 'in-person' && ev.is_virtual) return false;
            if (freeOnly && ev.cost && ev.cost !== 'Free') return false;
            return true;
        });
    }, [events, search, mode, freeOnly]);

    // Group by month
    const grouped = useMemo(() => {
        const map = new Map<string, EventItem[]>();
        for (const ev of filtered) {
            const key = ev.start_date ? formatMonthYear(ev.start_date) : 'TBC';
            if (!map.has(key)) map.set(key, []);
            map.get(key)!.push(ev);
        }
        return Array.from(map.entries());
    }, [filtered]);

    return (
        <div>
            {/* Filters */}
            <div className="card card-p mb-8 space-y-3">
                <div className="flex flex-col sm:flex-row gap-3">
                    <div className="flex-1 relative">
                        <svg
                            className="absolute left-3 top-1/2 -translate-y-1/2 text-text-3 pointer-events-none"
                            width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"
                        >
                            <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                        </svg>
                        <input
                            type="text"
                            className="field-input"
                            style={{ paddingLeft: '2.25rem' }}
                            placeholder="Search events…"
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                        />
                    </div>
                    <div className="flex rounded-lg border border-border overflow-hidden" style={{ height: '40px' }}>
                        {(['all', 'online', 'in-person'] as ModeFilter[]).map((m, i, arr) => (
                            <button
                                key={m}
                                type="button"
                                onClick={() => setMode(m)}
                                className="px-4 text-sm font-medium transition-colors"
                                style={{
                                    background: mode === m ? 'var(--brand)' : 'var(--surface)',
                                    color: mode === m ? '#fff' : 'var(--text-2)',
                                    borderRight: i < arr.length - 1 ? '1px solid var(--border)' : 'none',
                                    whiteSpace: 'nowrap',
                                }}
                            >
                                {m === 'all' ? 'All' : m === 'online' ? 'Online' : 'In Person'}
                            </button>
                        ))}
                    </div>
                </div>
                <label className="flex items-center gap-2 cursor-pointer select-none text-sm">
                    <input
                        type="checkbox"
                        checked={freeOnly}
                        onChange={e => setFreeOnly(e.target.checked)}
                        className="w-4 h-4 rounded"
                        style={{ accentColor: 'var(--brand)' }}
                    />
                    <span className="text-text-2">Free events only</span>
                </label>
            </div>

            {filtered.length === 0 ? (
                <div className="empty">
                    <p className="text-base font-medium mb-1">No events match your filters</p>
                    <p className="text-sm">Try adjusting your search or filters.</p>
                </div>
            ) : (
                <div className="space-y-10">
                    {grouped.map(([month, evs]) => (
                        <div key={month}>
                            {/* Timeline month marker */}
                            <div className="flex items-center gap-4 mb-5">
                                <div
                                    className="shrink-0 px-3 py-1 rounded-full text-sm font-bold"
                                    style={{ background: 'var(--brand)', color: '#fff' }}
                                >
                                    {month}
                                </div>
                                <div className="flex-1 h-px" style={{ background: 'var(--border)' }} />
                            </div>

                            {/* Events in this month */}
                            <div className="relative pl-6 space-y-5">
                                {/* Vertical timeline line */}
                                <div
                                    className="absolute left-0 top-2 bottom-2 w-px"
                                    style={{ background: 'var(--border)' }}
                                />
                                {evs.map(ev => (
                                    <div key={ev.id} className="relative">
                                        {/* Timeline dot */}
                                        <div
                                            className="absolute -left-6 top-5 w-3 h-3 rounded-full border-2"
                                            style={{
                                                background: 'var(--brand)',
                                                borderColor: 'var(--bg)',
                                                transform: 'translateX(-1px)',
                                            }}
                                        />
                                        <EventCard ev={ev} />
                                    </div>
                                ))}
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
