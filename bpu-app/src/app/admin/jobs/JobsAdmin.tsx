'use client';

import { useState, useEffect, useCallback } from 'react';
import { decodeHtml } from '@/lib/utils';

interface Job {
    id: number;
    title: string;
    company: string;
    type: 'inbound' | 'outbound';
    status: string;
    impressions: number;
    clicks: number;
    applications: number;
    date: string;
}

interface JobsResponse {
    jobs: Job[];
    total: number;
    counts?: {
        all?: number;
        pending?: number;
        publish?: number;
        draft?: number;
        trash?: number;
    };
}

type StatusFilter = 'all' | 'pending' | 'publish' | 'draft' | 'trash';

const STATUS_TABS: { value: StatusFilter; label: string }[] = [
    { value: 'all', label: 'All' },
    { value: 'pending', label: 'Pending Review' },
    { value: 'publish', label: 'Published' },
    { value: 'draft', label: 'Draft' },
    { value: 'trash', label: 'Trash' },
];

const VALID_STATUSES = ['publish', 'pending', 'draft', 'trash'];

const STATUS_BADGE: Record<string, { className: string; label: string }> = {
    pending: { className: 'badge-amber', label: 'Pending Review' },
    publish: { className: 'badge-green', label: 'Published' },
    draft: { className: 'badge-gray', label: 'Draft' },
    trash: { className: 'badge-red', label: 'Trashed' },
};

const TYPE_BADGE: Record<string, string> = {
    inbound: 'badge-purple',
    outbound: 'badge-blue',
};

export default function JobsAdmin() {
    const [jobs, setJobs] = useState<Job[]>([]);
    const [total, setTotal] = useState(0);
    const [counts, setCounts] = useState<JobsResponse['counts']>({});
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [statusFilter, setStatusFilter] = useState<StatusFilter>('all');
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [page, setPage] = useState(1);
    const [updatingId, setUpdatingId] = useState<number | null>(null);
    const [flashId, setFlashId] = useState<number | null>(null);
    const perPage = 20;

    const fetchJobs = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const params = new URLSearchParams({ page: String(page), per_page: String(perPage) });
            if (statusFilter !== 'all') params.set('status', statusFilter);
            if (search.trim()) params.set('search', search.trim());

            const res = await fetch(`/api/admin/jobs?${params}`);
            const data: JobsResponse = await res.json();
            if (!res.ok) throw new Error((data as unknown as { error: string }).error || 'Failed to load jobs.');
            setJobs(data.jobs || []);
            setTotal(data.total || 0);
            setCounts(data.counts || {});
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load jobs.');
        } finally {
            setLoading(false);
        }
    }, [page, statusFilter, search]);

    useEffect(() => {
        fetchJobs();
    }, [fetchJobs]);

    function handleSearch(e: React.FormEvent) {
        e.preventDefault();
        setSearch(searchInput);
        setPage(1);
    }

    function handleTabChange(val: StatusFilter) {
        setStatusFilter(val);
        setPage(1);
    }

    async function updateStatus(jobId: number, newStatus: string) {
        setUpdatingId(jobId);
        try {
            const res = await fetch(`/api/admin/jobs/${jobId}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newStatus }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to update status.');
            setJobs(prev => prev.map(j => j.id === jobId ? { ...j, status: newStatus } : j));
            setFlashId(jobId);
            setTimeout(() => setFlashId(null), 1500);
        } catch (e) {
            alert(e instanceof Error ? e.message : 'Action failed.');
        } finally {
            setUpdatingId(null);
        }
    }

    const totalPages = Math.ceil(total / perPage);

    const badgeFor = (status: string) => {
        const info = STATUS_BADGE[status] || { className: 'badge-amber', label: status };
        return <span className={`badge ${info.className}`}>{info.label}</span>;
    };

    return (
        <div className="space-y-5">
            {/* Summary Cards */}
            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div className="card card-p text-center">
                    <p className="text-xs text-text-3 mb-1">Total Jobs</p>
                    <p className="text-2xl font-bold">{counts?.all ?? total}</p>
                </div>
                <div className="card card-p text-center">
                    <p className="text-xs text-text-3 mb-1">Pending Review</p>
                    <p className="text-2xl font-bold" style={{ color: 'var(--amber)' }}>{counts?.pending ?? 0}</p>
                </div>
                <div className="card card-p text-center">
                    <p className="text-xs text-text-3 mb-1">Published</p>
                    <p className="text-2xl font-bold" style={{ color: 'var(--ok)' }}>{counts?.publish ?? 0}</p>
                </div>
            </div>

            {/* Create Job button */}
            <div>
                <a href="/admin/jobs/new" className="btn btn-purple btn-sm">Create Job +</a>
            </div>

            {/* Tabs */}
            <div style={{ display: 'flex', gap: 4, background: 'var(--surface)', padding: 4, borderRadius: 10, width: 'fit-content', flexWrap: 'wrap' }}>
                {STATUS_TABS.map(tab => (
                    <button
                        key={tab.value}
                        onClick={() => handleTabChange(tab.value)}
                        className={statusFilter === tab.value ? 'btn btn-purple btn-sm' : 'btn btn-ghost btn-sm'}
                        style={{ fontSize: '0.8rem' }}
                    >
                        {tab.label}
                        {counts && counts[tab.value as keyof typeof counts] !== undefined && (
                            <span className="ml-1 text-xs opacity-70">({counts[tab.value as keyof typeof counts]})</span>
                        )}
                    </button>
                ))}
            </div>

            {/* Search */}
            <form onSubmit={handleSearch} className="flex gap-2">
                <input
                    type="text"
                    className="field-input flex-1"
                    placeholder="Search by job title or company..."
                    value={searchInput}
                    onChange={e => setSearchInput(e.target.value)}
                />
                <button type="submit" className="btn btn-purple btn-sm">Search</button>
                {search && (
                    <button type="button" className="btn btn-ghost btn-sm" onClick={() => { setSearch(''); setSearchInput(''); setPage(1); }}>
                        Clear
                    </button>
                )}
            </form>

            {loading ? (
                <div className="text-center text-sm text-text-2 py-12">Loading jobs...</div>
            ) : error ? (
                <div className="card card-p text-center py-10" style={{ color: 'var(--err)' }}>{error}</div>
            ) : jobs.length === 0 ? (
                <div className="card card-p text-center py-10">
                    <p className="font-semibold text-text-2">No jobs found</p>
                    <p className="text-sm text-text-3 mt-1">Try adjusting your filters.</p>
                </div>
            ) : (
                <>
                    <p className="text-sm text-text-3">{total} job{total !== 1 ? 's' : ''} found</p>

                    {/* Desktop Table */}
                    <div className="card hidden md:block" style={{ overflow: 'hidden' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid var(--border)', background: 'var(--surface)' }}>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Title</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Company</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Type</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Status</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Impressions</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Clicks</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Applications</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Posted</th>
                                    <th className="text-right text-xs font-semibold text-text-3 p-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {jobs.map(j => (
                                    <tr key={j.id} style={{ borderBottom: '1px solid var(--border)' }}>
                                        <td className="p-3">
                                            <a
                                                href={`/jobs/${j.id}`}
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                className="text-sm font-semibold hover:underline"
                                                style={{ color: 'var(--link)' }}
                                            >
                                                {decodeHtml(j.title)}
                                            </a>
                                        </td>
                                        <td className="p-3 text-sm text-text-2">{decodeHtml(j.company || '—')}</td>
                                        <td className="p-3 text-center">
                                            <span className={`badge ${TYPE_BADGE[j.type] || 'badge-gray'}`}>{j.type || '—'}</span>
                                        </td>
                                        <td className="p-3 text-center">
                                            {badgeFor(j.status)}
                                            {flashId === j.id && (
                                                <span className="ml-1 text-xs text-green-500 font-semibold">Updated!</span>
                                            )}
                                        </td>
                                        <td className="p-3 text-center text-sm text-text-2">{j.impressions ?? 0}</td>
                                        <td className="p-3 text-center text-sm text-text-2">{j.clicks ?? 0}</td>
                                        <td className="p-3 text-center text-sm text-text-2">{j.applications ?? 0}</td>
                                        <td className="p-3 text-sm text-text-3">{j.date}</td>
                                        <td className="p-3 text-right">
                                            <div className="flex items-center justify-end gap-2">
                                                <a
                                                    href={`/admin/jobs/${j.id}/edit`}
                                                    className="btn btn-ghost btn-sm"
                                                    style={{ fontSize: '0.75rem' }}
                                                >
                                                    Edit
                                                </a>
                                                <select
                                                    className="field-input"
                                                    style={{ fontSize: '0.75rem', padding: '4px 8px', height: 'auto', display: 'inline-block', width: 'auto' }}
                                                    value={j.status}
                                                    disabled={updatingId === j.id}
                                                    onChange={e => updateStatus(j.id, e.target.value)}
                                                >
                                                    {VALID_STATUSES.map(s => {
                                                        const info = STATUS_BADGE[s];
                                                        return <option key={s} value={s}>{info?.label || s}</option>;
                                                    })}
                                                </select>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Mobile Cards */}
                    <div className="md:hidden space-y-3">
                        {jobs.map(j => (
                            <div key={j.id} className="card card-p space-y-3">
                                <div className="flex items-start justify-between gap-2">
                                    <a
                                        href={`/jobs/${j.id}`}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="text-sm font-semibold hover:underline"
                                        style={{ color: 'var(--link)' }}
                                    >
                                        {decodeHtml(j.title)}
                                    </a>
                                    {badgeFor(j.status)}
                                </div>
                                <p className="text-xs text-text-3">{decodeHtml(j.company || '—')}</p>
                                <div className="flex items-center gap-2 flex-wrap">
                                    <span className={`badge ${TYPE_BADGE[j.type] || 'badge-gray'}`}>{j.type || '—'}</span>
                                    <span className="text-xs text-text-3">Posted {j.date}</span>
                                </div>
                                <div className="grid grid-cols-3 gap-2 text-center">
                                    <div>
                                        <p className="text-xs text-text-3">Impressions</p>
                                        <p className="text-sm font-semibold">{j.impressions ?? 0}</p>
                                    </div>
                                    <div>
                                        <p className="text-xs text-text-3">Clicks</p>
                                        <p className="text-sm font-semibold">{j.clicks ?? 0}</p>
                                    </div>
                                    <div>
                                        <p className="text-xs text-text-3">Applications</p>
                                        <p className="text-sm font-semibold">{j.applications ?? 0}</p>
                                    </div>
                                </div>
                                <div className="flex items-center justify-between gap-2">
                                    <div className="flex items-center gap-2">
                                        <a
                                            href={`/admin/jobs/${j.id}/edit`}
                                            className="btn btn-ghost btn-sm"
                                            style={{ fontSize: '0.75rem' }}
                                        >
                                            Edit
                                        </a>
                                        {flashId === j.id && (
                                            <span className="text-xs text-green-500 font-semibold">Updated!</span>
                                        )}
                                    </div>
                                    <select
                                        className="field-input ml-auto"
                                        style={{ fontSize: '0.75rem', padding: '4px 8px', height: 'auto', display: 'inline-block', width: 'auto' }}
                                        value={j.status}
                                        disabled={updatingId === j.id}
                                        onChange={e => updateStatus(j.id, e.target.value)}
                                    >
                                        {VALID_STATUSES.map(s => {
                                            const info = STATUS_BADGE[s];
                                            return <option key={s} value={s}>{info?.label || s}</option>;
                                        })}
                                    </select>
                                </div>
                            </div>
                        ))}
                    </div>

                    {/* Pagination */}
                    {totalPages > 1 && (
                        <div className="flex items-center justify-center gap-2 pt-2">
                            <button onClick={() => setPage(p => Math.max(1, p - 1))} disabled={page === 1} className="btn btn-outline btn-sm">Previous</button>
                            <span className="text-sm text-text-2">Page {page} of {totalPages}</span>
                            <button onClick={() => setPage(p => Math.min(totalPages, p + 1))} disabled={page === totalPages} className="btn btn-outline btn-sm">Next</button>
                        </div>
                    )}
                </>
            )}
        </div>
    );
}
