'use client';

import { useState, useMemo } from 'react';
import Link from 'next/link';
import { Job } from './types';

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

function JobCard({ job }: JobCardProps) {
    const salary = formatSalary(job.salary_min, job.salary_max);
    const isInbound = job.job_type === 'inbound';

    return (
        <Link
            href={`/jobs/${job.id}`}
            className="card card-p card-lift block text-text hover:no-underline"
            style={{ textDecoration: 'none' }}
        >
            <div className="flex items-start justify-between gap-3 mb-3">
                <div className="flex-1 min-w-0">
                    <h3 className="font-semibold text-base leading-snug truncate">{job.title}</h3>
                    <p className="text-sm text-text-2 mt-0.5">{job.company}</p>
                </div>
                {isInbound ? (
                    <span className="badge badge-green flex-shrink-0">Apply now</span>
                ) : (
                    <span className="badge badge-amber flex-shrink-0">Partner role</span>
                )}
            </div>

            <div className="flex flex-wrap gap-x-4 gap-y-1 text-sm text-text-2 mb-3">
                <span className="flex items-center gap-1">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    {job.location}
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

            <div className="flex items-center justify-between text-xs text-text-3">
                <span>{job.industry}</span>
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

type TypeFilter = 'all' | 'inbound' | 'outbound';

interface JobBoardProps {
    initialJobs: Job[];
}

export default function JobBoard({ initialJobs }: JobBoardProps) {
    const [search, setSearch] = useState('');
    const [industry, setIndustry] = useState('All industries');
    const [typeFilter, setTypeFilter] = useState<TypeFilter>('all');

    const filtered = useMemo(() => {
        return initialJobs.filter(job => {
            const q = search.toLowerCase();
            const matchSearch =
                !q ||
                job.title.toLowerCase().includes(q) ||
                job.company.toLowerCase().includes(q) ||
                job.location.toLowerCase().includes(q);

            const matchIndustry =
                industry === 'All industries' || job.industry === industry;

            const matchType =
                typeFilter === 'all' || job.job_type === typeFilter;

            return matchSearch && matchIndustry && matchType;
        });
    }, [initialJobs, search, industry, typeFilter]);

    return (
        <div>
            {/* Filters */}
            <div className="card card-p mb-8">
                <div className="flex flex-col sm:flex-row gap-3">
                    {/* Search */}
                    <div className="flex-1">
                        <label htmlFor="job-search" className="field-label">Search</label>
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
                                className="field-input pl-9"
                                placeholder="Job title, company, or location…"
                                value={search}
                                onChange={e => setSearch(e.target.value)}
                            />
                        </div>
                    </div>

                    {/* Industry */}
                    <div className="sm:w-52">
                        <label htmlFor="job-industry" className="field-label">Industry</label>
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
                    <div>
                        <p className="field-label">Type</p>
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
                                    {t === 'all' ? 'All' : t === 'inbound' ? 'Inbound' : 'Outbound'}
                                </button>
                            ))}
                        </div>
                    </div>
                </div>
            </div>

            {/* Results count */}
            <p className="text-sm text-text-2 mb-4">
                {filtered.length === 0
                    ? 'No jobs match your filters'
                    : `${filtered.length} role${filtered.length === 1 ? '' : 's'} found`}
            </p>

            {/* Grid */}
            {filtered.length === 0 ? (
                <div className="empty">
                    <p className="text-base font-medium mb-1">No jobs found</p>
                    <p className="text-sm">Try adjusting your search or filters.</p>
                </div>
            ) : (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                    {filtered.map(job => (
                        <JobCard key={job.id} job={job} />
                    ))}
                </div>
            )}
        </div>
    );
}
