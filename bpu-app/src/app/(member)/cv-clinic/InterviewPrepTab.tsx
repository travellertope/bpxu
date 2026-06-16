'use client';

import React, { useState, useRef } from 'react';
import {
    InterviewQuestion,
    Answer,
    emptyAnswer,
    isAnswered,
    formatAnswerForCopy,
} from './interview-prep-types';
import {
    TypeBadge,
    StarInput,
    FreetextInput,
    RatingInput,
    RatingSummary,
} from './InterviewAnswerInputs';

// Keyframe injected once — grows to 85% over 90s, honest about uncertainty.
const PROGRESS_KEYFRAMES = `
@keyframes bpuPrepLoad {
  0%   { width: 2% }
  15%  { width: 28% }
  40%  { width: 52% }
  70%  { width: 70% }
  90%  { width: 81% }
  100% { width: 85% }
}
`;

const CLIENT_TIMEOUT_MS = 150_000; // 150s — gives PHP's 120s Gemini call room to complete

type Screen = 'input' | 'quiz' | 'summary';

interface Props {
    cvUrl: string;
}

export default function InterviewPrepTab({ cvUrl }: Props) {
    const [screen, setScreen]       = useState<Screen>('input');
    const [jd, setJd]               = useState('');
    const [cvFile, setCvFile]       = useState<File | null>(null);
    const [loading, setLoading]     = useState(false);
    const [error, setError]         = useState<string | null>(null);
    const [questions, setQuestions] = useState<InterviewQuestion[]>([]);
    const [currentQ, setCurrentQ]   = useState(0);
    const [answers, setAnswers]     = useState<Record<number, Answer>>({});

    // Per-question reveal state for summary screen
    const [summaryAimOpen, setSummaryAimOpen] = useState<Record<number, boolean>>({});

    // Quiz-screen hint/aim toggles (reset on question change)
    const [hintOpen, setHintOpen] = useState(false);
    const [aimOpen,  setAimOpen]  = useState(false);

    // Copy state
    const [copyState, setCopyState] = useState<'idle' | 'ok' | 'err'>('idle');

    // "Start over" confirmation
    const [confirmRestart, setConfirmRestart] = useState(false);

    const abortRef    = useRef<AbortController | null>(null);
    const timeoutRef  = useRef<ReturnType<typeof setTimeout> | null>(null);
    const timedOutRef = useRef(false);

    const total    = questions.length;
    const progress = total > 0 ? Math.round(((currentQ + 1) / total) * 100) : 0;

    // ── Input screen ───────────────────────────────────────────────────────

    const handleGenerate = async () => {
        if (!jd.trim()) { setError('Please paste the job description.'); return; }
        if (!cvUrl && !cvFile) { setError('Please upload your CV as a PDF.'); return; }

        const controller = new AbortController();
        abortRef.current   = controller;
        timedOutRef.current = false;

        timeoutRef.current = setTimeout(() => {
            timedOutRef.current = true;
            controller.abort();
        }, CLIENT_TIMEOUT_MS);

        setLoading(true);
        setError(null);

        const form = new FormData();
        form.append('job_description', jd.trim());
        if (cvFile) form.append('cv_file', cvFile);

        try {
            const res  = await fetch('/api/member/interview-prep', { method: 'POST', body: form, signal: controller.signal });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to generate questions.');

            const qs: InterviewQuestion[] = data.questions;
            setQuestions(qs);
            setAnswers(Object.fromEntries(qs.map((q, i) => [i, emptyAnswer(q.type)])));
            setCurrentQ(0);
            setHintOpen(false);
            setAimOpen(false);
            setSummaryAimOpen({});
            setScreen('quiz');
        } catch (err: unknown) {
            if (err instanceof Error && err.name === 'AbortError') {
                if (timedOutRef.current) {
                    setError('The AI took too long to respond. Gemini may be under heavy load — please try again.');
                }
                // User-initiated cancel: no error shown, form stays ready
            } else {
                setError(err instanceof Error ? err.message : 'Something went wrong. Please try again.');
            }
        } finally {
            if (timeoutRef.current) clearTimeout(timeoutRef.current);
            abortRef.current = null;
            setLoading(false);
        }
    };

    const handleCancel = () => {
        abortRef.current?.abort();
    };

    // ── Answer helpers ─────────────────────────────────────────────────────

    const setAnswer = (i: number, a: Answer) => setAnswers((prev: Record<number, Answer>) => ({ ...prev, [i]: a }));

    const goTo = (i: number) => {
        setCurrentQ(i);
        setHintOpen(false);
        setAimOpen(false);
        setConfirmRestart(false);
    };

    const goNext = () => {
        if (currentQ < total - 1) goTo(currentQ + 1);
        else setScreen('summary');
    };

    const goPrev = () => {
        if (currentQ > 0) goTo(currentQ - 1);
    };

    // ── Restart with confirmation ──────────────────────────────────────────

    const anyAnswered = questions.some((_: InterviewQuestion, i: number) => isAnswered(answers[i]));

    const requestRestart = () => {
        if (screen === 'quiz' && anyAnswered) {
            setConfirmRestart(true);
        } else {
            doRestart();
        }
    };

    const doRestart = () => {
        setScreen('input');
        setJd('');
        setCvFile(null);
        setQuestions([]);
        setAnswers({});
        setCurrentQ(0);
        setError(null);
        setConfirmRestart(false);
        setSummaryAimOpen({});
    };

    // ── Clipboard ──────────────────────────────────────────────────────────

    const handleCopy = () => {
        const text = questions.map((q: InterviewQuestion, i: number) => formatAnswerForCopy(q, answers[i])).join('\n\n---\n\n');
        navigator.clipboard.writeText(text)
            .then(() => {
                setCopyState('ok');
                setTimeout(() => setCopyState('idle'), 2500);
            })
            .catch(() => {
                setCopyState('err');
                setTimeout(() => setCopyState('idle'), 3000);
            });
    };

    // ── Shared restart button / confirmation panel ─────────────────────────

    const RestartButton = () => confirmRestart ? (
        <div className="alert" style={{ background: '#fef2f2', border: '1px solid #fecaca', borderRadius: '8px', padding: '10px 14px', display: 'flex', alignItems: 'center', gap: '12px', flexWrap: 'wrap' }}>
            <span style={{ fontSize: '0.875rem', color: '#991b1b', flex: 1 }}>All answers will be lost. Are you sure?</span>
            <div style={{ display: 'flex', gap: '8px' }}>
                <button className="btn btn-ghost btn-sm" onClick={() => setConfirmRestart(false)}>Cancel</button>
                <button className="btn btn-sm" style={{ background: '#dc2626', color: '#fff' }} onClick={doRestart}>Yes, start over</button>
            </div>
        </div>
    ) : (
        <button className="btn btn-ghost btn-sm" onClick={requestRestart} style={{ width: '100%' }}>
            ✕ Start over
        </button>
    );

    // ── Screen 1: Input ────────────────────────────────────────────────────

    if (screen === 'input') {
        return (
            <div className="card card-p space-y-5">
                <style>{PROGRESS_KEYFRAMES}</style>
                <div>
                    <p className="section-title">Interview Prep — Culture-Add Questions</p>
                    <p className="text-sm text-text-2">
                        Paste the job description for a role you&apos;re applying to. Our AI will generate personalised
                        interview questions based on your CV and the role — focusing on <strong>culture-add</strong>:
                        what unique perspectives and experiences <em>you</em> bring, not just whether you match the team.{' '}
                        <strong>Free for all members.</strong>
                    </p>
                </div>

                <div className="space-y-3">
                    <div>
                        <label className="field-label">
                            Job description <span className="text-red-400">*</span>
                        </label>
                        <textarea
                            className="field-input field-textarea"
                            rows={6}
                            placeholder="Paste the full job description here…"
                            value={jd}
                            onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => setJd(e.target.value)}
                            disabled={loading}
                        />
                    </div>

                    {cvUrl ? (
                        <p className="text-xs text-text-2">
                            Using your saved CV.{' '}
                            <label htmlFor="prep-cv-file" className="underline cursor-pointer">Use a different file</label>
                            {cvFile && <span className="ml-2 text-brand">✓ {cvFile.name}</span>}
                            <input id="prep-cv-file" type="file" accept=".pdf" className="sr-only"
                                onChange={(e: React.ChangeEvent<HTMLInputElement>) => setCvFile(e.target.files?.[0] ?? null)} />
                        </p>
                    ) : (
                        <div>
                            <label className="field-label">CV (PDF) <span className="text-red-400">*</span></label>
                            <label htmlFor="prep-cv-file"
                                className="block card p-4 text-center text-sm text-text-2 cursor-pointer"
                                style={{ borderStyle: 'dashed' }}>
                                {cvFile
                                    ? <span className="text-brand font-medium">✓ {cvFile.name}</span>
                                    : 'Click to upload PDF'}
                            </label>
                            <input id="prep-cv-file" type="file" accept=".pdf" className="sr-only"
                                onChange={(e: React.ChangeEvent<HTMLInputElement>) => setCvFile(e.target.files?.[0] ?? null)} />
                        </div>
                    )}

                    {error && <div className="alert alert-red text-sm">{error}</div>}

                    {loading ? (
                        <button onClick={handleCancel} className="btn btn-ghost">
                            Cancel
                        </button>
                    ) : (
                        <button onClick={handleGenerate} className="btn btn-amber">
                            Generate interview questions
                        </button>
                    )}
                </div>

                {loading && (
                    <div className="space-y-2">
                        <div className="h-1.5 w-full bg-border rounded-full overflow-hidden">
                            <div style={{
                                height: '100%',
                                background: 'var(--brand, #2563eb)',
                                borderRadius: '9999px',
                                animation: 'bpuPrepLoad 90s ease-out forwards',
                            }} />
                        </div>
                        <p className="text-sm text-text-2 text-center">
                            Analysing your CV against the role — Gemini typically takes 30–60 seconds…
                        </p>
                    </div>
                )}
            </div>
        );
    }

    // ── Screen 2: Quiz ─────────────────────────────────────────────────────

    if (screen === 'quiz') {
        const q      = questions[currentQ];
        const answer = answers[currentQ];
        const done   = isAnswered(answer);

        return (
            <div className="space-y-4">
                <style>{PROGRESS_KEYFRAMES}</style>

                {/* Progress header */}
                <div className="card card-p space-y-2">
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <span className="text-xs font-semibold text-text-3 uppercase tracking-wide">
                            Question {currentQ + 1} of {total}
                        </span>
                        {/* Dot nav — 24px hit target, 8px visual dot */}
                        <div style={{ display: 'flex', gap: '2px' }}>
                            {questions.map((_: InterviewQuestion, i: number) => (
                                <button
                                    key={i}
                                    onClick={() => goTo(i)}
                                    title={`Question ${i + 1}`}
                                    style={{
                                        width: '24px',
                                        height: '24px',
                                        padding: 0,
                                        border: 'none',
                                        background: 'transparent',
                                        cursor: 'pointer',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                    }}
                                >
                                    <span style={{
                                        display: 'block',
                                        width: '8px',
                                        height: '8px',
                                        borderRadius: '50%',
                                        transition: 'background 0.2s',
                                        background: i === currentQ
                                            ? 'var(--amber, #f59e0b)'
                                            : isAnswered(answers[i])
                                                ? 'var(--green, #16a34a)'
                                                : 'var(--border, #e2e8f0)',
                                    }} />
                                </button>
                            ))}
                        </div>
                    </div>
                    <div className="h-1.5 w-full bg-border rounded-full overflow-hidden">
                        <div className="h-full bg-brand rounded-full transition-all" style={{ width: `${progress}%` }} />
                    </div>
                </div>

                {/* Question card */}
                <div className="card card-p space-y-4">
                    <div className="space-y-2">
                        <TypeBadge type={q.type} />
                        <p style={{ fontSize: '1.1rem', fontWeight: 600, lineHeight: 1.4 }}>{q.question}</p>
                    </div>

                    {/* Hint */}
                    {q.hint && (
                        <div>
                            <button type="button" className="btn btn-ghost btn-sm"
                                style={{ fontSize: '0.8rem', padding: '4px 10px' }}
                                onClick={() => setHintOpen((h: boolean) => !h)}>
                                {hintOpen ? '▲ Hide hint' : '💡 Show hint'}
                            </button>
                            {hintOpen && (
                                <div style={{ marginTop: '8px', background: 'var(--bg-2)', border: '1px solid var(--border)', borderRadius: '8px', padding: '10px 14px', fontSize: '0.875rem', color: 'var(--text-2)' }}>
                                    {q.hint}
                                </div>
                            )}
                        </div>
                    )}

                    {/* Answer input — type-safe via discriminated union */}
                    <div>
                        {answer.type === 'star' && (
                            <StarInput value={answer.data} onChange={d => setAnswer(currentQ, { type: 'star', data: d })} />
                        )}
                        {answer.type === 'freetext' && (
                            <FreetextInput value={answer.data} onChange={d => setAnswer(currentQ, { type: 'freetext', data: d })} />
                        )}
                        {answer.type === 'rating' && (
                            <RatingInput value={answer.data} onChange={d => setAnswer(currentQ, { type: 'rating', data: d })} />
                        )}
                    </div>

                    {/* Interviewer aim — reveal on demand */}
                    {q.aim && (
                        <div>
                            <button type="button" className="btn btn-ghost btn-sm"
                                style={{ fontSize: '0.8rem', padding: '4px 10px' }}
                                onClick={() => setAimOpen((o: boolean) => !o)}>
                                {aimOpen ? '▲ Hide' : '🔍 What the interviewer is looking for'}
                            </button>
                            {aimOpen && (
                                <div style={{ marginTop: '8px', background: '#f0fdf4', border: '1px solid #bbf7d0', borderRadius: '8px', padding: '10px 14px', fontSize: '0.875rem', color: '#166534' }}>
                                    {q.aim}
                                </div>
                            )}
                        </div>
                    )}

                    {/* Navigation */}
                    <div style={{ display: 'flex', gap: '10px', justifyContent: 'space-between', paddingTop: '8px', borderTop: '1px solid var(--border)' }}>
                        <button className="btn btn-ghost btn-sm" onClick={goPrev} disabled={currentQ === 0}>
                            ← Back
                        </button>
                        <button className="btn btn-amber btn-sm" onClick={goNext}>
                            {currentQ === total - 1
                                ? (done ? 'Finish & review →' : 'Skip & finish →')
                                : (done ? 'Next →' : 'Skip →')}
                        </button>
                    </div>
                </div>

                <RestartButton />
            </div>
        );
    }

    // ── Screen 3: Summary ──────────────────────────────────────────────────

    const answeredCount = questions.filter((_: InterviewQuestion, i: number) => isAnswered(answers[i])).length;

    return (
        <div className="space-y-4">
            <div className="card card-p space-y-1">
                <p className="section-title">Your Interview Prep Summary</p>
                <p className="text-sm text-text-2">
                    {answeredCount} of {total} questions answered.
                    Review your answers below. The &quot;Interviewer focus&quot; for each question is hidden by default — reveal it when you&apos;re ready.
                </p>
            </div>

            {questions.map((q: InterviewQuestion, i: number) => {
                const a        = answers[i];
                const answered = isAnswered(a);
                const aimVisible = summaryAimOpen[i] ?? false;

                return (
                    <div key={i} className="card card-p space-y-3">
                        <div className="space-y-1">
                            <div style={{ display: 'flex', alignItems: 'center', gap: '8px', flexWrap: 'wrap' }}>
                                <TypeBadge type={q.type} />
                                <span className="text-xs text-text-3">Q{i + 1}</span>
                                {answered && (
                                    <span style={{ marginLeft: 'auto', fontSize: '0.75rem', color: 'var(--green)', fontWeight: 700 }}>✓ Answered</span>
                                )}
                            </div>
                            <p style={{ fontWeight: 600, fontSize: '0.95rem' }}>{q.question}</p>
                        </div>

                        {/* Answer display — type-safe, no IIFEs */}
                        {answered ? (
                            <div style={{ fontSize: '0.875rem', color: 'var(--text-2)' }}>
                                {a.type === 'star' && (
                                    <div className="space-y-2">
                                        {(['situation', 'task', 'action', 'result'] as const)
                                            .filter(k => a.data[k])
                                            .map(k => (
                                                <div key={k}>
                                                    <span style={{ fontWeight: 700, color: 'var(--amber, #f59e0b)', textTransform: 'uppercase', fontSize: '0.7rem' }}>{k}</span>
                                                    <p style={{ marginTop: '2px', whiteSpace: 'pre-wrap' }}>{a.data[k]}</p>
                                                </div>
                                            ))}
                                    </div>
                                )}
                                {a.type === 'freetext' && (
                                    <p style={{ whiteSpace: 'pre-wrap' }}>{a.data.text}</p>
                                )}
                                {a.type === 'rating' && <RatingSummary value={a.data} />}
                            </div>
                        ) : (
                            <p className="text-sm text-text-3 italic">Not answered</p>
                        )}

                        {/* Interviewer aim — toggleable per question in summary too */}
                        {q.aim && (
                            <div>
                                <button type="button" className="btn btn-ghost btn-sm"
                                    style={{ fontSize: '0.8rem', padding: '4px 10px' }}
                                    onClick={() => setSummaryAimOpen((prev: Record<number, boolean>) => ({ ...prev, [i]: !aimVisible }))}>
                                    {aimVisible ? '▲ Hide' : '🔍 What the interviewer was looking for'}
                                </button>
                                {aimVisible && (
                                    <div style={{ marginTop: '8px', padding: '8px 12px', background: '#f0fdf4', border: '1px solid #bbf7d0', borderRadius: '8px', fontSize: '0.8rem', color: '#166534' }}>
                                        {q.aim}
                                    </div>
                                )}
                            </div>
                        )}

                        <button className="btn btn-ghost btn-sm" style={{ alignSelf: 'flex-start', fontSize: '0.8rem' }}
                            onClick={() => { goTo(i); setScreen('quiz'); }}>
                            ✎ Edit answer
                        </button>
                    </div>
                );
            })}

            {/* Actions */}
            <div className="card card-p space-y-2">
                <div style={{ display: 'flex', gap: '10px', flexWrap: 'wrap' }}>
                    <button className="btn btn-amber btn-sm" onClick={handleCopy}>
                        {copyState === 'ok'  ? '✓ Copied!'              : '📋 Copy all answers'}
                    </button>
                    <button className="btn btn-ghost btn-sm" onClick={requestRestart}>
                        ↺ Start over
                    </button>
                </div>
                {copyState === 'err' && (
                    <p className="text-xs text-red-500">
                        Clipboard access was blocked. Please copy the text manually.
                    </p>
                )}
            </div>
        </div>
    );
}
