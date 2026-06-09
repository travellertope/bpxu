'use client';

import { useState, useMemo } from 'react';
import { CourseItem } from '@/lib/api';

const LEVEL_ORDER = ['beginner', 'intermediate', 'advanced', 'expert'];

function LevelBadge({ level }: { level: string }) {
    const l = level.toLowerCase();
    const color = l === 'beginner' ? 'badge-green' : l === 'intermediate' ? 'badge-amber' : l === 'advanced' ? 'badge-purple' : 'badge-gray';
    return <span className={`badge ${color}`} style={{ fontSize: '11px' }}>{level}</span>;
}

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
                {/* Category + level */}
                <div className="flex items-center gap-2 flex-wrap">
                    <span className="text-xs font-semibold text-text-3 uppercase tracking-wide">{course.category}</span>
                    {course.level && <LevelBadge level={course.level} />}
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
    const [search, setSearch]     = useState('');
    const [category, setCategory] = useState('All');
    const [level, setLevel]       = useState('All');

    const categories = useMemo(() => {
        const cats = Array.from(new Set(courses.map(c => c.category).filter(Boolean)));
        return ['All', ...cats.sort()];
    }, [courses]);

    const levels = useMemo(() => {
        const lvls = Array.from(new Set(courses.map(c => c.level).filter(Boolean)));
        lvls.sort((a, b) => LEVEL_ORDER.indexOf(a.toLowerCase()) - LEVEL_ORDER.indexOf(b.toLowerCase()));
        return ['All', ...lvls];
    }, [courses]);

    const filtered = useMemo(() => {
        return courses.filter(c => {
            if (search && !c.title.toLowerCase().includes(search.toLowerCase()) && !c.excerpt.toLowerCase().includes(search.toLowerCase())) return false;
            if (category !== 'All' && c.category !== category) return false;
            if (level !== 'All' && c.level !== level) return false;
            return true;
        });
    }, [courses, search, category, level]);

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
                            placeholder="Search courses…"
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                        />
                    </div>

                    <select
                        className="field-input sm:w-52"
                        value={category}
                        onChange={e => setCategory(e.target.value)}
                    >
                        {categories.map(cat => (
                            <option key={cat} value={cat}>{cat === 'All' ? 'All categories' : cat}</option>
                        ))}
                    </select>

                    {levels.length > 2 && (
                        <select
                            className="field-input sm:w-44"
                            value={level}
                            onChange={e => setLevel(e.target.value)}
                        >
                            {levels.map(l => (
                                <option key={l} value={l}>{l === 'All' ? 'All levels' : l}</option>
                            ))}
                        </select>
                    )}
                </div>
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
