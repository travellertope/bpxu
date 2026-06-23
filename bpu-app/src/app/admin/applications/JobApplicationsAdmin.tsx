'use client';

import { useState, useEffect, useCallback } from 'react';
import { decodeHtml } from '@/lib/utils';

interface Application {
    id: number;
    applicant_name: string;
    applicant_email: string;
    applicant_phone: string;
    cv_url: string;
    cover_letter: string;
    screening_answers: { question: string; answer: string }[];
    status: string;
    applied_at: string;
    job_id: number;
    job_title: string;
    job_company: string;
}

interface ApplicationsResponse {
    applications: Application[];
    total: number;
    counts?: {
        all?: number;
        pending?: number;
        reviewed?: number;
        shortlisted?: number;
        rejected?: number;
    };
}

type StatusFilter = 'all' | 'pending' | 'reviewed' | 'shortlisted' | 'rejected';

const STATUS_TABS: { value: StatusFilter; label: string }[] = [
    { value: 'all', label: 'All' },
    { value: 'pending', label: 'Pending' },
    { value: 'reviewed', label: 'Reviewed' },
    { value: 'shortlisted', label: 'Shortlisted' },
    { value: 'rejected', label: 'Rejected' },
];

const VALID_STATUSES = ['pending', 'reviewed', 'shortlisted', 'rejected'];

const STATUS_BADGE: Record<string, { className: string; label: string }> = {
    pending:     { className: 'badge-amber',  label: 'Pending' },
    reviewed:    { className: 'badge-blue',   label: 'Reviewed' },
    shortlisted: { className: 'badge-green',  label: 'Shortlisted' },
    rejected:    { className: 'badge-red',    label: 'Rejected' },
};

export default function JobApplicationsAdmin() {
    const [applications, setApplications] = useState<Application[]>([]);
    const [total, setTotal] = useState(0);
    const [counts, setCounts] = useState<ApplicationsResponse['counts']>({});
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [statusFilter, setStatusFilter] = useState<StatusFilter>('all');
    const [search, setSearch] = useState('');
    const [searchInput, setSearchInput] = useState('');
    const [page, setPage] = useState(1);
    const [updatingId, setUpdatingId] = useState<number | null>(null);
    const [flashId, setFlashId] = useState<number | null>(null);
    const [expandedId, setExpandedId] = useState<number | null>(null);
    const perPage = 20;

    const fetchApplications = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const params = new URLSearchParams({ page: String(page), per_page: String(perPage) });
            if (statusFilter !== 'all') params.set('status', statusFilter);
            if (search.trim()) params.set('search', search.trim());

            const res = await fetch(`/api/paired/admin/applications?${params}`);
            const data: ApplicationsResponse = await res.json();
            if (!res.ok) throw new Error((data as unknown as { error: string }).error || 'Failed to load applications.');
            setApplications(data.applications || []);
            setTotal(data.total || 0);
            setCounts(data.counts || {});
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load applications.');
        } finally {
            setLoading(false);
        }
    }, [page, statusFilter, search]);

    useEffect(() => {
        fetchApplications();
    }, [fetchApplications]);

    function handleSearch(e: React.FormEvent) {
        e.preventDefault();
        setSearch(searchInput);
        setPage(1);
    }

    function handleTabChange(val: StatusFilter) {
        setStatusFilter(val);
        setPage(1);
        setExpandedId(null);
    }

    async function updateStatus(appId: number, newStatus: string) {
        setUpdatingId(appId);
        try {
            const res = await fetch(`/api/paired/admin/applications/${appId}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newStatus }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to update status.');
            setApplications(prev => prev.map(a => a.id === appId ? { ...a, status: newStatus } : a));
            setFlashId(appId);
            setTimeout(() => setFlashId(null), 1500);
        } catch (e) {
            alert(e instanceof Error ? e.message : 'Action failed.');
        } finally {
            setUpdatingId(null);
        }
    }

    function toggleExpand(id: number) {
        setExpandedId(prev => prev === id ? null : id);
    }

    function formatDate(dateStr: string) {
        if (!dateStr) return '—';
        const d = new Date(dateStr);
        return d.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
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
                    <p className="text-xs text-text-3 mb-1">Total Applications</p>
                    <p className="text-2xl font-bold">{counts?.all ?? total}</p>
                </div>
                <div className="card card-p text-center">
                    <p className="text-xs text-text-3 mb-1">Pending</p>
                    <p className="text-2xl font-bold" style={{ color: 'var(--amber)' }}>{counts?.pending ?? 0}</p>
                </div>
                <div className="card card-p text-center">
                    <p className="text-xs text-text-3 mb-1">Shortlisted</p>
                    <p className="text-2xl font-bold" style={{ color: 'var(--ok)' }}>{counts?.shortlisted ?? 0}</p>
                </div>
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
                    placeholder="Search by applicant name or email..."
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
                <div className="text-center text-sm text-text-2 py-12">Loading applications...</div>
            ) : error ? (
                <div className="card card-p text-center py-10" style={{ color: 'var(--err)' }}>{error}</div>
            ) : applications.length === 0 ? (
                <div className="card card-p text-center py-10">
                    <p className="font-semibold text-text-2">No applications found</p>
                    <p className="text-sm text-text-3 mt-1">Try adjusting your filters.</p>
                </div>
            ) : (
                <>
                    <p className="text-sm text-text-3">{total} application{total !== 1 ? 's' : ''} found</p>

                    {/* Desktop Table */}
                    <div className="card hidden md:block" style={{ overflow: 'hidden' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid var(--border)', background: 'var(--surface)' }}>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Applicant</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Email</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Job</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Status</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Applied</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">CV</th>
                                    <th className="text-right text-xs font-semibold text-text-3 p-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {applications.map(app => (
                                    <DesktopRow
                                        key={app.id}
                                        app={app}
                                        expanded={expandedId === app.id}
                                        onToggle={() => toggleExpand(app.id)}
                                        onUpdateStatus={updateStatus}
                                        updatingId={updatingId}
                                        flashId={flashId}
                                        badgeFor={badgeFor}
                                        formatDate={formatDate}
                                    />
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Mobile Cards */}
                    <div className="md:hidden space-y-3">
                        {applications.map(app => (
                            <MobileCard
                                key={app.id}
                                app={app}
                                expanded={expandedId === app.id}
                                onToggle={() => toggleExpand(app.id)}
                                onUpdateStatus={updateStatus}
                                updatingId={updatingId}
                                flashId={flashId}
                                badgeFor={badgeFor}
                                formatDate={formatDate}
                            />
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

/* ---------- Desktop row with expandable details ---------- */

function DesktopRow({
    app, expanded, onToggle, onUpdateStatus, updatingId, flashId, badgeFor, formatDate,
}: {
    app: Application;
    expanded: boolean;
    onToggle: () => void;
    onUpdateStatus: (id: number, status: string) => void;
    updatingId: number | null;
    flashId: number | null;
    badgeFor: (s: string) => React.ReactNode;
    formatDate: (d: string) => string;
}) {
    return (
        <>
            <tr
                style={{ borderBottom: '1px solid var(--border)', cursor: 'pointer' }}
                onClick={onToggle}
            >
                <td className="p-3">
                    <p className="text-sm font-semibold">{app.applicant_name}</p>
                </td>
                <td className="p-3 text-sm text-text-2">{app.applicant_email}</td>
                <td className="p-3">
                    <a
                        href={`/jobs/${app.job_id}`}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-sm hover:underline"
                        style={{ color: 'var(--link)' }}
                        onClick={e => e.stopPropagation()}
                    >
                        {decodeHtml(app.job_title)}
                    </a>
                    {app.job_company && (
                        <p className="text-xs text-text-3">{decodeHtml(app.job_company)}</p>
                    )}
                </td>
                <td className="p-3 text-center">
                    {badgeFor(app.status)}
                    {flashId === app.id && (
                        <span className="ml-1 text-xs text-green-500 font-semibold">Updated!</span>
                    )}
                </td>
                <td className="p-3 text-sm text-text-3">{formatDate(app.applied_at)}</td>
                <td className="p-3 text-center">
                    {app.cv_url ? (
                        <a
                            href={app.cv_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="text-xs font-medium hover:underline"
                            style={{ color: 'var(--link)' }}
                            onClick={e => e.stopPropagation()}
                        >
                            Download CV
                        </a>
                    ) : (
                        <span className="text-xs text-text-3">—</span>
                    )}
                </td>
                <td className="p-3 text-right" onClick={e => e.stopPropagation()}>
                    <select
                        className="field-input"
                        style={{ fontSize: '0.75rem', padding: '4px 8px', height: 'auto', display: 'inline-block', width: 'auto' }}
                        value={app.status}
                        disabled={updatingId === app.id}
                        onChange={e => onUpdateStatus(app.id, e.target.value)}
                    >
                        {VALID_STATUSES.map(s => {
                            const info = STATUS_BADGE[s];
                            return <option key={s} value={s}>{info?.label || s}</option>;
                        })}
                    </select>
                </td>
            </tr>
            {expanded && (
                <tr style={{ background: 'var(--surface)' }}>
                    <td colSpan={7} className="p-4">
                        <ExpandedDetails app={app} />
                    </td>
                </tr>
            )}
        </>
    );
}

/* ---------- Mobile card with expandable details ---------- */

function MobileCard({
    app, expanded, onToggle, onUpdateStatus, updatingId, flashId, badgeFor, formatDate,
}: {
    app: Application;
    expanded: boolean;
    onToggle: () => void;
    onUpdateStatus: (id: number, status: string) => void;
    updatingId: number | null;
    flashId: number | null;
    badgeFor: (s: string) => React.ReactNode;
    formatDate: (d: string) => string;
}) {
    return (
        <div className="card card-p space-y-3">
            <div className="cursor-pointer" onClick={onToggle}>
                <div className="flex items-start justify-between gap-2">
                    <div className="min-w-0">
                        <p className="text-sm font-semibold truncate">{app.applicant_name}</p>
                        <p className="text-xs text-text-2 truncate">{app.applicant_email}</p>
                    </div>
                    <div className="flex items-center gap-2 shrink-0">
                        {badgeFor(app.status)}
                        <span className="text-text-3 text-lg">{expanded ? '▾' : '▸'}</span>
                    </div>
                </div>
                <div className="flex items-center gap-2 mt-2 flex-wrap">
                    <a
                        href={`/jobs/${app.job_id}`}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="text-xs hover:underline"
                        style={{ color: 'var(--link)' }}
                        onClick={e => e.stopPropagation()}
                    >
                        {decodeHtml(app.job_title)}
                    </a>
                    {app.job_company && (
                        <span className="text-xs text-text-3">at {decodeHtml(app.job_company)}</span>
                    )}
                </div>
                <div className="flex items-center gap-3 mt-2 text-xs text-text-3">
                    <span>Applied {formatDate(app.applied_at)}</span>
                    {app.cv_url && (
                        <a
                            href={app.cv_url}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="font-medium hover:underline"
                            style={{ color: 'var(--link)' }}
                            onClick={e => e.stopPropagation()}
                        >
                            Download CV
                        </a>
                    )}
                </div>
            </div>

            {expanded && (
                <div className="pt-3 border-t border-border">
                    <ExpandedDetails app={app} />
                </div>
            )}

            <div className="flex items-center justify-between pt-1">
                {flashId === app.id && (
                    <span className="text-xs text-green-500 font-semibold">Updated!</span>
                )}
                <select
                    className="field-input ml-auto"
                    style={{ fontSize: '0.75rem', padding: '4px 8px', height: 'auto', display: 'inline-block', width: 'auto' }}
                    value={app.status}
                    disabled={updatingId === app.id}
                    onChange={e => onUpdateStatus(app.id, e.target.value)}
                >
                    {VALID_STATUSES.map(s => {
                        const info = STATUS_BADGE[s];
                        return <option key={s} value={s}>{info?.label || s}</option>;
                    })}
                </select>
            </div>
        </div>
    );
}

/* ---------- Shared expanded details ---------- */

function ExpandedDetails({ app }: { app: Application }) {
    return (
        <div className="space-y-4">
            {app.applicant_phone && (
                <div>
                    <p className="text-xs text-text-3 font-medium mb-1">Phone</p>
                    <p className="text-sm">{app.applicant_phone}</p>
                </div>
            )}

            {app.cover_letter && (
                <div>
                    <p className="text-xs text-text-3 font-medium mb-1">Cover Letter</p>
                    <p className="text-sm bg-bg rounded-lg p-3 whitespace-pre-wrap">{app.cover_letter}</p>
                </div>
            )}

            {app.screening_answers && app.screening_answers.length > 0 && (
                <div>
                    <p className="text-xs text-text-3 font-medium mb-2">Screening Answers</p>
                    <div className="space-y-3">
                        {app.screening_answers.map((qa, i) => (
                            <div key={i} className="bg-bg rounded-lg p-3">
                                <p className="text-xs font-semibold text-text-2 mb-1">{qa.question}</p>
                                <p className="text-sm">{qa.answer}</p>
                            </div>
                        ))}
                    </div>
                </div>
            )}

            {!app.cover_letter && (!app.screening_answers || app.screening_answers.length === 0) && !app.applicant_phone && (
                <p className="text-sm text-text-3">No additional details provided.</p>
            )}
        </div>
    );
}
