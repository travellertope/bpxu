'use client';

import { useState, useMemo } from 'react';
import { CourseItem } from '@/lib/api';

const DISCIPLINE_COLORS: Record<string, { bg: string; text: string }> = {
    'Health & Social Care':          { bg: '#f0fdf4', text: '#16a34a' },
    'Management & Leadership':       { bg: '#eff6ff', text: '#1d4ed8' },
    'Education & IAG':               { bg: '#fdf4ff', text: '#7c3aed' },
    'Digital & Technology':          { bg: '#fff7ed', text: '#c2410c' },
    'Professional Development':      { bg: 'var(--brand-bg)', text: 'var(--brand)' },
};

function disciplineChip(cat: string) {
    return DISCIPLINE_COLORS[cat] ?? { bg: 'var(--bg)', text: 'var(--text-2)' };
}

function CourseCard({ course }: { course: CourseItem }) {
    const primaryCat = course.categories[0] ?? course.category ?? 'Professional Development';
    const chip = disciplineChip(primaryCat);

    return (
        <div
            className="flex flex-col overflow-hidden"
            style={{
                background: '#fff',
                border: '2px solid #111317',
                borderRadius: 12,
                boxShadow: '4px 4px 0px 0px #001478',
                transition: 'transform 0.15s, box-shadow 0.15s',
            }}
            onMouseEnter={e => {
                (e.currentTarget as HTMLElement).style.transform = 'translate(-2px,-2px)';
                (e.currentTarget as HTMLElement).style.boxShadow = '6px 6px 0px 0px #001478';
            }}
            onMouseLeave={e => {
                (e.currentTarget as HTMLElement).style.transform = '';
                (e.currentTarget as HTMLElement).style.boxShadow = '4px 4px 0px 0px #001478';
            }}
        >
            {/* Thumbnail */}
            {course.image ? (
                /* eslint-disable-next-line @next/next/no-img-element */
                <img
                    src={course.image}
                    alt={course.title}
                    style={{ width: '100%', height: 160, objectFit: 'cover', borderBottom: '2px solid #111317', display: 'block' }}
                />
            ) : (
                <div
                    style={{
                        height: 160,
                        borderBottom: '2px solid #111317',
                        background: 'linear-gradient(135deg, #111317 0%, #2a2f38 100%)',
                        display: 'flex', alignItems: 'center', justifyContent: 'center',
                    }}
                >
                    <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="rgba(255,255,255,0.4)" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round">
                        <path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/>
                    </svg>
                </div>
            )}

            {/* Body */}
            <div className="flex flex-col gap-3 p-5 flex-1">
                {/* Badges */}
                <div className="flex flex-wrap gap-1.5">
                    <span
                        className="text-[10px] font-bold uppercase px-2 py-0.5 rounded"
                        style={{ background: chip.bg, color: chip.text, border: `1px solid ${chip.text}30` }}
                    >
                        {primaryCat}
                    </span>
                    {course.level && (
                        <span className="text-[10px] font-bold uppercase px-2 py-0.5 rounded" style={{ background: 'var(--bg)', color: 'var(--text-2)', border: '1px solid var(--border)' }}>
                            {course.level}
                        </span>
                    )}
                    {course.tags.slice(0, 1).map(tag => (
                        <span key={tag} className="text-[10px] font-bold uppercase px-2 py-0.5 rounded" style={{ background: 'var(--purple-bg)', color: '#5b21b6', border: '1px solid var(--purple-ring)' }}>
                            {tag}
                        </span>
                    ))}
                </div>

                {/* Title */}
                <p className="font-black text-sm uppercase tracking-tight leading-snug" style={{ color: '#111317' }}>
                    {course.title}
                </p>

                {/* Provider + duration */}
                <p className="text-xs font-medium" style={{ color: 'var(--text-2)' }}>
                    {course.provider}{course.duration ? ` · ${course.duration}` : ''}
                </p>

                {/* Excerpt */}
                {course.excerpt && (
                    <p
                        className="text-xs leading-relaxed flex-1"
                        style={{
                            color: 'var(--text-2)',
                            display: '-webkit-box',
                            WebkitLineClamp: 3,
                            WebkitBoxOrient: 'vertical',
                            overflow: 'hidden',
                        }}
                    >
                        {course.excerpt}
                    </p>
                )}
            </div>

            {/* CTA */}
            <div className="px-5 pb-5">
                <a
                    href={course.learn_more_url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="block w-full text-center text-xs font-black uppercase tracking-widest py-3 rounded-lg"
                    style={{
                        background: 'var(--brand)',
                        color: '#fff',
                        border: '2px solid #111317',
                        letterSpacing: '0.08em',
                        textDecoration: 'none',
                    }}
                >
                    Enrol Free →
                </a>
            </div>
        </div>
    );
}

interface Props {
    courses: CourseItem[];
}

export default function CourseBoard({ courses }: Props) {
    const [search, setSearch] = useState('');
    const [discipline, setDiscipline] = useState('All');

    const disciplines = useMemo(() => {
        const set = new Set<string>();
        courses.forEach(c => c.categories.forEach(cat => set.add(cat)));
        return ['All', ...Array.from(set).sort()];
    }, [courses]);

    const filtered = useMemo(() => {
        const q = search.toLowerCase();
        return courses.filter(c => {
            if (q && !c.title.toLowerCase().includes(q) && !c.excerpt.toLowerCase().includes(q)) return false;
            if (discipline !== 'All' && !c.categories.includes(discipline)) return false;
            return true;
        });
    }, [courses, search, discipline]);

    return (
        <div>
            {/* Floating filter panel — overlaps the dark hero */}
            <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" style={{ marginTop: -32, position: 'relative', zIndex: 20 }}>
                <div style={{
                    background: '#fff',
                    border: '2px solid #111317',
                    borderRadius: 16,
                    boxShadow: '6px 6px 0px 0px #001478',
                    padding: '24px',
                }}>
                    {/* Search row */}
                    <div className="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div className="md:col-span-10 relative">
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                Search Courses
                            </label>
                            <div className="relative">
                                <svg className="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ color: 'var(--text-3)' }}>
                                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                </svg>
                                <input
                                    type="text"
                                    className="field-input"
                                    style={{ paddingLeft: '2.25rem' }}
                                    placeholder="e.g. Leadership, Cyber Security, Mental Health…"
                                    value={search}
                                    onChange={e => setSearch(e.target.value)}
                                />
                            </div>
                        </div>
                        <div className="md:col-span-2 flex items-end">
                            <button
                                type="button"
                                onClick={() => { setSearch(''); setDiscipline('All'); }}
                                disabled={!search && discipline === 'All'}
                                className="w-full font-black text-xs uppercase tracking-wider py-2.5 px-4 rounded-lg"
                                style={{
                                    background: (search || discipline !== 'All') ? '#111317' : 'var(--bg)',
                                    color: (search || discipline !== 'All') ? '#fff' : 'var(--text-3)',
                                    border: '2px solid #111317',
                                    cursor: (search || discipline !== 'All') ? 'pointer' : 'default',
                                    transition: 'background 0.15s',
                                }}
                            >
                                Reset
                            </button>
                        </div>
                    </div>

                    {/* Discipline pill filters */}
                    {disciplines.length > 1 && (
                        <div
                            className="flex flex-wrap items-center gap-2 pt-4 mt-4"
                            style={{ borderTop: '1.5px solid var(--border)' }}
                        >
                            <span className="text-xs font-extrabold uppercase tracking-wide" style={{ color: 'var(--text-2)' }}>Discipline:</span>
                            {disciplines.map(d => (
                                <button
                                    key={d}
                                    type="button"
                                    onClick={() => setDiscipline(d)}
                                    className="text-xs font-bold uppercase px-3 py-1.5 rounded-lg"
                                    style={{
                                        border: '2px solid #111317',
                                        background: discipline === d ? '#111317' : '#fff',
                                        color: discipline === d ? '#fff' : 'var(--text)',
                                        transition: 'background 0.12s, color 0.12s',
                                        cursor: 'pointer',
                                    }}
                                >
                                    {d}
                                </button>
                            ))}
                        </div>
                    )}
                </div>
            </section>

            {/* Results header */}
            <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex items-center justify-between">
                <div>
                    <h2 className="font-black uppercase tracking-tight text-xl" style={{ color: '#111317' }}>Available Courses</h2>
                    <p className="text-sm mt-0.5" style={{ color: 'var(--text-2)' }}>
                        {filtered.length === 0
                            ? 'No courses match your filters'
                            : `${filtered.length} course${filtered.length === 1 ? '' : 's'} available`}
                    </p>
                </div>
                {discipline !== 'All' && (
                    <button
                        type="button"
                        onClick={() => setDiscipline('All')}
                        className="flex items-center gap-1.5 text-xs font-bold px-3 py-1.5 rounded-full"
                        style={{ background: 'var(--brand-bg)', color: 'var(--brand)', border: '1px solid var(--brand-ring)' }}
                    >
                        {discipline}
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                )}
            </section>

            {/* Grid */}
            <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
                {filtered.length === 0 ? (
                    <div className="text-center py-20">
                        <p className="font-black text-lg uppercase tracking-tight mb-2">No courses found</p>
                        <p className="text-sm" style={{ color: 'var(--text-2)' }}>Try adjusting your search or filters.</p>
                        <button
                            type="button"
                            onClick={() => { setSearch(''); setDiscipline('All'); }}
                            className="mt-4 text-sm font-bold underline"
                            style={{ color: 'var(--brand)' }}
                        >
                            Clear all filters
                        </button>
                    </div>
                ) : (
                    <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        {filtered.map(c => <CourseCard key={c.id} course={c} />)}
                    </div>
                )}
            </section>
        </div>
    );
}
