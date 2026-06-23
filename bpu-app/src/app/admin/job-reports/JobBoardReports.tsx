'use client';

import { useState, useEffect, useCallback } from 'react';
import { decodeHtml } from '@/lib/utils';

interface JobRow {
    id: number;
    title: string;
    company: string;
    job_type: string;
    industry: string;
    location: string;
    status: string;
    date: string;
    impressions: number;
    clicks: number;
    applications: number;
    ctr: number;
}

interface EmployerOption {
    id: number;
    name: string;
}

interface Summary {
    impressions: number;
    clicks: number;
    applications: number;
    avg_ctr: number;
}

interface ReportData {
    summary: Summary;
    rows: JobRow[];
    employers: EmployerOption[];
}

const STATUS_BADGE: Record<string, string> = {
    publish: 'badge-green',
    pending: 'badge-amber',
    draft:   'badge-gray',
    trash:   'badge-red',
};

export default function JobBoardReports() {
    const [data, setData] = useState<ReportData | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    const [employerId, setEmployerId] = useState('');
    const [jobType, setJobType] = useState('');
    const [dateFrom, setDateFrom] = useState('');
    const [dateTo, setDateTo] = useState('');

    const [activeEmployerId, setActiveEmployerId] = useState('');
    const [activeJobType, setActiveJobType] = useState('');
    const [activeDateFrom, setActiveDateFrom] = useState('');
    const [activeDateTo, setActiveDateTo] = useState('');

    const [sortKey, setSortKey] = useState<keyof JobRow>('date');
    const [sortDir, setSortDir] = useState<'asc' | 'desc'>('desc');

    const fetchReports = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const params = new URLSearchParams();
            if (activeEmployerId) params.set('employer_term_id', activeEmployerId);
            if (activeJobType) params.set('job_type', activeJobType);
            if (activeDateFrom) params.set('date_from', activeDateFrom);
            if (activeDateTo) params.set('date_to', activeDateTo);

            const res = await fetch(`/api/paired/admin/job-reports${params.toString() ? `?${params}` : ''}`);
            const json = await res.json();
            if (!res.ok) throw new Error(json.error || 'Failed to load reports.');
            setData(json);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load reports.');
        } finally {
            setLoading(false);
        }
    }, [activeEmployerId, activeJobType, activeDateFrom, activeDateTo]);

    useEffect(() => {
        fetchReports();
    }, [fetchReports]);

    function handleFilter(e: React.FormEvent) {
        e.preventDefault();
        setActiveEmployerId(employerId);
        setActiveJobType(jobType);
        setActiveDateFrom(dateFrom);
        setActiveDateTo(dateTo);
    }

    function handleReset() {
        setEmployerId(''); setJobType(''); setDateFrom(''); setDateTo('');
        setActiveEmployerId(''); setActiveJobType(''); setActiveDateFrom(''); setActiveDateTo('');
    }

    function toggleSort(key: keyof JobRow) {
        if (sortKey === key) {
            setSortDir(d => d === 'asc' ? 'desc' : 'asc');
        } else {
            setSortKey(key);
            setSortDir('desc');
        }
    }

    const sorted = data
        ? [...data.rows].sort((a, b) => {
            const av = a[sortKey], bv = b[sortKey];
            if (typeof av === 'number' && typeof bv === 'number') {
                return sortDir === 'asc' ? av - bv : bv - av;
            }
            return sortDir === 'asc'
                ? String(av).localeCompare(String(bv))
                : String(bv).localeCompare(String(av));
        })
        : [];

    const SortTh = ({ label, col }: { label: string; col: keyof JobRow }) => (
        <th
            className="text-left text-xs font-semibold text-text-3 p-3 cursor-pointer select-none whitespace-nowrap"
            onClick={() => toggleSort(col)}
        >
            {label}
            {sortKey === col && (
                <span className="ml-1">{sortDir === 'asc' ? '↑' : '↓'}</span>
            )}
        </th>
    );

    const isFiltered = activeEmployerId || activeJobType || activeDateFrom || activeDateTo;

    return (
        <div className="space-y-5">
            {/* Filters */}
            <form onSubmit={handleFilter} className="card card-p space-y-4">
                <p className="text-sm font-semibold">Filters</p>
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                    <div>
                        <label className="text-xs text-text-3 mb-1 block">Employer</label>
                        <select
                            className="field-input w-full"
                            value={employerId}
                            onChange={e => setEmployerId(e.target.value)}
                        >
                            <option value="">All employers</option>
                            {(data?.employers || []).map(em => (
                                <option key={em.id} value={String(em.id)}>{em.name}</option>
                            ))}
                        </select>
                    </div>
                    <div>
                        <label className="text-xs text-text-3 mb-1 block">Job Type</label>
                        <select
                            className="field-input w-full"
                            value={jobType}
                            onChange={e => setJobType(e.target.value)}
                        >
                            <option value="">All types</option>
                            <option value="inbound">Inbound</option>
                            <option value="outbound">Outbound</option>
                        </select>
                    </div>
                    <div>
                        <label className="text-xs text-text-3 mb-1 block">From</label>
                        <input
                            type="date"
                            className="field-input w-full"
                            value={dateFrom}
                            onChange={e => setDateFrom(e.target.value)}
                        />
                    </div>
                    <div>
                        <label className="text-xs text-text-3 mb-1 block">To</label>
                        <input
                            type="date"
                            className="field-input w-full"
                            value={dateTo}
                            onChange={e => setDateTo(e.target.value)}
                        />
                    </div>
                </div>
                <div className="flex gap-2">
                    <button type="submit" className="btn btn-purple btn-sm">Apply Filters</button>
                    {isFiltered && (
                        <button type="button" className="btn btn-ghost btn-sm" onClick={handleReset}>
                            Reset
                        </button>
                    )}
                </div>
            </form>

            {loading ? (
                <div className="text-center text-sm text-text-2 py-12">Loading reports...</div>
            ) : error ? (
                <div className="card card-p text-center py-10" style={{ color: 'var(--err)' }}>{error}</div>
            ) : !data ? null : (
                <>
                    {/* Summary cards */}
                    <div className="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div className="card card-p text-center">
                            <p className="text-xs text-text-3 mb-1">Impressions</p>
                            <p className="text-2xl font-bold">{data.summary.impressions.toLocaleString()}</p>
                        </div>
                        <div className="card card-p text-center">
                            <p className="text-xs text-text-3 mb-1">Clicks</p>
                            <p className="text-2xl font-bold">{data.summary.clicks.toLocaleString()}</p>
                        </div>
                        <div className="card card-p text-center">
                            <p className="text-xs text-text-3 mb-1">Applications</p>
                            <p className="text-2xl font-bold">{data.summary.applications.toLocaleString()}</p>
                        </div>
                        <div className="card card-p text-center">
                            <p className="text-xs text-text-3 mb-1">Avg CTR</p>
                            <p className="text-2xl font-bold">{data.summary.avg_ctr}%</p>
                        </div>
                    </div>

                    {sorted.length === 0 ? (
                        <div className="card card-p text-center py-10">
                            <p className="font-semibold text-text-2">No jobs match your filters</p>
                        </div>
                    ) : (
                        <>
                            <p className="text-sm text-text-3">{sorted.length} job{sorted.length !== 1 ? 's' : ''}</p>

                            {/* Desktop table */}
                            <div className="card hidden md:block" style={{ overflow: 'auto' }}>
                                <table style={{ width: '100%', borderCollapse: 'collapse', minWidth: 700 }}>
                                    <thead>
                                        <tr style={{ borderBottom: '1px solid var(--border)', background: 'var(--surface)' }}>
                                            <SortTh label="Job Title" col="title" />
                                            <SortTh label="Company" col="company" />
                                            <th className="text-left text-xs font-semibold text-text-3 p-3 whitespace-nowrap">Status</th>
                                            <SortTh label="Impressions" col="impressions" />
                                            <SortTh label="Clicks" col="clicks" />
                                            <SortTh label="Applications" col="applications" />
                                            <SortTh label="CTR %" col="ctr" />
                                            <SortTh label="Posted" col="date" />
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {sorted.map(row => (
                                            <tr key={row.id} style={{ borderBottom: '1px solid var(--border)' }}>
                                                <td className="p-3">
                                                    <a
                                                        href={`/jobs/${row.id}`}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="text-sm font-semibold hover:underline"
                                                        style={{ color: 'var(--link)' }}
                                                    >
                                                        {decodeHtml(row.title)}
                                                    </a>
                                                </td>
                                                <td className="p-3 text-sm text-text-2 whitespace-nowrap">{decodeHtml(row.company)}</td>
                                                <td className="p-3">
                                                    <span className={`badge ${STATUS_BADGE[row.status] || 'badge-gray'}`}>{row.status}</span>
                                                </td>
                                                <td className="p-3 text-center text-sm">{row.impressions.toLocaleString()}</td>
                                                <td className="p-3 text-center text-sm">{row.clicks.toLocaleString()}</td>
                                                <td className="p-3 text-center text-sm">{row.applications.toLocaleString()}</td>
                                                <td className="p-3 text-center text-sm">{row.ctr}%</td>
                                                <td className="p-3 text-sm text-text-3 whitespace-nowrap">
                                                    {new Date(row.date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })}
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>

                            {/* Mobile cards */}
                            <div className="md:hidden space-y-3">
                                {sorted.map(row => (
                                    <div key={row.id} className="card card-p space-y-3">
                                        <div className="flex items-start justify-between gap-2">
                                            <a
                                                href={`/jobs/${row.id}`}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="text-sm font-semibold hover:underline"
                                                style={{ color: 'var(--link)' }}
                                            >
                                                {decodeHtml(row.title)}
                                            </a>
                                            <span className={`badge ${STATUS_BADGE[row.status] || 'badge-gray'} shrink-0`}>{row.status}</span>
                                        </div>
                                        <p className="text-xs text-text-3">{decodeHtml(row.company)}</p>
                                        <div className="grid grid-cols-4 gap-2 text-center">
                                            <div>
                                                <p className="text-xs text-text-3">Impr.</p>
                                                <p className="text-sm font-semibold">{row.impressions}</p>
                                            </div>
                                            <div>
                                                <p className="text-xs text-text-3">Clicks</p>
                                                <p className="text-sm font-semibold">{row.clicks}</p>
                                            </div>
                                            <div>
                                                <p className="text-xs text-text-3">Apps</p>
                                                <p className="text-sm font-semibold">{row.applications}</p>
                                            </div>
                                            <div>
                                                <p className="text-xs text-text-3">CTR</p>
                                                <p className="text-sm font-semibold">{row.ctr}%</p>
                                            </div>
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
