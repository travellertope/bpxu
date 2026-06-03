'use client';

import { useState, useRef } from 'react';
import { Job, ScreeningQuestion } from '../types';
import { BPUUser } from '@/lib/auth';
import Link from 'next/link';

interface Props {
    job: Job;
    user: BPUUser;
    onClose: () => void;
}

const STEP_LABELS = [
    'Personal details',
    'Your CV',
    'Cover letter',
    'Screening questions',
    'Review & submit',
];

export default function ApplyWizard({ job, user, onClose }: Props) {
    const [step, setStep] = useState(1);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');
    const [submitted, setSubmitted] = useState(false);

    // Step 1 — Personal details
    const [phone, setPhone] = useState(user.profile?.phone_number || '');

    // Step 2 — CV
    const [cvFile, setCvFile] = useState<File | null>(null);
    const [replaceCV, setReplaceCV] = useState(false);
    const fileInputRef = useRef<HTMLInputElement>(null);

    // Step 3 — Cover letter
    const [coverLetter, setCoverLetter] = useState('');

    // Step 4 — Screening answers
    const questions: ScreeningQuestion[] = job.screening_questions ?? [];
    const [answers, setAnswers] = useState<Record<string, string>>(
        Object.fromEntries(questions.map(q => [q.id, '']))
    );

    const totalSteps = questions.length > 0 ? 5 : 4;
    const stepLabels = questions.length > 0
        ? STEP_LABELS
        : STEP_LABELS.filter((_, i) => i !== 3);

    function nextStep() {
        setError('');
        // Validate current step
        if (step === 4 && questions.length > 0) {
            const missing = questions.filter(q => q.required && !answers[q.id]?.trim());
            if (missing.length > 0) {
                setError(`Please answer all required questions (marked with *).`);
                return;
            }
        }
        setStep(s => s + 1);
    }

    function prevStep() {
        setError('');
        setStep(s => s - 1);
    }

    function getDisplayStep() {
        // When there are no screening questions, steps map: 1,2,3,4 -> 1,2,3,5
        if (questions.length === 0 && step >= 4) return 5;
        return step;
    }

    async function handleSubmit() {
        setError('');
        setLoading(true);

        try {
            const fd = new FormData();
            fd.append('phone', phone);
            fd.append('cover_letter', coverLetter);
            fd.append('screening_answers', JSON.stringify(answers));

            if (cvFile) {
                fd.append('cv_file', cvFile);
            }

            const res = await fetch(`/api/jobs/${job.id}/apply`, {
                method: 'POST',
                body: fd,
            });

            const data = await res.json();

            if (!res.ok) {
                setError(data.error || 'Application submission failed. Please try again.');
                setLoading(false);
                return;
            }

            setSubmitted(true);
        } catch {
            setError('Something went wrong. Please try again.');
            setLoading(false);
        }
    }

    const displayName = user.display_name || `${user.profile?.first_name ?? ''} ${user.profile?.last_name ?? ''}`.trim();
    const actualStep = getDisplayStep();

    return (
        <>
            {/* Backdrop */}
            <div
                className="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm"
                onClick={onClose}
                aria-hidden="true"
            />

            {/* Panel */}
            <div
                className="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:justify-end pointer-events-none"
                role="dialog"
                aria-modal="true"
                aria-label="Apply for role"
            >
                <div
                    className="pointer-events-auto w-full sm:w-[480px] sm:h-full flex flex-col"
                    style={{
                        background: 'var(--surface)',
                        borderRadius: '16px 16px 0 0',
                        boxShadow: '0 -4px 40px rgba(0,0,0,0.14)',
                        maxHeight: '92vh',
                        // On desktop: full-height right panel
                        borderTopRightRadius: '0',
                        borderBottomRightRadius: '0',
                    }}
                >
                    {/* Header */}
                    <div className="flex items-center justify-between px-6 py-4 border-b border-border shrink-0">
                        <div>
                            <p className="font-bold text-base">{submitted ? 'Application submitted!' : `Apply — ${job.title}`}</p>
                            {!submitted && (
                                <p className="text-xs text-text-3 mt-0.5">{job.company}</p>
                            )}
                        </div>
                        <button
                            type="button"
                            onClick={onClose}
                            className="btn btn-ghost btn-sm"
                            aria-label="Close"
                        >
                            ✕
                        </button>
                    </div>

                    {/* Success screen */}
                    {submitted ? (
                        <div className="flex-1 flex flex-col items-center justify-center px-6 py-10 text-center space-y-5">
                            <div
                                className="w-16 h-16 rounded-full flex items-center justify-center text-3xl"
                                style={{ background: 'var(--ok-bg)' }}
                            >
                                ✓
                            </div>
                            <div className="space-y-2">
                                <h2 className="text-xl font-bold">Application submitted!</h2>
                                <p className="text-sm text-text-2 max-w-xs mx-auto">
                                    Your application for <strong>{job.title}</strong> at <strong>{job.company}</strong> has been received. Good luck!
                                </p>
                            </div>
                            <div className="flex flex-col gap-2 w-full max-w-xs">
                                <Link href="/jobs" className="btn btn-amber w-full justify-center">
                                    Browse more jobs
                                </Link>
                                <button
                                    type="button"
                                    onClick={onClose}
                                    className="btn btn-outline w-full justify-center"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    ) : (
                        <>
                            {/* Progress */}
                            <div className="px-6 pt-4 pb-2 shrink-0">
                                <div className="flex items-center justify-between mb-2">
                                    <p className="text-xs font-semibold text-text-3 uppercase tracking-wider">
                                        {stepLabels[step - 1]}
                                    </p>
                                    <p className="text-xs text-text-3">
                                        Step {step} of {totalSteps}
                                    </p>
                                </div>
                                <div className="w-full h-1.5 rounded-full bg-border overflow-hidden">
                                    <div
                                        className="h-full rounded-full transition-all duration-300"
                                        style={{
                                            width: `${(step / totalSteps) * 100}%`,
                                            background: 'var(--brand)',
                                        }}
                                    />
                                </div>
                            </div>

                            {/* Body */}
                            <div className="flex-1 overflow-y-auto px-6 py-4 space-y-5">
                                {error && (
                                    <div className="alert alert-red text-sm">{error}</div>
                                )}

                                {/* Step 1: Personal details */}
                                {step === 1 && (
                                    <div className="space-y-4">
                                        <div>
                                            <label className="field-label">Full name</label>
                                            <input
                                                type="text"
                                                className="field-input"
                                                value={displayName}
                                                readOnly
                                                style={{ background: 'var(--bg)', cursor: 'not-allowed', color: 'var(--text-2)' }}
                                            />
                                            <p className="text-xs text-text-3 mt-1">From your profile (read-only)</p>
                                        </div>
                                        <div>
                                            <label className="field-label">Email address</label>
                                            <input
                                                type="email"
                                                className="field-input"
                                                value={user.email}
                                                readOnly
                                                style={{ background: 'var(--bg)', cursor: 'not-allowed', color: 'var(--text-2)' }}
                                            />
                                            <p className="text-xs text-text-3 mt-1">From your profile (read-only)</p>
                                        </div>
                                        <div>
                                            <label htmlFor="apply-phone" className="field-label">
                                                Phone number
                                            </label>
                                            <input
                                                id="apply-phone"
                                                type="tel"
                                                className="field-input"
                                                placeholder="+44 7700 000000"
                                                value={phone}
                                                onChange={e => setPhone(e.target.value)}
                                            />
                                        </div>
                                    </div>
                                )}

                                {/* Step 2: CV */}
                                {step === 2 && (
                                    <div className="space-y-4">
                                        {user.cv_url && !replaceCV ? (
                                            <div className="space-y-3">
                                                <div
                                                    className="flex items-center gap-3 p-4 rounded-lg border border-border"
                                                    style={{ background: 'var(--ok-bg)' }}
                                                >
                                                    <div
                                                        className="w-10 h-10 rounded-lg flex items-center justify-center text-xl shrink-0"
                                                        style={{ background: 'var(--ok-bg)', color: 'var(--ok)' }}
                                                    >
                                                        ✓
                                                    </div>
                                                    <div className="flex-1 min-w-0">
                                                        <p className="text-sm font-semibold" style={{ color: 'var(--ok)' }}>
                                                            Using saved CV
                                                        </p>
                                                        <p className="text-xs text-text-3 truncate">
                                                            Your CV is already on file
                                                        </p>
                                                    </div>
                                                </div>
                                                <button
                                                    type="button"
                                                    className="btn btn-outline btn-sm w-full"
                                                    onClick={() => setReplaceCV(true)}
                                                >
                                                    Upload a different CV
                                                </button>
                                            </div>
                                        ) : (
                                            <div className="space-y-3">
                                                {user.cv_url && replaceCV && (
                                                    <button
                                                        type="button"
                                                        className="btn btn-ghost btn-sm"
                                                        onClick={() => { setReplaceCV(false); setCvFile(null); }}
                                                    >
                                                        ← Use saved CV instead
                                                    </button>
                                                )}
                                                <div>
                                                    <label className="field-label">
                                                        Upload your CV
                                                        {!user.cv_url && ' *'}
                                                    </label>
                                                    <div
                                                        className="border-2 border-dashed rounded-lg p-6 text-center cursor-pointer transition-colors"
                                                        style={{ borderColor: cvFile ? 'var(--brand)' : 'var(--border)' }}
                                                        onClick={() => fileInputRef.current?.click()}
                                                    >
                                                        {cvFile ? (
                                                            <div className="space-y-1">
                                                                <p className="text-sm font-medium">{cvFile.name}</p>
                                                                <p className="text-xs text-text-3">
                                                                    {(cvFile.size / 1024).toFixed(0)} KB
                                                                </p>
                                                            </div>
                                                        ) : (
                                                            <div className="space-y-1">
                                                                <p className="text-2xl">📄</p>
                                                                <p className="text-sm text-text-2">
                                                                    Click to select a file
                                                                </p>
                                                                <p className="text-xs text-text-3">
                                                                    PDF, DOC, or DOCX — max 10 MB
                                                                </p>
                                                            </div>
                                                        )}
                                                    </div>
                                                    <input
                                                        ref={fileInputRef}
                                                        type="file"
                                                        accept=".pdf,.doc,.docx"
                                                        className="hidden"
                                                        onChange={e => setCvFile(e.target.files?.[0] ?? null)}
                                                    />
                                                </div>
                                            </div>
                                        )}
                                    </div>
                                )}

                                {/* Step 3: Cover letter */}
                                {step === 3 && (
                                    <div className="space-y-3">
                                        <div>
                                            <label htmlFor="apply-cover" className="field-label">
                                                Cover letter <span className="text-text-3 font-normal">(optional)</span>
                                            </label>
                                            <textarea
                                                id="apply-cover"
                                                className="field-input field-textarea"
                                                style={{ minHeight: '180px' }}
                                                placeholder={`Dear Hiring Manager,\n\nI am writing to express my interest in the ${job.title} position…`}
                                                value={coverLetter}
                                                onChange={e => setCoverLetter(e.target.value)}
                                            />
                                            <p className="text-xs text-text-3 mt-1">
                                                {coverLetter.length} characters
                                            </p>
                                        </div>
                                    </div>
                                )}

                                {/* Step 4: Screening questions (only shown when questions exist) */}
                                {step === 4 && questions.length > 0 && (
                                    <div className="space-y-4">
                                        {questions.map((q, i) => (
                                            <div key={q.id}>
                                                <label
                                                    htmlFor={`sq-${q.id}`}
                                                    className="field-label"
                                                >
                                                    {i + 1}. {q.question}
                                                    {q.required && <span className="text-err ml-1">*</span>}
                                                </label>
                                                <input
                                                    id={`sq-${q.id}`}
                                                    type="text"
                                                    className="field-input"
                                                    value={answers[q.id] ?? ''}
                                                    onChange={e =>
                                                        setAnswers(prev => ({ ...prev, [q.id]: e.target.value }))
                                                    }
                                                    required={q.required}
                                                />
                                            </div>
                                        ))}
                                    </div>
                                )}

                                {/* Step 5 (or 4 if no questions): Review & submit */}
                                {actualStep === 5 && (
                                    <div className="space-y-4">
                                        <p className="text-sm text-text-2">
                                            Please review your application before submitting.
                                        </p>

                                        <div className="card card-p space-y-3" style={{ background: 'var(--bg)' }}>
                                            <div>
                                                <p className="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">
                                                    Personal
                                                </p>
                                                <p className="text-sm"><strong>Name:</strong> {displayName}</p>
                                                <p className="text-sm"><strong>Email:</strong> {user.email}</p>
                                                {phone && <p className="text-sm"><strong>Phone:</strong> {phone}</p>}
                                            </div>

                                            <div className="divider" />

                                            <div>
                                                <p className="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">
                                                    CV
                                                </p>
                                                {cvFile ? (
                                                    <p className="text-sm">{cvFile.name} (uploaded)</p>
                                                ) : user.cv_url ? (
                                                    <p className="text-sm">Using saved CV ✓</p>
                                                ) : (
                                                    <p className="text-sm text-text-3 italic">No CV provided</p>
                                                )}
                                            </div>

                                            {coverLetter && (
                                                <>
                                                    <div className="divider" />
                                                    <div>
                                                        <p className="text-xs font-semibold text-text-3 uppercase tracking-wider mb-1">
                                                            Cover letter
                                                        </p>
                                                        <p className="text-sm text-text-2 line-clamp-3">
                                                            {coverLetter}
                                                        </p>
                                                    </div>
                                                </>
                                            )}

                                            {questions.length > 0 && (
                                                <>
                                                    <div className="divider" />
                                                    <div>
                                                        <p className="text-xs font-semibold text-text-3 uppercase tracking-wider mb-2">
                                                            Screening answers
                                                        </p>
                                                        {questions.map(q => (
                                                            <div key={q.id} className="mb-2">
                                                                <p className="text-xs text-text-2 font-medium">{q.question}</p>
                                                                <p className="text-sm">
                                                                    {answers[q.id] || <span className="text-text-3 italic">Not answered</span>}
                                                                </p>
                                                            </div>
                                                        ))}
                                                    </div>
                                                </>
                                            )}
                                        </div>
                                    </div>
                                )}
                            </div>

                            {/* Footer navigation */}
                            <div className="px-6 py-4 border-t border-border shrink-0 flex items-center justify-between gap-3">
                                {step > 1 ? (
                                    <button
                                        type="button"
                                        className="btn btn-outline"
                                        onClick={prevStep}
                                        disabled={loading}
                                    >
                                        Back
                                    </button>
                                ) : (
                                    <button
                                        type="button"
                                        className="btn btn-ghost"
                                        onClick={onClose}
                                        disabled={loading}
                                    >
                                        Cancel
                                    </button>
                                )}

                                {actualStep < 5 ? (
                                    <button
                                        type="button"
                                        className="btn btn-amber"
                                        onClick={nextStep}
                                        disabled={loading}
                                    >
                                        Continue
                                    </button>
                                ) : (
                                    <button
                                        type="button"
                                        className="btn btn-amber"
                                        onClick={handleSubmit}
                                        disabled={loading}
                                    >
                                        {loading ? 'Submitting…' : 'Submit application'}
                                    </button>
                                )}
                            </div>
                        </>
                    )}
                </div>
            </div>
        </>
    );
}
