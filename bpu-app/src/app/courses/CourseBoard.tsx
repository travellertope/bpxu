'use client';

import { useState, useMemo } from 'react';
import { CourseItem } from '@/lib/api';

function CourseCard({ course }: { course: CourseItem }) {
    return (
        <div className="card card-lift flex flex-col overflow-hidden">
            {course.image ? (
                <img
                    src={course.image}
                    alt={course.title}
                    style={{ width: '100%', height: '160px', objectFit: 'cover' }}
                />
            ) : (
                <div
                    style={{
                        height: '160px',
                        background: 'linear-gradient(135deg, var(--brand-bg) 0%, var(--surface) 100%)',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        fontSize: '2.5rem',
                    }}
                >
                    🎓
                </div>
            )}

            <div className="flex flex-col gap-3 p-5 flex-1">
                {/* Discipline + country tags */}
                <div className="flex flex-wrap gap-1.5">
                    {course.categories.map(cat => (
                        <span key={cat} className="badge badge-gray" style={{ fontSize: '11px' }}>{cat}</span>
                    ))}
                    {course.tags.map(tag => (
                        <span key={tag} className="badge badge-purple" style={{ fontSize: '11px' }}>{tag}</span>
                    ))}
                </div>

                {/* Title */}
                <p className="font-semibold text-sm leading-snug">{course.title}</p>

                {/* Provider + duration */}
                <p className="text-xs text-text-2">
                    by {course.provider}{course.duration ? ` · ${course.duration}` : ''}
                </p>

                {/* Excerpt */}
                {course.excerpt && (
                    <p
                        className="text-xs text-text-2 leading-relaxed flex-1"
                        style={{ display: '-webkit-box', WebkitLineClamp: 3, WebkitBoxOrient: 'vertical', overflow: 'hidden' }}
                    >
                        {course.excerpt}
                    </p>
                )}

                <a
                    href={course.learn_more_url}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="btn btn-amber btn-sm mt-auto"
                >
                    View course →
                </a>
            </div>
        </div>
    );
}

interface Props {
    courses: CourseItem[];
}

export default function CourseBoard({ courses }: Props) {
    const [search,     setSearch]     = useState('');
    const [discipline, setDiscipline] = useState('All');
    const [country,    setCountry]    = useState('All');

    const disciplines = useMemo(() => {
        const set = new Set<string>();
        courses.forEach(c => c.categories.forEach(cat => set.add(cat)));
        return ['All', ...Array.from(set).sort()];
    }, [courses]);

    const countries = useMemo(() => {
        const set = new Set<string>();
        courses.forEach(c => c.tags.forEach(tag => set.add(tag)));
        return ['All', ...Array.from(set).sort()];
    }, [courses]);

    const filtered = useMemo(() => {
        const q = search.toLowerCase();
        return courses.filter(c => {
            if (q && !c.title.toLowerCase().includes(q) && !c.excerpt.toLowerCase().includes(q)) return false;
            if (discipline !== 'All' && !c.categories.includes(discipline)) return false;
            if (country !== 'All' && !c.tags.includes(country)) return false;
            return true;
        });
    }, [courses, search, discipline, country]);

    const activeFilters = [
        discipline !== 'All' && { label: discipline, clear: () => setDiscipline('All') },
        country    !== 'All' && { label: country,    clear: () => setCountry('All') },
    ].filter(Boolean) as { label: string; clear: () => void }[];

    return (
        <div>
            {/* Filters */}
            <div className="card card-p mb-8">
                <div className="flex flex-col lg:flex-row gap-3">
                    {/* Search — takes up remaining space */}
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
                            className="field-input w-full"
                            style={{ paddingLeft: '2.25rem' }}
                            placeholder="Search courses by title or keyword…"
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                        />
                    </div>

                    <div className="flex gap-3">
                        {/* Discipline (course-category) */}
                        <div className="flex-1 lg:flex-none lg:w-52">
                            <select
                                className="field-input w-full"
                                value={discipline}
                                onChange={e => setDiscipline(e.target.value)}
                            >
                                {disciplines.map(d => (
                                    <option key={d} value={d}>{d === 'All' ? 'All disciplines' : d}</option>
                                ))}
                            </select>
                        </div>

                        {/* Country (course-tag) — only show if tags exist */}
                        {countries.length > 1 && (
                            <div className="flex-1 lg:flex-none lg:w-44">
                                <select
                                    className="field-input w-full"
                                    value={country}
                                    onChange={e => setCountry(e.target.value)}
                                >
                                    {countries.map(ct => (
                                        <option key={ct} value={ct}>{ct === 'All' ? 'All countries' : ct}</option>
                                    ))}
                                </select>
                            </div>
                        )}
                    </div>
                </div>

                {/* Active filter chips */}
                {activeFilters.length > 0 && (
                    <div className="flex flex-wrap gap-2 mt-3 pt-3" style={{ borderTop: '1px solid var(--border)' }}>
                        {activeFilters.map(f => (
                            <button
                                key={f.label}
                                onClick={f.clear}
                                className="flex items-center gap-1.5 text-xs font-medium px-2.5 py-1 rounded-full"
                                style={{ background: 'var(--brand-bg)', color: 'var(--brand)' }}
                            >
                                {f.label}
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                            </button>
                        ))}
                        <button
                            onClick={() => { setDiscipline('All'); setCountry('All'); setSearch(''); }}
                            className="text-xs text-text-3 hover:text-text underline"
                        >
                            Clear all
                        </button>
                    </div>
                )}
            </div>

            {/* Count */}
            <p className="text-sm text-text-2 mb-5">
                {filtered.length === 0
                    ? 'No courses match your filters'
                    : `${filtered.length} course${filtered.length === 1 ? '' : 's'}`}
            </p>

            {filtered.length === 0 ? (
                <div className="empty">
                    <p className="text-base font-medium mb-1">No courses found</p>
                    <p className="text-sm">Try adjusting your search or filters.</p>
                </div>
            ) : (
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    {filtered.map(c => <CourseCard key={c.id} course={c} />)}
                </div>
            )}
        </div>
    );
}
