'use client';

import { useState, useEffect } from 'react';

interface KYCSubmission {
    user_id: number;
    display_name: string;
    email: string;
    doc_front: string | null;
    doc_back: string | null;
    status: string;
    submitted_at: string | null;
}

export default function KYCAdmin() {
    const [submissions, setSubmissions] = useState<KYCSubmission[]>([]);
    const [loading, setLoading] = useState(true);
    const [tab, setTab] = useState<'pending' | 'approved' | 'rejected'>('pending');
    const [expanded, setExpanded] = useState<number | null>(null);
    const [rejectReason, setRejectReason] = useState('');
    const [actionLoading, setActionLoading] = useState(0);

    async function load(status: string) {
        setLoading(true);
        try {
            const res = await fetch(`/api/paired/admin/kyc?status=${status}`);
            const data = await res.json();
            setSubmissions(data.submissions || []);
        } catch {
            setSubmissions([]);
        } finally {
            setLoading(false);
        }
    }

    useEffect(() => { load(tab); }, [tab]);

    async function handleAction(userId: number, status: 'approved' | 'rejected') {
        setActionLoading(userId);
        try {
            await fetch(`/api/paired/admin/kyc/${userId}`, {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status, rejection_reason: status === 'rejected' ? rejectReason : '' }),
            });
            setSubmissions(prev => prev.filter(s => s.user_id !== userId));
            setExpanded(null);
            setRejectReason('');
        } catch { /* ignore */ } finally {
            setActionLoading(0);
        }
    }

    return (
        <div className="fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
            <div>
                <h1 className="text-2xl font-bold">KYC Reviews</h1>
                <p className="text-sm text-text-2 mt-1">Review mentor identity verification submissions.</p>
            </div>

            <div className="flex gap-2">
                {(['pending', 'approved', 'rejected'] as const).map(t => (
                    <button
                        key={t}
                        onClick={() => setTab(t)}
                        className={`btn btn-sm ${tab === t ? 'btn-purple' : 'btn-outline'} capitalize`}
                    >
                        {t}
                    </button>
                ))}
            </div>

            {loading ? (
                <div className="text-center text-sm text-text-2 py-12">Loading…</div>
            ) : submissions.length === 0 ? (
                <div className="card card-p text-center text-sm text-text-2 py-10">
                    No {tab} submissions.
                </div>
            ) : (
                <div className="space-y-4">
                    {submissions.map(s => (
                        <div key={s.user_id} className="card card-p space-y-3">
                            <div
                                className="flex items-center justify-between cursor-pointer"
                                onClick={() => setExpanded(expanded === s.user_id ? null : s.user_id)}
                            >
                                <div>
                                    <p className="font-bold">{s.display_name}</p>
                                    <p className="text-sm text-text-2">{s.email}</p>
                                    {s.submitted_at && (
                                        <p className="text-xs text-text-3">Submitted {new Date(s.submitted_at).toLocaleDateString('en-GB')}</p>
                                    )}
                                </div>
                                <span className={`badge ${s.status === 'pending' ? 'badge-amber' : s.status === 'approved' ? 'badge-green' : 'badge-purple'} capitalize`}>
                                    {s.status}
                                </span>
                            </div>

                            {expanded === s.user_id && (
                                <div className="space-y-4 pt-3 border-t border-border">
                                    <div className="flex gap-4 flex-wrap">
                                        {s.doc_front && (
                                            <div>
                                                <p className="text-xs text-text-3 mb-1">ID Front</p>
                                                <img
                                                    src={s.doc_front}
                                                    alt="ID Front"
                                                    style={{ maxWidth: 300, maxHeight: 200, borderRadius: 8, border: '1px solid var(--border)' }}
                                                />
                                            </div>
                                        )}
                                        {s.doc_back && (
                                            <div>
                                                <p className="text-xs text-text-3 mb-1">ID Back</p>
                                                <img
                                                    src={s.doc_back}
                                                    alt="ID Back"
                                                    style={{ maxWidth: 300, maxHeight: 200, borderRadius: 8, border: '1px solid var(--border)' }}
                                                />
                                            </div>
                                        )}
                                    </div>

                                    {tab === 'pending' && (
                                        <div className="flex flex-col gap-3">
                                            <div className="flex gap-2">
                                                <button
                                                    onClick={() => handleAction(s.user_id, 'approved')}
                                                    disabled={actionLoading === s.user_id}
                                                    className="btn btn-purple btn-sm"
                                                >
                                                    Approve
                                                </button>
                                                <button
                                                    onClick={() => {
                                                        if (!rejectReason) {
                                                            const r = prompt('Enter rejection reason:');
                                                            if (r) {
                                                                setRejectReason(r);
                                                                handleAction(s.user_id, 'rejected');
                                                            }
                                                        } else {
                                                            handleAction(s.user_id, 'rejected');
                                                        }
                                                    }}
                                                    disabled={actionLoading === s.user_id}
                                                    className="btn btn-outline btn-sm"
                                                    style={{ color: 'var(--err, #ef4444)', borderColor: 'var(--err, #ef4444)' }}
                                                >
                                                    Reject
                                                </button>
                                            </div>
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
