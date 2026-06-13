'use client';

import { useState, useEffect, useRef } from 'react';

type KYCStatus = 'not_submitted' | 'pending' | 'approved' | 'rejected';

interface KYCData {
    status: KYCStatus;
    submitted_at?: string;
    rejection_reason?: string;
}

export default function KYCForm() {
    const [data, setData] = useState<KYCData | null>(null);
    const [loading, setLoading] = useState(true);
    const [submitting, setSubmitting] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');
    const [idFront, setIdFront] = useState<File | null>(null);
    const [idBack, setIdBack] = useState<File | null>(null);
    const [frontPreview, setFrontPreview] = useState<string | null>(null);
    const [backPreview, setBackPreview] = useState<string | null>(null);
    const frontRef = useRef<HTMLInputElement>(null);
    const backRef = useRef<HTMLInputElement>(null);

    useEffect(() => {
        async function load() {
            try {
                const res = await fetch('/api/paired/mentor/kyc/status');
                const json = await res.json();
                if (!res.ok) throw new Error(json.error || 'Failed to load KYC status.');
                setData(json);
            } catch (e) {
                setError(e instanceof Error ? e.message : 'Failed to load KYC status.');
            } finally {
                setLoading(false);
            }
        }
        load();
    }, []);

    function handleFileChange(file: File | null, side: 'front' | 'back') {
        if (!file) return;

        if (file.size > 10 * 1024 * 1024) {
            setError('File must be under 10MB.');
            return;
        }

        const validTypes = ['image/jpeg', 'image/png', 'image/webp', 'application/pdf'];
        if (!validTypes.includes(file.type)) {
            setError('File must be JPEG, PNG, WebP, or PDF.');
            return;
        }

        setError('');

        if (side === 'front') {
            setIdFront(file);
            if (file.type.startsWith('image/')) {
                const url = URL.createObjectURL(file);
                setFrontPreview(url);
            } else {
                setFrontPreview(null);
            }
        } else {
            setIdBack(file);
            if (file.type.startsWith('image/')) {
                const url = URL.createObjectURL(file);
                setBackPreview(url);
            } else {
                setBackPreview(null);
            }
        }
    }

    async function handleSubmit() {
        if (!idFront) {
            setError('Please select an ID front image.');
            return;
        }

        setSubmitting(true);
        setError('');
        setSuccess('');

        try {
            const formData = new FormData();
            formData.append('id_front', idFront);
            if (idBack) {
                formData.append('id_back', idBack);
            }

            const res = await fetch('/api/paired/mentor/kyc/submit', {
                method: 'POST',
                body: formData,
            });
            const json = await res.json();
            if (!res.ok) throw new Error(json.error || 'Failed to submit KYC.');
            setSuccess('Your documents have been submitted for review.');
            setData({ status: 'pending', submitted_at: new Date().toISOString() });
            setIdFront(null);
            setIdBack(null);
            setFrontPreview(null);
            setBackPreview(null);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to submit documents.');
        } finally {
            setSubmitting(false);
        }
    }

    function handleResubmit() {
        setData({ status: 'not_submitted' });
        setError('');
        setSuccess('');
    }

    if (loading) {
        return (
            <div className="wrap py-12 fade-up">
                <div className="text-center text-sm py-12" style={{ color: 'var(--text-2)' }}>
                    Loading KYC status...
                </div>
            </div>
        );
    }

    const status = data?.status || 'not_submitted';

    return (
        <div className="wrap py-12 fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px', maxWidth: '640px' }}>
            <div>
                <h1 className="text-3xl font-extrabold tracking-tight">Identity Verification</h1>
                <p className="mt-2" style={{ color: 'var(--text-2)' }}>
                    Verify your identity to receive payouts and build trust with mentees.
                </p>
            </div>

            {error && <div className="alert alert-red">{error}</div>}
            {success && <div className="alert alert-green">{success}</div>}

            {/* Status display */}
            {status === 'pending' && (
                <div className="card card-p" style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                        <h2 className="text-lg font-bold">Verification Status</h2>
                        <span className="badge badge-amber">Under review</span>
                    </div>
                    <p className="text-sm" style={{ color: 'var(--text-2)' }}>
                        Your documents are being reviewed. This usually takes 1-2 business days.
                    </p>
                    {data?.submitted_at && (
                        <p className="text-xs" style={{ color: 'var(--text-3)' }}>
                            Submitted on{' '}
                            {new Date(data.submitted_at).toLocaleDateString('en-GB', {
                                day: 'numeric', month: 'short', year: 'numeric',
                            })}
                        </p>
                    )}
                </div>
            )}

            {status === 'approved' && (
                <div className="card card-p" style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                    <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                        <h2 className="text-lg font-bold">Verification Status</h2>
                        <span className="badge badge-green">Verified</span>
                    </div>
                    <div className="flex items-center gap-3">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#22c55e" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                        <span className="font-medium">Your identity has been verified.</span>
                    </div>
                </div>
            )}

            {status === 'rejected' && (
                <div style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                    <div className="alert alert-red">
                        <p className="font-medium">Verification Rejected</p>
                        {data?.rejection_reason && (
                            <p className="text-sm mt-1">Reason: {data.rejection_reason}</p>
                        )}
                    </div>
                    <button onClick={handleResubmit} className="btn btn-purple">
                        Resubmit Documents
                    </button>
                </div>
            )}

            {/* Upload form */}
            {status === 'not_submitted' && (
                <div className="card card-p" style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
                    <h2 className="text-lg font-bold">Upload Identity Document</h2>
                    <p className="text-sm" style={{ color: 'var(--text-2)' }}>
                        Upload a clear photo or scan of a government-issued ID (passport, driving licence, or national ID card).
                        Accepted formats: JPEG, PNG, WebP, PDF. Maximum 10MB per file.
                    </p>

                    {/* ID Front */}
                    <div>
                        <label className="field-label">ID Front (required)</label>
                        <input
                            ref={frontRef}
                            type="file"
                            accept=".jpg,.jpeg,.png,.webp,.pdf"
                            onChange={e => handleFileChange(e.target.files?.[0] || null, 'front')}
                            className="field-input mt-1"
                            style={{ padding: '8px' }}
                        />
                        {frontPreview && (
                            <div className="mt-3">
                                <img
                                    src={frontPreview}
                                    alt="ID front preview"
                                    style={{
                                        maxWidth: '240px',
                                        maxHeight: '160px',
                                        borderRadius: '8px',
                                        border: '1px solid var(--border)',
                                        objectFit: 'contain',
                                    }}
                                />
                            </div>
                        )}
                    </div>

                    {/* ID Back */}
                    <div>
                        <label className="field-label">ID Back (optional)</label>
                        <input
                            ref={backRef}
                            type="file"
                            accept=".jpg,.jpeg,.png,.webp,.pdf"
                            onChange={e => handleFileChange(e.target.files?.[0] || null, 'back')}
                            className="field-input mt-1"
                            style={{ padding: '8px' }}
                        />
                        {backPreview && (
                            <div className="mt-3">
                                <img
                                    src={backPreview}
                                    alt="ID back preview"
                                    style={{
                                        maxWidth: '240px',
                                        maxHeight: '160px',
                                        borderRadius: '8px',
                                        border: '1px solid var(--border)',
                                        objectFit: 'contain',
                                    }}
                                />
                            </div>
                        )}
                    </div>

                    <button
                        onClick={handleSubmit}
                        disabled={submitting || !idFront}
                        className="btn btn-purple"
                    >
                        {submitting ? 'Submitting...' : 'Submit for Verification'}
                    </button>
                </div>
            )}
        </div>
    );
}
