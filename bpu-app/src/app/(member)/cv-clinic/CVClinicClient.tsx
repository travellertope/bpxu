'use client';

import React, { useState, useRef } from 'react';
import { useRouter } from 'next/navigation';
import { BPUUser } from '@/lib/auth';
import { CVReview, BPUApi } from '@/lib/api';
import InterviewPrepTab from './InterviewPrepTab';

// Keyframe for honest (non-misleading) indeterminate progress.
// Advances slowly to ~85 % over 90 s and never reaches 100 until done.
const PROGRESS_STYLE = `
@keyframes bpuClinicLoad {
  0%   { width: 2% }
  10%  { width: 18% }
  25%  { width: 35% }
  50%  { width: 55% }
  75%  { width: 70% }
  100% { width: 85% }
}
`;

function ProGate({ children, isPro, feature }: { children: React.ReactNode; isPro: boolean; feature: string }) {
    if (isPro) return <>{children}</>;
    return (
        <div style={{ position: 'relative', minHeight: '220px' }}>
            <div style={{ filter: 'blur(4px)', pointerEvents: 'none', userSelect: 'none', opacity: 0.5 }} aria-hidden="true">
                {children}
            </div>
            <div
                className="absolute inset-0 flex flex-col items-center justify-center gap-3"
                style={{ background: 'rgba(0,0,0,0.75)', borderRadius: 'var(--radius)', padding: '2rem', color: '#ffffff' }}
            >
                <p style={{ fontSize: '2rem', lineHeight: 1, color: '#f59e0b' }}>★</p>
                <p style={{ fontWeight: 700, fontSize: '1rem', textAlign: 'center', color: '#ffffff' }}>{feature}</p>
                <p style={{ fontSize: '0.875rem', textAlign: 'center', color: 'rgba(255,255,255,0.75)' }}>Requires BPU Pro membership</p>
                <a href="/upgrade" className="btn btn-amber btn-sm">Upgrade to Pro →</a>
            </div>
        </div>
    );
}

interface Props {
    user: BPUUser;
    reviews: CVReview[];
}

export default function CVClinicClient({ user, reviews }: Props) {
    const router = useRouter();
    const isPro = user.is_pro;

    // Sub-tab state
    const [cvTab, setCvTab] = useState<'analyse' | 'upload' | 'review' | 'prep'>('analyse');

    // CV URL — updated after a successful upload
    const [cvUrl, setCvUrl] = useState(user.cv_url || '');

    // ── AI Analysis state ──
    const [analyzeRole, setAnalyzeRole] = useState('');
    const [analyzeJD, setAnalyzeJD] = useState('');
    const [analyzeCvFile, setAnalyzeCvFile] = useState<File | null>(null);
    const [analyzing, setAnalyzing] = useState(false);
    const [analyzeMsg, setAnalyzeMsg] = useState<{ type: 'ok' | 'err'; text: string } | null>(null);
    const [analyzeResult, setAnalyzeResult] = useState<{
        score: number;
        strengths: string[];
        weaknesses: string[];
        recommendation: string;
    } | null>(null);
    const analyzeAbortRef = useRef<AbortController | null>(null);

    // ── Upload state ──
    const [pendingUploadFile, setPendingUploadFile] = useState<File | null>(null);
    const [uploading, setUploading] = useState(false);
    const [uploadMsg, setUploadMsg] = useState<{ type: 'ok' | 'err'; text: string } | null>(null);
    const uploadAbortRef = useRef<AbortController | null>(null);

    // ── Expert Review state ──
    const [reviewConfirm, setReviewConfirm] = useState(false);
    const [requestingReview, setRequestingReview] = useState(false);
    const [reviewRequestMsg, setReviewRequestMsg] = useState<{ type: 'ok' | 'err'; text: string } | null>(null);
    const [openReview, setOpenReview] = useState<number | null>(null);

    // ── Handlers ──

    const handleCVAnalyze = async () => {
        if (!analyzeRole.trim()) {
            setAnalyzeMsg({ type: 'err', text: 'Please enter a target role.' });
            return;
        }
        if (!cvUrl && !analyzeCvFile) {
            setAnalyzeMsg({ type: 'err', text: 'Please upload a CV to analyze.' });
            return;
        }
        if (analyzeCvFile && analyzeCvFile.size > 10 * 1024 * 1024) {
            setAnalyzeMsg({ type: 'err', text: 'CV file must be under 10 MB.' });
            return;
        }

        const controller = new AbortController();
        analyzeAbortRef.current = controller;

        setAnalyzing(true);
        setAnalyzeMsg(null);
        setAnalyzeResult(null);

        try {
            const form = new FormData();
            form.append('target_role', analyzeRole.trim());
            if (analyzeJD.trim()) form.append('job_description', analyzeJD.trim());
            if (analyzeCvFile) form.append('cv_file', analyzeCvFile);

            const res = await fetch('/api/member/cv-analyze', {
                method: 'POST',
                body: form,
                signal: controller.signal,
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Analysis failed.');
            setAnalyzeResult(data);
        } catch (err: unknown) {
            if (err instanceof Error && err.name === 'AbortError') return;
            setAnalyzeMsg({ type: 'err', text: err instanceof Error ? err.message : 'Analysis error.' });
        } finally {
            analyzeAbortRef.current = null;
            setAnalyzing(false);
        }
    };

    const handleAnalyzeCancel = () => {
        analyzeAbortRef.current?.abort();
        setAnalyzing(false);
        setAnalyzeMsg({ type: 'err', text: 'Analysis cancelled.' });
    };

    const handleFileSelect = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (!file) return;
        e.target.value = '';
        if (file.type !== 'application/pdf') {
            setUploadMsg({ type: 'err', text: 'Only PDF files are supported.' });
            return;
        }
        if (file.size > 10 * 1024 * 1024) {
            setUploadMsg({ type: 'err', text: 'CV file must be under 10 MB.' });
            return;
        }
        setUploadMsg(null);
        setPendingUploadFile(file);
    };

    const handleCVUpload = async () => {
        if (!pendingUploadFile) return;

        const controller = new AbortController();
        uploadAbortRef.current = controller;

        setUploading(true);
        setUploadMsg(null);
        setPendingUploadFile(null);

        const form = new FormData();
        form.append('cv_file', pendingUploadFile);

        try {
            const res = await fetch('/api/member/cv-upload', {
                method: 'POST',
                body: form,
                signal: controller.signal,
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Upload failed.');

            setCvUrl(data.cv_url);
            router.refresh();

            const parsed = data.parsed_data || {};
            const parts: string[] = [];
            if (parsed.first_name || parsed.last_name) parts.push('name');
            if (parsed.work_experiences?.length) parts.push(`${parsed.work_experiences.length} job${parsed.work_experiences.length !== 1 ? 's' : ''}`);
            if (parsed.education_history?.length) parts.push(`${parsed.education_history.length} education entr${parsed.education_history.length !== 1 ? 'ies' : 'y'}`);
            if (parsed.skills_separate) parts.push('skills');
            if (parsed.user_bio) parts.push('bio');
            const summary = parts.length
                ? ` Extracted: ${parts.join(', ')}.`
                : ' No data could be extracted — check the PDF is readable.';
            setUploadMsg({ type: 'ok', text: `CV uploaded — your profile has been updated automatically.${summary}` });
        } catch (err: unknown) {
            if (err instanceof Error && err.name === 'AbortError') return;
            setUploadMsg({ type: 'err', text: err instanceof Error ? err.message : 'Upload error.' });
        } finally {
            uploadAbortRef.current = null;
            setUploading(false);
        }
    };

    const handleUploadCancel = () => {
        uploadAbortRef.current?.abort();
        setUploading(false);
        setUploadMsg({ type: 'err', text: 'Upload cancelled.' });
    };

    const handleRequestReview = async () => {
        setRequestingReview(true);
        setReviewRequestMsg(null);
        setReviewConfirm(false);
        const result = await BPUApi.requestCVReview();
        setReviewRequestMsg(result.success
            ? { type: 'ok', text: 'Review requested — our team will be in touch within 5 working days.' }
            : { type: 'err', text: result.error || 'Could not submit request.' }
        );
        setRequestingReview(false);
    };

    return (
        <>
            <style>{PROGRESS_STYLE}</style>
            <div className="wrap-sm fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
                <div>
                    <h2 className="text-xl font-bold">CV Clinic</h2>
                    <p className="section-sub">Three ways to get the most out of your CV — pick the tool that fits your need.</p>
                </div>

                {/* ── Sub-tab bar ── */}
                <div className="card" style={{ padding: '6px', display: 'flex', gap: '4px', overflowX: 'auto', scrollbarWidth: 'none', WebkitOverflowScrolling: 'touch' } as React.CSSProperties}>
                    {(
                        [
                            { id: 'analyse', label: 'AI Analysis',    badge: 'Free' },
                            { id: 'prep',    label: 'Interview Prep', badge: 'Free' },
                            { id: 'upload',  label: 'Upload & Parse', badge: isPro ? undefined : 'Pro' },
                            { id: 'review',  label: 'Expert Review',  badge: isPro ? undefined : 'Pro' },
                        ] as const
                    ).map(({ id, label, badge }) => (
                        <button
                            key={id}
                            onClick={() => setCvTab(id)}
                            className={cvTab === id ? 'btn btn-amber btn-sm' : 'btn btn-ghost btn-sm'}
                            style={{ justifyContent: 'center', gap: '6px', flexShrink: 0, whiteSpace: 'nowrap' }}
                        >
                            {label}
                            {badge && (
                                <span className={`badge ${badge === 'Free' ? 'badge-green' : 'badge-amber'}`} style={{ fontSize: '10px', padding: '1px 5px' }}>
                                    {badge}
                                </span>
                            )}
                        </button>
                    ))}
                </div>

                {/* ── Tab: AI Analysis ── */}
                {cvTab === 'analyse' && (
                    <div className="card card-p space-y-5">
                        <div>
                            <p className="section-title">Instant AI Analysis</p>
                            <p className="text-sm text-text-2">
                                Paste a job description and upload your CV — our AI scores how well you match the role and highlights
                                specific strengths and gaps. <strong>Free for all members.</strong> No CV saved to your profile.
                            </p>
                        </div>

                        <div className="space-y-3">
                            <div>
                                <label className="field-label">Target role <span className="text-red-400">*</span></label>
                                <input
                                    className="field-input"
                                    placeholder="e.g. Senior Software Engineer"
                                    value={analyzeRole}
                                    onChange={(e: React.ChangeEvent<HTMLInputElement>) => setAnalyzeRole(e.target.value)}
                                    disabled={analyzing}
                                />
                            </div>
                            <div>
                                <label className="field-label">
                                    Job description <span className="text-text-3 font-normal">(optional — paste the JD for a more accurate score)</span>
                                </label>
                                <textarea
                                    className="field-input field-textarea"
                                    placeholder="Paste the job description here…"
                                    rows={4}
                                    value={analyzeJD}
                                    onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => setAnalyzeJD(e.target.value)}
                                    disabled={analyzing}
                                />
                            </div>
                            {cvUrl
                                ? <p className="text-xs text-text-2">
                                    Using your saved CV.{' '}
                                    <label htmlFor="analyze-cv-file" className="underline cursor-pointer">Use a different file</label>
                                    {analyzeCvFile && <span className="ml-2 text-brand">{'✓'} {analyzeCvFile.name}</span>}
                                    <input
                                        id="analyze-cv-file"
                                        type="file"
                                        accept=".pdf"
                                        className="sr-only"
                                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => setAnalyzeCvFile(e.target.files?.[0] ?? null)}
                                    />
                                  </p>
                                : <div>
                                    <label className="field-label">CV (PDF) <span className="text-red-400">*</span></label>
                                    <label
                                        htmlFor="analyze-cv-file"
                                        className="block card p-4 text-center text-sm text-text-2 cursor-pointer"
                                        style={{ borderStyle: 'dashed' }}
                                    >
                                        {analyzeCvFile
                                            ? <span className="text-brand font-medium">{'✓'} {analyzeCvFile.name}</span>
                                            : 'Click to upload PDF'}
                                    </label>
                                    <input
                                        id="analyze-cv-file"
                                        type="file"
                                        accept=".pdf"
                                        className="sr-only"
                                        onChange={(e: React.ChangeEvent<HTMLInputElement>) => setAnalyzeCvFile(e.target.files?.[0] ?? null)}
                                    />
                                  </div>
                            }
                            {analyzeMsg && (
                                <div className={`alert ${analyzeMsg.type === 'ok' ? 'alert-green' : 'alert-red'} text-sm`}>
                                    {analyzeMsg.text}
                                </div>
                            )}
                            <div style={{ display: 'flex', gap: '8px', flexWrap: 'wrap' }}>
                                <button
                                    onClick={handleCVAnalyze}
                                    disabled={analyzing}
                                    className="btn btn-amber"
                                >
                                    {analyzing ? 'Analysing…' : 'Analyse my CV'}
                                </button>
                                {analyzing && (
                                    <button onClick={handleAnalyzeCancel} className="btn btn-ghost btn-sm" style={{ alignSelf: 'center' }}>
                                        Cancel
                                    </button>
                                )}
                            </div>
                        </div>

                        {analyzing && (
                            <div className="space-y-2 text-center py-4">
                                <div className="h-1.5 w-full bg-border rounded-full overflow-hidden">
                                    <div
                                        className="h-full bg-brand rounded-full"
                                        style={{ animation: 'bpuClinicLoad 90s linear forwards' }}
                                    />
                                </div>
                                <p className="text-sm text-text-2">Our AI is reviewing your CV{'…'}</p>
                            </div>
                        )}

                        {analyzeResult && (
                            <div className="space-y-4 pt-2 border-t border-border">
                                {/* Score */}
                                <div className="flex items-center gap-5">
                                    <div
                                        className="shrink-0 w-20 h-20 rounded-full flex items-center justify-center text-2xl font-extrabold"
                                        style={{
                                            background: analyzeResult.score >= 70 ? 'var(--green-bg)' : analyzeResult.score >= 45 ? '#fef9c3' : '#fee2e2',
                                            color: analyzeResult.score >= 70 ? 'var(--green)' : analyzeResult.score >= 45 ? '#a16207' : '#b91c1c',
                                        }}
                                    >
                                        {analyzeResult.score}
                                    </div>
                                    <div>
                                        <p className="font-bold">Match Score</p>
                                        <p className="text-sm text-text-2">
                                            {analyzeResult.score >= 70
                                                ? 'Strong match for this role.'
                                                : analyzeResult.score >= 45
                                                    ? 'Moderate match — some gaps to address.'
                                                    : 'Low match — significant improvements recommended.'}
                                        </p>
                                    </div>
                                </div>

                                {/* Strengths */}
                                {analyzeResult.strengths.length > 0 && (
                                    <div className="space-y-2">
                                        <p className="text-xs font-bold uppercase tracking-wide" style={{ color: 'var(--green)' }}>Strengths</p>
                                        <ul className="space-y-1">
                                            {analyzeResult.strengths.map((s: string, i: number) => (
                                                <li key={i} className="flex items-start gap-2 text-sm">
                                                    <span style={{ color: 'var(--green)' }} className="mt-0.5 shrink-0">{'✓'}</span> {s}
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                )}

                                {/* Weaknesses */}
                                {analyzeResult.weaknesses.length > 0 && (
                                    <div className="space-y-2">
                                        <p className="text-xs font-bold uppercase tracking-wide text-red-500">Areas to improve</p>
                                        <ul className="space-y-1">
                                            {analyzeResult.weaknesses.map((w: string, i: number) => (
                                                <li key={i} className="flex items-start gap-2 text-sm">
                                                    <span className="text-red-400 mt-0.5 shrink-0">{'✗'}</span> {w}
                                                </li>
                                            ))}
                                        </ul>
                                    </div>
                                )}

                                {/* Recommendation */}
                                {analyzeResult.recommendation && (
                                    <div className="card card-p space-y-1" style={{ background: 'var(--bg-2)' }}>
                                        <p className="text-xs font-bold uppercase tracking-wide text-text-3">Top recommendation</p>
                                        <p className="text-sm leading-relaxed">{analyzeResult.recommendation}</p>
                                    </div>
                                )}

                                <button
                                    onClick={() => setAnalyzeResult(null)}
                                    className="btn btn-ghost btn-sm"
                                >
                                    Analyse again
                                </button>
                            </div>
                        )}
                    </div>
                )}

                {/* ── Tab: Interview Prep ── */}
                {cvTab === 'prep' && (
                    <InterviewPrepTab cvUrl={cvUrl} />
                )}

                {/* ── Tab: Upload & Parse ── */}
                {cvTab === 'upload' && (
                    <div className="space-y-4">
                        <div className="card card-p space-y-1">
                            <p className="section-title">Upload &amp; Auto-fill Profile</p>
                            <p className="text-sm text-text-2">
                                Upload your CV and our AI will read it and automatically populate your BPU profile — work history,
                                education, certifications, and more. Your CV is also stored so recruiters can view it. <strong>Pro members only.</strong>
                            </p>
                        </div>

                        <ProGate isPro={isPro} feature="CV upload &amp; AI parsing">
                            {/* Current CV */}
                            {cvUrl && (
                                <div className="alert alert-green flex items-center justify-between gap-4">
                                    <span className="text-sm font-medium">CV on file</span>
                                    <div className="flex items-center gap-2">
                                        <a href={cvUrl} target="_blank" rel="noopener noreferrer" className="btn btn-outline btn-sm">
                                            Download
                                        </a>
                                        {!uploading && (
                                            <label htmlFor="cv-file" className="btn btn-outline btn-sm" style={{ cursor: 'pointer' }}>
                                                Replace
                                            </label>
                                        )}
                                    </div>
                                </div>
                            )}

                            {/* Pending upload confirmation */}
                            {pendingUploadFile && (
                                <div className="alert alert-green space-y-2">
                                    <p className="text-sm font-medium">Ready to upload: <strong>{pendingUploadFile.name}</strong></p>
                                    <div style={{ display: 'flex', gap: '8px' }}>
                                        <button onClick={handleCVUpload} className="btn btn-amber btn-sm">Confirm upload</button>
                                        <button
                                            onClick={() => { setPendingUploadFile(null); setUploadMsg(null); }}
                                            className="btn btn-ghost btn-sm"
                                        >
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            )}

                            {/* Upload area */}
                            {!pendingUploadFile && (
                                <div className="card" style={{ borderStyle: 'dashed' }}>
                                    <label
                                        htmlFor="cv-file"
                                        className="block p-12 text-center space-y-3"
                                        style={{ cursor: uploading ? 'not-allowed' : 'pointer' }}
                                    >
                                        <div className="text-4xl">{uploading ? '⏳' : '📄'}</div>
                                        <p className="text-base font-semibold">
                                            {uploading ? 'Reading your CV…' : cvUrl ? 'Click to replace your CV' : 'Click to upload your CV'}
                                        </p>
                                        <p className="text-sm text-text-2">PDF only &middot; Max 10 MB</p>
                                        <input
                                            id="cv-file"
                                            type="file"
                                            accept=".pdf"
                                            className="sr-only"
                                            onChange={handleFileSelect}
                                            disabled={uploading}
                                        />
                                    </label>
                                </div>
                            )}

                            {uploading && (
                                <div className="space-y-2">
                                    <div className="h-1.5 w-full bg-border rounded-full overflow-hidden">
                                        <div
                                            className="h-full bg-brand rounded-full"
                                            style={{ animation: 'bpuClinicLoad 90s linear forwards' }}
                                        />
                                    </div>
                                    <div className="flex justify-end">
                                        <button onClick={handleUploadCancel} className="btn btn-ghost btn-sm">Cancel</button>
                                    </div>
                                </div>
                            )}

                            {uploadMsg && (
                                <div className={`alert ${uploadMsg.type === 'ok' ? 'alert-green' : 'alert-red'} text-sm`}>
                                    {uploadMsg.text}
                                </div>
                            )}
                        </ProGate>
                    </div>
                )}

                {/* ── Tab: Expert Review ── */}
                {cvTab === 'review' && (
                    <div className="space-y-4">
                        <div className="card card-p space-y-1">
                            <p className="section-title">Expert Review by a BPU Recruiter</p>
                            <p className="text-sm text-text-2">
                                A real BPU recruiter reads your CV and writes a personalised, actionable critique — covering
                                structure, language, keywords, and presentation. Typically returned within 3{'–'}5 business days. <strong>Pro members only.</strong>
                            </p>
                        </div>

                        {isPro ? (
                            <div className="space-y-3">
                                {!reviewConfirm ? (
                                    <div className="flex items-center justify-between">
                                        <p className="text-sm text-text-2">Submit your CV for a written critique.</p>
                                        <button
                                            onClick={() => setReviewConfirm(true)}
                                            disabled={requestingReview}
                                            className="btn btn-amber btn-sm shrink-0"
                                        >
                                            Request review
                                        </button>
                                    </div>
                                ) : (
                                    <div className="alert alert-green space-y-2">
                                        <p className="text-sm font-medium">
                                            This will send your saved CV to our review team. Continue?
                                        </p>
                                        <div style={{ display: 'flex', gap: '8px' }}>
                                            <button
                                                onClick={handleRequestReview}
                                                disabled={requestingReview}
                                                className="btn btn-amber btn-sm"
                                            >
                                                {requestingReview ? 'Submitting…' : 'Yes, submit'}
                                            </button>
                                            <button
                                                onClick={() => setReviewConfirm(false)}
                                                className="btn btn-ghost btn-sm"
                                            >
                                                Cancel
                                            </button>
                                        </div>
                                    </div>
                                )}
                                {reviewRequestMsg && (
                                    <div className={`alert ${reviewRequestMsg.type === 'ok' ? 'alert-green' : 'alert-red'} text-sm`}>
                                        {reviewRequestMsg.text}
                                    </div>
                                )}
                                {reviews.length === 0
                                    ? <div className="empty">No reviews yet.</div>
                                    : reviews.map(r => (
                                        <div key={r.id} className="card overflow-hidden">
                                            <button
                                                className="w-full flex items-center justify-between px-5 py-4 text-left text-sm font-semibold hover:bg-bg transition-colors"
                                                onClick={() => setOpenReview(openReview === r.id ? null : r.id)}
                                            >
                                                <span className="flex items-center gap-3">
                                                    {r.title}
                                                    {r.score != null && (
                                                        <span className="badge badge-green">Score: {r.score}/100</span>
                                                    )}
                                                </span>
                                                <span className="text-text-3">{openReview === r.id ? '▲' : '▼'}</span>
                                            </button>
                                            {openReview === r.id && (
                                                <div className="px-5 pb-5 pt-2 border-t border-border text-sm space-y-3">
                                                    <p className="whitespace-pre-line leading-relaxed text-text-2">{r.critique}</p>
                                                    <div className="flex justify-between text-xs text-text-3 pt-2 border-t border-border">
                                                        <span>Reviewer: {r.reviewer}</span>
                                                        <span>{new Date(r.date).toLocaleDateString('en-GB')}</span>
                                                    </div>
                                                </div>
                                            )}
                                        </div>
                                    ))
                                }
                            </div>
                        ) : (
                            <div className="card card-p text-center py-8 space-y-3">
                                <p className="text-sm text-text-2">Get written feedback from a BPU recruiter with a Pro membership.</p>
                                <a href="/upgrade" className="btn btn-amber btn-sm inline-flex mx-auto">Upgrade to Pro &rarr;</a>
                            </div>
                        )}
                    </div>
                )}
            </div>
        </>
    );
}
