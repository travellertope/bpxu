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
import { PrepHistoryEntry } from './cv-clinic-history-types';

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

const CLIENT_TIMEOUT_MS = 150_000;

type Screen = 'input' | 'quiz' | 'summary';

interface SaveSessionData {
    jd: string;
    questions: unknown[];
    answers: Record<string, unknown>;
    answeredCount: number;
}

interface Props {
    cvUrl: string;
    prepHistory: PrepHistoryEntry[];
    onSaveSession: (data: SaveSessionData) => void;
}

// ── Past Sessions History Panel ───────────────────────────────────────────────

interface PrepHistoryPanelProps {
    entries: PrepHistoryEntry[];
    onReview: (entry: PrepHistoryEntry) => void;
    onPractice: (entry: PrepHistoryEntry) => void;
}

function PrepHistoryPanel({ entries, onReview, onPractice }: PrepHistoryPanelProps) {
    const [open, setOpen] = useState(false);
    if (entries.length === 0) return null;
    return (
        <div>
            <button
                className="btn btn-ghost btn-sm"
                onClick={() => setOpen((o: boolean) => !o)}
                style={{ fontSize: '0.8rem', display: 'flex', alignItems: 'center', gap: '6px' }}
            >
                <span>{open ? '▲' : '▼'}</span>
                Past sessions ({entries.length})
            </button>
            {open && (
                <div className="space-y-2" style={{ marginTop: '10px' }}>
                    {entries.map((e: PrepHistoryEntry) => (
                        <div
                            key={e.id}
                            className="card"
                            style={{ padding: '10px 14px' }}
                        >
                            <div style={{ marginBottom: '8px' }}>
                                <p style={{ fontWeight: 600, fontSize: '0.875rem', overflow: 'hidden', display: '-webkit-box', WebkitLineClamp: 2, WebkitBoxOrient: 'vertical' } as React.CSSProperties}>
                                    {e.jd_snippet || 'Interview prep session'}
                                </p>
                                <p style={{ fontSize: '0.75rem', color: 'var(--text-3)', marginTop: '2px' }}>
                                    {new Date(e.date).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })}
                                    {' · '}
                                    {e.answered_count}/{e.question_count} answered
                                </p>
                            </div>
                            <div style={{ display: 'flex', gap: '8px', flexWrap: 'wrap' }}>
                                <button
                                    className="btn btn-ghost btn-sm"
                                    style={{ fontSize: '0.75rem' }}
                                    onClick={() => { onReview(e); setOpen(false); }}
                                >
                                    Review answers
                                </button>
                                <button
                                    className="btn btn-ghost btn-sm"
                                    style={{ fontSize: '0.75rem' }}
                                    onClick={() => { onPractice(e); setOpen(false); }}
                                >
                                    Practice again
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}

// ── Main component ────────────────────────────────────────────────────────────

export default function InterviewPrepTab({ cvUrl, prepHistory, onSaveSession }: Props) {
    const [screen, setScreen]       = useState<Screen>('input');
    const [jd, setJd]               = useState('');
    const [cvFile, setCvFile]       = useState<File | null>(null);
    const [loading, setLoading]     = useState(false);
    const [error, setError]         = useState<string | null>(null);
    const [questions, setQuestions] = useState<InterviewQuestion[]>([]);
    const [currentQ, setCurrentQ]   = useState(0);
    const [answers, setAnswers]     = useState<Record<number, Answer>>({});
    const [savedAt, setSavedAt]     = useState<string | null>(null);
    const [saving, setSaving]       = useState(false);

    const [summaryAimOpen, setSummaryAimOpen] = useState<Record<number, boolean>>({});
    const [hintOpen, setHintOpen] = useState(false);
    const [aimOpen,  setAimOpen]  = useState(false);
    const [copyState, setCopyState] = useState<'idle' | 'ok' | 'err'>('idle');
    const [confirmRestart, setConfirmRestart] = useState(false);

    const abortRef    = useRef<AbortController | null>(null);
    const timeoutRef  = useRef<ReturnType<typeof setTimeout> | null>(null);
    const timedOutRef = useRef(false);

    const total    = questions.length;
    const progress = total > 0 ? Math.round(((currentQ + 1) / total) * 100) : 0;

    const answeredCount = questions.filter((_: InterviewQuestion, i: number) => isAnswered(answers[i])).length;

    // ── Generate ────────────────────────────────────────────────────────────

    const handleGenerate = async () => {
        if (!jd.trim()) { setError('Please paste the job description.'); return; }
        if (!cvUrl && !cvFile) { setError('Please upload your CV as a PDF.'); return; }

        const controller = new AbortController();
        abortRef.current    = controller;
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
            const emptyAnswers = Object.fromEntries(qs.map((q, i) => [i, emptyAnswer(q.type)]));
            setQuestions(qs);
            setAnswers(emptyAnswers);
            setCurrentQ(0);
            setHintOpen(false);
            setAimOpen(false);
            setSummaryAimOpen({});
            setSavedAt(null);
            setScreen('quiz');

            // Auto-save new session (empty answers)
            onSaveSession({
                jd: jd.trim(),
                questions: qs,
                answers: Object.fromEntries(Object.entries(emptyAnswers).map(([k, v]) => [k, v])),
                answeredCount: 0,
            });
        } catch (err: unknown) {
            if (err instanceof Error && err.name === 'AbortError') {
                if (timedOutRef.current) {
                    setError('The AI took too long to respond. Gemini may be under heavy load — please try again.');
                }
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

    // ── Save progress ────────────────────────────────────────────────────────

    const handleSaveProgress = () => {
        setSaving(true);
        onSaveSession({
            jd,
            questions,
            answers: Object.fromEntries(Object.entries(answers).map(([k, v]) => [k, v])),
            answeredCount,
        });
        setSavedAt(new Date().toLocaleTimeString('en-GB', { hour: '2-digit', minute: '2-digit' }));
        setSaving(false);
    };

    // ── Load from history ────────────────────────────────────────────────────

    const loadSession = (entry: PrepHistoryEntry, mode: 'review' | 'practice') => {
        const restoredAnswers: Record<number, Answer> = {};
        if (mode === 'review') {
            // Restore saved answers
            Object.entries(entry.answers).forEach(([k, v]) => {
                restoredAnswers[parseInt(k, 10)] = v as Answer;
            });
        } else {
            // Practice again with empty answers
            entry.questions.forEach((q, i) => {
                restoredAnswers[i] = emptyAnswer(q.type);
            });
        }
        setQuestions(entry.questions);
        setAnswers(restoredAnswers);
        setJd(entry.jd_snippet);
        setCurrentQ(0);
        setHintOpen(false);
        setAimOpen(false);
        setSummaryAimOpen({});
        setSavedAt(null);
        setScreen(mode === 'review' ? 'summary' : 'quiz');
    };

    // ── Navigation helpers ───────────────────────────────────────────────────

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
        setSavedAt(null);
    };

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

    // ── Screen 1: Input ──────────────────────────────────────────────────────

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
                        <button onClick={handleCancel} className="btn btn-ghost">Cancel</button>
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

                <div className="pt-2 border-t border-border">
                    <PrepHistoryPanel
                        entries={prepHistory}
                        onReview={e => loadSession(e, 'review')}
                        onPractice={e => loadSession(e, 'practice')}
                    />
                </div>
            </div>
        );
    }

    // ── Screen 2: Quiz ───────────────────────────────────────────────────────

    if (screen === 'quiz') {
        const q      = questions[currentQ];
        const answer = answers[currentQ];
        const done   = isAnswered(answer);

        return (
            <div className="space-y-4">
                <style>{PROGRESS_KEYFRAMES}</style>

                <div className="card card-p space-y-2">
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <span className="text-xs font-semibold text-text-3 uppercase tracking-wide">
                            Question {currentQ + 1} of {total}
                        </span>
                        <div style={{ display: 'flex', gap: '2px' }}>
                            {questions.map((_: InterviewQuestion, i: number) => (
                                <button
                                    key={i}
                                    onClick={() => goTo(i)}
                                    title={`Question ${i + 1}`}
                                    style={{
                                        width: '24px', height: '24px', padding: 0,
                                        border: 'none', background: 'transparent',
                                        cursor: 'pointer', display: 'flex',
                                        alignItems: 'center', justifyContent: 'center',
                                    }}
                                >
                                    <span style={{
                                        display: 'block', width: '8px', height: '8px',
                                        borderRadius: '50%', transition: 'background 0.2s',
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

                <div className="card card-p space-y-4">
                    <div className="space-y-2">
                        <TypeBadge type={q.type} />
                        <p style={{ fontSize: '1.1rem', fontWeight: 600, lineHeight: 1.4 }}>{q.question}</p>
                    </div>

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

                <div className="card card-p" style={{ display: 'flex', gap: '10px', flexWrap: 'wrap', alignItems: 'center' }}>
                    <button
                        className="btn btn-ghost btn-sm"
                        onClick={handleSaveProgress}
                        disabled={saving}
                        style={{ fontSize: '0.8rem' }}
                    >
                        {saving ? 'Saving…' : '💾 Save progress'}
                    </button>
                    {savedAt && (
                        <span style={{ fontSize: '0.75rem', color: 'var(--text-3)' }}>Saved at {savedAt}</span>
                    )}
                </div>

                <RestartButton />
            </div>
        );
    }

    // ── Screen 3: Summary ────────────────────────────────────────────────────

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

            <div className="card card-p space-y-2">
                <div style={{ display: 'flex', gap: '10px', flexWrap: 'wrap' }}>
                    <button className="btn btn-amber btn-sm" onClick={handleCopy}>
                        {copyState === 'ok' ? '✓ Copied!' : '📋 Copy all answers'}
                    </button>
                    <button
                        className="btn btn-ghost btn-sm"
                        onClick={handleSaveProgress}
                        disabled={saving}
                    >
                        {saving ? 'Saving…' : '💾 Save answers'}
                    </button>
                    <button className="btn btn-ghost btn-sm" onClick={requestRestart}>
                        ↺ Start over
                    </button>
                </div>
                {savedAt && (
                    <p style={{ fontSize: '0.75rem', color: 'var(--text-3)' }}>Answers saved at {savedAt}</p>
                )}
                {copyState === 'err' && (
                    <p className="text-xs text-red-500">
                        Clipboard access was blocked. Please copy the text manually.
                    </p>
                )}
            </div>
        </div>
    );
}
