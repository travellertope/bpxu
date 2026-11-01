'use client';

import { useState, useEffect, useRef, useCallback } from 'react';
import Link from 'next/link';
import { Job } from './types';
import { decodeHtml } from '@/lib/utils';

const PER_PAGE = 20;

function formatSalary(min?: number, max?: number): string | null {
    if (!min && !max) return null;
    const fmt = (n: number) =>
        n >= 1000 ? `£${(n / 1000).toFixed(0)}k` : `£${n.toLocaleString()}`;
    if (min && max) return `${fmt(min)} – ${fmt(max)}`;
    if (min) return `From ${fmt(min)}`;
    if (max) return `Up to ${fmt(max)}`;
    return null;
}

function formatDate(dateStr: string): string {
    try {
        const d = new Date(dateStr);
        const now = new Date();
        const diffDays = Math.floor((now.getTime() - d.getTime()) / (1000 * 60 * 60 * 24));
        if (diffDays === 0) return 'Today';
        if (diffDays === 1) return 'Yesterday';
        if (diffDays < 7) return `${diffDays}d ago`;
        if (diffDays < 30) return `${Math.floor(diffDays / 7)}w ago`;
        return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short' });
    } catch {
        return dateStr;
    }
}

const EMP_TYPE_COLOR: Record<string, string> = {
    'full-time':  '#1d4ed8',
    'part-time':  '#7c3aed',
    'contract':   '#0f766e',
    'freelance':  '#c2410c',
    'internship': '#16a34a',
};
function empTypeColor(type: string): string {
    const t = type.toLowerCase();
    const key = Object.keys(EMP_TYPE_COLOR).find(k => t.includes(k));
    return key ? EMP_TYPE_COLOR[key] : '#6b7280';
}

function CompanyAvatar({ name, logoUrl }: { name: string; logoUrl?: string }) {
    if (logoUrl) {
        return (
            <div style={{
                width: 44, height: 44, flexShrink: 0,
                borderRadius: 8, overflow: 'hidden',
                border: '2px solid #111317',
                background: '#fff',
            }}>
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img src={logoUrl} alt={name} style={{ width: '100%', height: '100%', objectFit: 'contain' }} />
            </div>
        );
    }
    const initials = name
        .split(/\s+/).slice(0, 2)
        .map(w => w[0]?.toUpperCase() ?? '').join('');
    return (
        <div style={{
            width: 44, height: 44, flexShrink: 0,
            borderRadius: 8, border: '2px solid #111317',
            background: 'var(--brand)', color: '#fff',
            display: 'flex', alignItems: 'center', justifyContent: 'center',
            fontSize: 14, fontWeight: 800,
        }}>
            {initials}
        </div>
    );
}

function JobCard({ job }: { job: Job }) {
    const salary = formatSalary(job.salary_min, job.salary_max);
    const isInbound = job.job_type === 'inbound';
    const company = decodeHtml(job.company);
    const title = decodeHtml(job.title);
    const excerpt = job.excerpt ?? '';
    const linkHref = isInbound ? `/jobs/${job.id}` : (job.apply_url || '#');
    const linkProps = isInbound ? {} : { target: '_blank' as const, rel: 'noopener noreferrer', prefetch: false };
    const color = empTypeColor(job.employment_type);

    return (
        <Link
            href={linkHref}
            {...linkProps}
            className="flex flex-col hover:no-underline"
            style={{
                background: '#fff',
                border: '2px solid #111317',
                borderRadius: 12,
                boxShadow: '4px 4px 0px 0px #001478',
                transition: 'transform 0.15s, box-shadow 0.15s',
                color: 'inherit',
                textDecoration: 'none',
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
            {/* Body */}
            <div className="p-5 flex flex-col gap-3 flex-1">
                {/* Company + emp type */}
                <div className="flex items-start justify-between gap-3">
                    <div className="flex items-center gap-3">
                        <CompanyAvatar name={company} logoUrl={job.employer?.logo_url || undefined} />
                        <div>
                            <p className="text-xs font-semibold mb-0.5" style={{ color: 'var(--text-3)' }}>{company}</p>
                            <span
                                className="inline-block text-white text-[10px] font-bold uppercase px-2 py-0.5 rounded"
                                style={{ background: 'var(--brand)', letterSpacing: '0.04em' }}
                            >
                                Partner
                            </span>
                        </div>
                    </div>
                    <span
                        className="text-[11px] font-bold uppercase px-2 py-1 rounded shrink-0"
                        style={{ background: `${color}18`, color, border: `1px solid ${color}40` }}
                    >
                        {job.employment_type}
                    </span>
                </div>

                {/* Title */}
                <h3 className="font-black text-sm uppercase tracking-tight leading-snug" style={{ color: '#111317' }}>
                    {title}
                </h3>

                {/* Excerpt */}
                {excerpt && (
                    <p
                        className="text-xs leading-relaxed flex-1"
                        style={{
                            color: 'var(--text-2)',
                            display: '-webkit-box',
                            WebkitLineClamp: 2,
                            WebkitBoxOrient: 'vertical',
                            overflow: 'hidden',
                        }}
                    >
                        {decodeHtml(excerpt)}
                    </p>
                )}

                {/* Location + salary */}
                <div className="flex flex-wrap items-center gap-x-4 gap-y-1">
                    <span className="flex items-center gap-1 text-xs font-medium" style={{ color: 'var(--text-2)' }}>
                        <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/>
                        </svg>
                        {job.remote ? 'Remote' : job.location}
                    </span>
                    {job.remote && job.location && !job.location.toLowerCase().includes('remote') && (
                        <span className="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded" style={{ background: 'var(--bg)', color: 'var(--text-2)', border: '1px solid var(--border)' }}>Remote</span>
                    )}
                    {job.featured && (
                        <span className="text-[10px] font-bold uppercase px-1.5 py-0.5 rounded" style={{ background: 'var(--brand-bg)', color: 'var(--brand)', border: '1px solid var(--brand-ring)' }}>Featured</span>
                    )}
                    {salary && (
                        <span className="text-xs font-semibold" style={{ color: 'var(--text-2)' }}>{salary}</span>
                    )}
                    <span className="ml-auto text-[10px]" style={{ color: 'var(--text-3)' }}>{formatDate(job.date_posted)}</span>
                </div>
            </div>

            {/* CTA */}
            <div className="px-5 pb-5">
                <div
                    className="w-full text-center text-xs font-black uppercase tracking-widest py-3 rounded-lg"
                    style={{
                        background: 'var(--brand)',
                        color: '#fff',
                        border: '2px solid #111317',
                        letterSpacing: '0.08em',
                    }}
                >
                    {isInbound ? 'Apply Direct →' : 'View & Apply →'}
                </div>
            </div>
        </Link>
    );
}

const INDUSTRIES = [
    'All Industries',
    'Technology',
    'Finance',
    'Healthcare',
    'Education',
    'Legal',
    'Marketing',
    'Engineering',
    'HR & Recruitment',
    'Creative & Media',
    'Public Sector',
    'Consulting',
    'Other',
];

const EMP_TYPE_PILLS = ['All', 'Full-time', 'Part-time', 'Contract', 'Freelance', 'Internship'];

interface JobBoardProps {
    initialJobs: Job[];
    initialTotal: number;
}

export default function JobBoard({ initialJobs, initialTotal }: JobBoardProps) {
    const [search, setSearch] = useState('');
    const [industry, setIndustry] = useState('All Industries');
    const [empType, setEmpType] = useState('All');
    const [remoteOnly, setRemoteOnly] = useState(false);

    const [jobs, setJobs] = useState<Job[]>(initialJobs);
    const [total, setTotal] = useState(initialTotal);
    const [page, setPage] = useState(1);
    const [loading, setLoading] = useState(false);
    const [loadingMore, setLoadingMore] = useState(false);
    const [stale, setStale] = useState(false);

    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    const buildUrl = useCallback((p: number) => {
        const params = new URLSearchParams({ page: String(p), per_page: String(PER_PAGE) });
        if (search) params.set('search', search);
        if (industry !== 'All Industries') params.set('industry', industry);
        if (remoteOnly) params.set('remote', '1');
        if (empType !== 'All') params.set('employment_type', empType);
        params.set('_cb', `${Date.now().toString(36)}${Math.random().toString(36).slice(2, 8)}`);
        return `/api/job-listings?${params}`;
    }, [search, industry, remoteOnly, empType]);

    useEffect(() => {
        if (debounceRef.current) clearTimeout(debounceRef.current);
        const delay = search ? 350 : 0;
        debounceRef.current = setTimeout(async () => {
            setLoading(true);
            try {
                const res = await fetch(buildUrl(1), { cache: 'no-store' });
                if (!res.ok) throw new Error(`${res.status}`);
                const data = await res.json();
                setJobs(data.jobs ?? []);
                setTotal(data.total ?? 0);
                setPage(1);
                setStale(false);
            } catch {
                setStale(true);
            } finally {
                setLoading(false);
            }
        }, delay);
        return () => { if (debounceRef.current) clearTimeout(debounceRef.current); };
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search, industry, remoteOnly, empType]);

    async function loadMore() {
        const nextPage = page + 1;
        setLoadingMore(true);
        try {
            const res = await fetch(buildUrl(nextPage), { cache: 'no-store' });
            if (!res.ok) throw new Error(`${res.status}`);
            const data = await res.json();
            setJobs(prev => [...prev, ...(data.jobs ?? [])]);
            setPage(nextPage);
            setStale(false);
        } catch {
            setStale(true);
        } finally {
            setLoadingMore(false);
        }
    }

    function resetFilters() {
        setSearch('');
        setIndustry('All Industries');
        setEmpType('All');
        setRemoteOnly(false);
    }

    const hasMore = jobs.length < total;
    const isFiltered = search || industry !== 'All Industries' || empType !== 'All' || remoteOnly;

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
                    {/* Row 1: keyword + industry + reset */}
                    <div className="grid grid-cols-1 md:grid-cols-12 gap-4">
                        <div className="md:col-span-6 relative">
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                Keywords / Role / Company
                            </label>
                            <div className="relative">
                                <svg className="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ color: 'var(--text-3)' }}>
                                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                                </svg>
                                <input
                                    type="text"
                                    className="field-input"
                                    style={{ paddingLeft: '2.25rem' }}
                                    placeholder="e.g. Commercial Lawyer, Data Engineer…"
                                    value={search}
                                    onChange={e => setSearch(e.target.value)}
                                />
                            </div>
                        </div>
                        <div className="md:col-span-4">
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                Industry / Sector
                            </label>
                            <select
                                className="field-input w-full"
                                value={industry}
                                onChange={e => setIndustry(e.target.value)}
                            >
                                {INDUSTRIES.map(i => <option key={i} value={i}>{i}</option>)}
                            </select>
                        </div>
                        <div className="md:col-span-2 flex items-end">
                            <button
                                type="button"
                                onClick={resetFilters}
                                disabled={!isFiltered}
                                className="w-full font-black text-xs uppercase tracking-wider py-2.5 px-4 rounded-lg"
                                style={{
                                    background: isFiltered ? '#111317' : 'var(--bg)',
                                    color: isFiltered ? '#fff' : 'var(--text-3)',
                                    border: '2px solid #111317',
                                    cursor: isFiltered ? 'pointer' : 'default',
                                    transition: 'background 0.15s',
                                }}
                            >
                                Reset
                            </button>
                        </div>
                    </div>

                    {/* Row 2: employment type pills + remote toggle */}
                    <div
                        className="flex flex-wrap items-center justify-between gap-4 pt-4 mt-4"
                        style={{ borderTop: '1.5px solid var(--border)' }}
                    >
                        <div className="flex flex-wrap items-center gap-2">
                            <span className="text-xs font-extrabold uppercase tracking-wide" style={{ color: 'var(--text-2)' }}>Type:</span>
                            {EMP_TYPE_PILLS.map(t => (
                                <button
                                    key={t}
                                    type="button"
                                    onClick={() => setEmpType(t)}
                                    className="text-xs font-bold uppercase px-3 py-1.5 rounded-lg"
                                    style={{
                                        border: '2px solid #111317',
                                        background: empType === t ? '#111317' : '#fff',
                                        color: empType === t ? '#fff' : 'var(--text)',
                                        transition: 'background 0.12s, color 0.12s',
                                        cursor: 'pointer',
                                    }}
                                >
                                    {t}
                                </button>
                            ))}
                        </div>
                        <label className="flex items-center gap-2 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                checked={remoteOnly}
                                onChange={e => setRemoteOnly(e.target.checked)}
                                className="w-4 h-4 rounded"
                                style={{ accentColor: 'var(--brand)' }}
                            />
                            <span className="text-xs font-bold uppercase tracking-wide" style={{ color: 'var(--text-2)' }}>Remote only</span>
                        </label>
                    </div>
                </div>
            </section>

            {/* Results header */}
            <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex items-center justify-between">
                <div>
                    <h2 className="font-black uppercase tracking-tight text-xl" style={{ color: '#111317' }}>Available Openings</h2>
                    <p className="text-sm mt-0.5" style={{ color: 'var(--text-2)' }}>
                        {loading ? 'Loading…' : total === 0 ? 'No roles match your filters' : `${total.toLocaleString()} role${total === 1 ? '' : 's'} found`}
                    </p>
                </div>
                <div className="flex items-center gap-2 text-xs font-bold uppercase tracking-wide" style={{ color: 'var(--text-3)' }}>
                    <span>Sorted by:</span>
                    <span className="px-2 py-1 rounded" style={{ background: 'var(--bg)', border: '1px solid var(--border)' }}>Most Recent</span>
                </div>
            </section>

            {/* Grid */}
            <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
                {stale && (
                    <div className="mb-4 p-3 rounded-lg text-sm font-medium" style={{ background: '#fff7ed', color: '#c2410c', border: '1px solid #fed7aa' }}>
                        Couldn&apos;t refresh the job list — results may be out of date.{' '}
                        <button type="button" onClick={() => window.location.reload()} className="underline font-semibold">Reload</button>
                    </div>
                )}

                {!loading && jobs.length === 0 ? (
                    <div className="text-center py-20">
                        <p className="font-black text-lg uppercase tracking-tight mb-2">No jobs found</p>
                        <p className="text-sm" style={{ color: 'var(--text-2)' }}>Try adjusting your search or filters.</p>
                        <button
                            type="button"
                            onClick={resetFilters}
                            className="mt-4 text-sm font-bold underline"
                            style={{ color: 'var(--brand)' }}
                        >
                            Clear all filters
                        </button>
                    </div>
                ) : (
                    <>
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            {jobs.map(job => <JobCard key={job.id} job={job} />)}
                        </div>
                        {hasMore && (
                            <div className="mt-10 text-center">
                                <button
                                    type="button"
                                    onClick={loadMore}
                                    disabled={loadingMore}
                                    className="font-black text-xs uppercase tracking-wider px-8 py-3 rounded-lg"
                                    style={{
                                        background: '#fff',
                                        border: '2px solid #111317',
                                        boxShadow: '4px 4px 0px 0px #001478',
                                        cursor: loadingMore ? 'default' : 'pointer',
                                    }}
                                >
                                    {loadingMore ? 'Loading…' : `Load More (${(total - jobs.length).toLocaleString()} remaining)`}
                                </button>
                            </div>
                        )}
                    </>
                )}
            </section>
        </div>
    );
}
