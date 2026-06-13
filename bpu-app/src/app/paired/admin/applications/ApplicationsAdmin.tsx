'use client';

import { useState, useEffect, useCallback } from 'react';

interface MentorApplication {
    user_id: number;
    display_name: string;
    email: string;
    avatar_url: string;
    registered: string;
    status: string;
    application: {
        job_title?: string;
        employer?: string;
        years_exp?: string;
        expertise?: string;
        mentorship_style?: string[];
        availability?: string;
        has_mentored?: string;
        linkedin_url?: string;
        motivation?: string;
        applied_at?: string;
    };
}

type StatusFilter = 'pending' | 'approved' | 'rejected';

export default function ApplicationsAdmin() {
    const [applications, setApplications] = useState<MentorApplication[]>([]);
    const [total, setTotal] = useState(0);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [statusFilter, setStatusFilter] = useState<StatusFilter>('pending');
    const [actionLoading, setActionLoading] = useState<number | null>(null);
    const [expanded, setExpanded] = useState<number | null>(null);
    const [rejectReason, setRejectReason] = useState('');

    const fetchApplications = useCallback(async (status: StatusFilter) => {
        setLoading(true);
        setError('');
        try {
            const res = await fetch(`/api/paired/mentor-admin?status=${status}`);
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load.');
            setApplications(data.applications || []);
            setTotal(data.total || 0);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load applications.');
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        fetchApplications(statusFilter);
    }, [statusFilter, fetchApplications]);

    async function handleAction(userId: number, action: 'approve' | 'reject') {
        setActionLoading(userId);
        try {
            const res = await fetch(`/api/paired/mentor-admin/${userId}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action, reason: action === 'reject' ? rejectReason : '' }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || `Failed to ${action}.`);
            setApplications(prev => prev.filter(a => a.user_id !== userId));
            setTotal(prev => prev - 1);
            setExpanded(null);
            setRejectReason('');
        } catch (e) {
            alert(e instanceof Error ? e.message : 'Action failed.');
        } finally {
            setActionLoading(null);
        }
    }

    const tabs: { key: StatusFilter; label: string }[] = [
        { key: 'pending', label: 'Pending' },
        { key: 'approved', label: 'Approved' },
        { key: 'rejected', label: 'Rejected' },
    ];

    return (
        <div className="fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px' }}>
            <div>
                <h1 className="text-3xl font-extrabold tracking-tight">Mentor Applications</h1>
                <p className="text-text-2 mt-2">Review and manage mentor applications for the PAIRED directory.</p>
            </div>

            {/* Tab filters */}
            <div className="flex gap-2">
                {tabs.map(tab => (
                    <button
                        key={tab.key}
                        onClick={() => { setStatusFilter(tab.key); setExpanded(null); }}
                        className={`btn btn-sm ${statusFilter === tab.key ? 'btn-purple' : 'btn-outline'}`}
                    >
                        {tab.label}
                        {tab.key === statusFilter && !loading ? ` (${total})` : ''}
                    </button>
                ))}
            </div>

            {/* Content */}
            {loading ? (
                <div className="text-center text-sm text-text-2 py-12">Loading applications...</div>
            ) : error ? (
                <div className="card card-p text-center text-err text-sm py-10">{error}</div>
            ) : applications.length === 0 ? (
                <div className="card card-p text-center py-10 space-y-2">
                    <p className="font-semibold text-text-2">No {statusFilter} applications</p>
                    <p className="text-sm text-text-3">
                        {statusFilter === 'pending'
                            ? 'All caught up! No applications waiting for review.'
                            : `No ${statusFilter} applications to show.`}
                    </p>
                </div>
            ) : (
                <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                    {applications.map(app => {
                        const isExpanded = expanded === app.user_id;
                        const isActioning = actionLoading === app.user_id;
                        const a = app.application;

                        return (
                            <div key={app.user_id} className="card card-p">
                                {/* Summary row */}
                                <div
                                    className="flex items-center gap-4 cursor-pointer"
                                    onClick={() => setExpanded(isExpanded ? null : app.user_id)}
                                >
                                    <div
                                        className="avatar avatar-md text-white shrink-0"
                                        style={{ background: '#7c3aed' }}
                                    >
                                        {app.display_name[0]}
                                    </div>
                                    <div className="flex-1 min-w-0">
                                        <p className="font-bold truncate">{app.display_name}</p>
                                        <p className="text-sm text-text-2 truncate">{app.email}</p>
                                        {a.job_title && (
                                            <p className="text-sm text-text-3">
                                                {a.job_title}{a.employer ? ` at ${a.employer}` : ''}
                                            </p>
                                        )}
                                    </div>
                                    <div className="text-right shrink-0">
                                        {a.applied_at && (
                                            <p className="text-xs text-text-3">
                                                {new Date(a.applied_at).toLocaleDateString('en-GB', {
                                                    day: 'numeric', month: 'short', year: 'numeric',
                                                })}
                                            </p>
                                        )}
                                        <span className={`badge mt-1 ${
                                            app.status === 'pending' ? 'badge-amber' :
                                            app.status === 'approved' ? 'badge-green' : 'badge-red'
                                        }`}>
                                            {app.status}
                                        </span>
                                    </div>
                                    <span className="text-text-3 text-lg">{isExpanded ? '▾' : '▸'}</span>
                                </div>

                                {/* Expanded details */}
                                {isExpanded && (
                                    <div className="mt-6 pt-6 border-t border-border">
                                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                                            <Field label="Job Title" value={a.job_title} />
                                            <Field label="Employer" value={a.employer} />
                                            <Field label="Years of Experience" value={a.years_exp} />
                                            <Field label="Area of Expertise" value={a.expertise} />
                                            <Field label="Availability" value={a.availability} />
                                            <Field label="Previous Mentoring" value={a.has_mentored} />
                                            {a.mentorship_style && a.mentorship_style.length > 0 && (
                                                <Field label="Mentorship Style" value={a.mentorship_style.join(', ')} />
                                            )}
                                            {a.linkedin_url && (
                                                <div>
                                                    <p className="text-text-3 font-medium mb-1">LinkedIn</p>
                                                    <a
                                                        href={a.linkedin_url}
                                                        target="_blank"
                                                        rel="noopener noreferrer"
                                                        className="text-purple-600 hover:underline break-all"
                                                    >
                                                        {a.linkedin_url}
                                                    </a>
                                                </div>
                                            )}
                                        </div>

                                        {a.motivation && (
                                            <div className="mt-4">
                                                <p className="text-text-3 font-medium text-sm mb-1">Motivation</p>
                                                <p className="text-sm bg-bg rounded-lg p-3 whitespace-pre-wrap">
                                                    {a.motivation}
                                                </p>
                                            </div>
                                        )}

                                        {/* Actions */}
                                        {statusFilter === 'pending' && (
                                            <div className="mt-6 pt-4 border-t border-border">
                                                <div className="flex flex-col sm:flex-row gap-3">
                                                    <button
                                                        onClick={() => handleAction(app.user_id, 'approve')}
                                                        disabled={isActioning}
                                                        className="btn btn-purple"
                                                    >
                                                        {isActioning ? 'Processing...' : 'Approve & Assign Mentor Role'}
                                                    </button>
                                                    <div className="flex-1 flex gap-2">
                                                        <input
                                                            type="text"
                                                            placeholder="Rejection reason (optional)"
                                                            value={rejectReason}
                                                            onChange={e => setRejectReason(e.target.value)}
                                                            className="field-input flex-1"
                                                            onClick={e => e.stopPropagation()}
                                                        />
                                                        <button
                                                            onClick={() => handleAction(app.user_id, 'reject')}
                                                            disabled={isActioning}
                                                            className="btn btn-outline"
                                                            style={{ color: 'var(--err)', borderColor: 'var(--err)' }}
                                                        >
                                                            Reject
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                )}
                            </div>
                        );
                    })}
                </div>
            )}
        </div>
    );
}

function Field({ label, value }: { label: string; value?: string }) {
    if (!value) return null;
    return (
        <div>
            <p className="text-text-3 font-medium mb-1">{label}</p>
            <p>{value}</p>
        </div>
    );
}
