'use client';

import { useState, useEffect, useRef, useCallback } from 'react';
import Link from 'next/link';
import { Job } from './types';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';
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
        if (diffDays < 7) return `${diffDays} days ago`;
        if (diffDays < 30) return `${Math.floor(diffDays / 7)}w ago`;
        return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
    } catch {
        return dateStr;
    }
}

interface JobCardProps {
    job: Job;
}

function CompanyAvatar({ name, logoUrl }: { name: string; logoUrl?: string }) {
    if (logoUrl) {
        return (
            <div
                className="shrink-0 rounded-lg overflow-hidden border border-border bg-surface"
                style={{ width: 48, height: 48 }}
            >
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img src={logoUrl} alt={name} style={{ width: '100%', height: '100%', objectFit: 'contain' }} />
            </div>
        );
    }
    // Initials fallback
    const initials = name
        .split(/\s+/)
        .slice(0, 2)
        .map(w => w[0]?.toUpperCase() ?? '')
        .join('');
    return (
        <div
            className="shrink-0 rounded-lg flex items-center justify-center text-sm font-bold border border-border"
            style={{ width: 48, height: 48, background: 'var(--brand-bg)', color: 'var(--brand)' }}
        >
            {initials}
        </div>
    );
}

function stripHtml(html: string): string {
    return html.replace(/<[^>]+>/g, ' ').replace(/\s+/g, ' ').trim();
}

function JobCard({ job }: JobCardProps) {
    const salary = formatSalary(job.salary_min, job.salary_max);
    const isInbound = job.job_type === 'inbound';
    const logoUrl = job.employer?.logo_url || undefined;
    const excerpt = job.description
        ? (() => { const t = stripHtml(job.description); return t.length > 130 ? t.slice(0, 130).trimEnd() + '…' : t; })()
        : '';

    return (
        <Link
            href={`/jobs/${job.id}`}
            className="card card-p card-lift block text-text hover:no-underline"
            style={{ textDecoration: 'none' }}
        >
            {/* Header: logo + title + badge */}
            <div className="flex items-start gap-3 mb-3">
                <CompanyAvatar name={job.company} logoUrl={logoUrl} />
                <div className="flex-1 min-w-0">
                    <div className="flex items-start justify-between gap-2">
                        <h3 className="font-semibold text-base leading-snug">{job.title}</h3>
                        {isInbound ? (
                            <span className="badge badge-green shrink-0">Apply now</span>
                        ) : (
                            <span className="badge badge-amber shrink-0">Partner</span>
                        )}
                    </div>
                    <p className="text-sm text-text-2 mt-0.5 truncate">{job.company}</p>
                    {excerpt && (
                        <p className="text-xs text-text-3 mt-1 line-clamp-2 leading-snug" style={{display:'-webkit-box',WebkitLineClamp:2,WebkitBoxOrient:'vertical',overflow:'hidden'}}>{excerpt}</p>
                    )}
                </div>
            </div>

            {/* Meta row */}
            <div className="flex flex-wrap gap-x-4 gap-y-1 text-sm text-text-2 mb-3">
                <span className="flex items-center gap-1">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {job.remote ? 'Remote' : job.location}
                </span>
                <span className="flex items-center gap-1">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                    {job.employment_type}
                </span>
                {salary && (
                    <span className="flex items-center gap-1">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        {salary}
                    </span>
                )}
            </div>

            {/* Footer: industry + badges + date */}
            <div className="flex items-center justify-between text-xs text-text-3">
                <div className="flex items-center gap-1.5">
                    <span>{job.industry}</span>
                    {job.remote && (
                        <span className="badge badge-gray" style={{ fontSize: '10px', padding: '1px 6px' }}>Remote</span>
                    )}
                    {job.featured && (
                        <span className="badge badge-amber" style={{ fontSize: '10px', padding: '1px 6px' }}>Featured</span>
                    )}
                </div>
                <span>{formatDate(job.date_posted)}</span>
            </div>
        </Link>
    );
}

const INDUSTRIES = [
    'All industries',
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

const EMPLOYMENT_TYPES = ['Full-time', 'Part-time', 'Freelance', 'Contract', 'Internship'];

type TypeFilter = 'all' | 'inbound' | 'outbound';

interface JobBoardProps {
    initialJobs: Job[];
    initialTotal: number;
}

export default function JobBoard({ initialJobs, initialTotal }: JobBoardProps) {
    const [search, setSearch] = useState('');
    const [industry, setIndustry] = useState('All industries');
    const [typeFilter, setTypeFilter] = useState<TypeFilter>('all');
    const [remoteOnly, setRemoteOnly] = useState(false);
    const [empTypes, setEmpTypes] = useState<string[]>([]);

    const [jobs, setJobs] = useState<Job[]>(initialJobs);
    const [total, setTotal] = useState(initialTotal);
    const [page, setPage] = useState(1);
    const [loading, setLoading] = useState(false);
    const [loadingMore, setLoadingMore] = useState(false);

    const debounceRef = useRef<ReturnType<typeof setTimeout> | null>(null);

    const toggleEmpType = (t: string) =>
        setEmpTypes(prev => prev.includes(t) ? prev.filter(x => x !== t) : [...prev, t]);

    const buildUrl = useCallback((p: number) => {
        const params = new URLSearchParams({ page: String(p), per_page: String(PER_PAGE) });
        if (search) params.set('search', search);
        if (industry !== 'All industries') params.set('industry', industry);
        if (typeFilter !== 'all') params.set('job_type', typeFilter);
        if (remoteOnly) params.set('remote', '1');
        if (empTypes.length === 1) params.set('employment_type', empTypes[0]);
        return `${WP_BACKEND_URL}/wp-json/bpu/v1/jobs?${params}`;
    }, [search, industry, typeFilter, remoteOnly, empTypes]);

    // Refetch from page 1 whenever filters change (debounced for search)
    useEffect(() => {
        if (debounceRef.current) clearTimeout(debounceRef.current);
        const delay = search ? 350 : 0;
        debounceRef.current = setTimeout(async () => {
            setLoading(true);
            try {
                const res = await fetch(buildUrl(1));
                if (res.ok) {
                    const data = await res.json();
                    setJobs(data.jobs ?? []);
                    setTotal(data.total ?? 0);
                    setPage(1);
                }
            } finally {
                setLoading(false);
            }
        }, delay);
        return () => { if (debounceRef.current) clearTimeout(debounceRef.current); };
    // eslint-disable-next-line react-hooks/exhaustive-deps
    }, [search, industry, typeFilter, remoteOnly, empTypes]);

    async function loadMore() {
        const nextPage = page + 1;
        setLoadingMore(true);
        try {
            const res = await fetch(buildUrl(nextPage));
            if (res.ok) {
                const data = await res.json();
                setJobs(prev => [...prev, ...(data.jobs ?? [])]);
                setPage(nextPage);
            }
        } finally {
            setLoadingMore(false);
        }
    }

    const hasMore = jobs.length < total;

    return (
        <div>
            {/* Search + filters */}
            <div className="card card-p mb-6">
                <div className="flex flex-col sm:flex-row gap-3 mb-3">
                    {/* Keywords */}
                    <div className="flex-1">
                        <div className="relative">
                            <svg
                                className="absolute left-3 top-1/2 -translate-y-1/2 text-text-3 pointer-events-none"
                                width="16" height="16" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"
                            >
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input
                                id="job-search"
                                type="text"
                                className="field-input"
                                style={{ paddingLeft: '2.25rem' }}
                                placeholder="Keywords — job title or company…"
                                value={search}
                                onChange={e => setSearch(e.target.value)}
                            />
                        </div>
                    </div>

                    {/* Industry */}
                    <div className="sm:w-52">
                        <select
                            id="job-industry"
                            className="field-input"
                            value={industry}
                            onChange={e => setIndustry(e.target.value)}
                        >
                            {INDUSTRIES.map(i => (
                                <option key={i} value={i}>{i}</option>
                            ))}
                        </select>
                    </div>

                    {/* Type toggle */}
                    <div className="flex rounded-lg border border-border overflow-hidden" style={{ height: '40px' }}>
                        {(['all', 'inbound', 'outbound'] as TypeFilter[]).map(t => (
                            <button
                                key={t}
                                type="button"
                                onClick={() => setTypeFilter(t)}
                                className="px-3 text-sm font-medium transition-colors"
                                style={{
                                    background: typeFilter === t ? 'var(--brand)' : 'var(--surface)',
                                    color: typeFilter === t ? '#fff' : 'var(--text-2)',
                                    borderRight: t !== 'outbound' ? '1px solid var(--border)' : 'none',
                                }}
                            >
                                {t === 'all' ? 'All' : t === 'inbound' ? 'Apply direct' : 'Partner'}
                            </button>
                        ))}
                    </div>
                </div>

                {/* Second row: checkboxes */}
                <div className="flex flex-wrap items-center gap-x-5 gap-y-2 text-sm">
                    <label className="flex items-center gap-2 cursor-pointer select-none">
                        <input
                            type="checkbox"
                            checked={remoteOnly}
                            onChange={e => setRemoteOnly(e.target.checked)}
                            className="w-4 h-4 rounded"
                            style={{ accentColor: 'var(--brand)' }}
                        />
                        <span className="text-text-2">Remote positions only</span>
                    </label>
                    <span className="text-text-3" style={{ fontSize: '12px' }}>
                        Employment type:
                    </span>
                    {EMPLOYMENT_TYPES.map(t => (
                        <label key={t} className="flex items-center gap-1.5 cursor-pointer select-none">
                            <input
                                type="checkbox"
                                checked={empTypes.includes(t)}
                                onChange={() => toggleEmpType(t)}
                                className="w-4 h-4 rounded"
                                style={{ accentColor: 'var(--brand)' }}
                            />
                            <span className="text-text-2">{t}</span>
                        </label>
                    ))}
                </div>
            </div>

            {/* Results count */}
            <p className="text-sm text-text-2 mb-4">
                {loading
                    ? 'Loading…'
                    : total === 0
                        ? 'No jobs match your filters'
                        : `${total.toLocaleString()} role${total === 1 ? '' : 's'} found`}
            </p>

            {/* Grid */}
            {!loading && jobs.length === 0 ? (
                <div className="empty">
                    <p className="text-base font-medium mb-1">No jobs found</p>
                    <p className="text-sm">Try adjusting your search or filters.</p>
                </div>
            ) : (
                <>
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        {jobs.map(job => (
                            <JobCard key={job.id} job={job} />
                        ))}
                    </div>
                    {hasMore && (
                        <div className="mt-8 text-center">
                            <button
                                type="button"
                                onClick={loadMore}
                                disabled={loadingMore}
                                className="btn btn-outline"
                            >
                                {loadingMore ? 'Loading…' : `Load more (${(total - jobs.length).toLocaleString()} remaining)`}
                            </button>
                        </div>
                    )}
                </>
            )}
        </div>
    );
}
