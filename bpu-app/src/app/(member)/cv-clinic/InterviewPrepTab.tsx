'use client';

import React, { useState } from 'react';

// ── Types ──────────────────────────────────────────────────────────────────

type QuestionType = 'star' | 'freetext' | 'rating';

interface InterviewQuestion {
    question: string;
    type: QuestionType;
    hint: string;
    aim: string;
}

interface StarAnswer   { situation: string; task: string; action: string; result: string; }
interface FreetextAnswer { text: string; }
interface RatingAnswer { score: number; notes: string; }
type Answer = StarAnswer | FreetextAnswer | RatingAnswer;

type Screen = 'input' | 'quiz' | 'summary';

// ── Helpers ────────────────────────────────────────────────────────────────

function emptyAnswer(type: QuestionType): Answer {
    if (type === 'star')   return { situation: '', task: '', action: '', result: '' };
    if (type === 'rating') return { score: 0, notes: '' };
    return { text: '' };
}

function formatAnswerForCopy(q: InterviewQuestion, a: Answer): string {
    const lines: string[] = [`Q: ${q.question}`];
    if (q.type === 'star') {
        const s = a as StarAnswer;
        lines.push(`  Situation: ${s.situation || '—'}`);
        lines.push(`  Task:      ${s.task      || '—'}`);
        lines.push(`  Action:    ${s.action    || '—'}`);
        lines.push(`  Result:    ${s.result    || '—'}`);
    } else if (q.type === 'rating') {
        const r = a as RatingAnswer;
        lines.push(`  Self-rating: ${r.score}/5`);
        if (r.notes) lines.push(`  Notes: ${r.notes}`);
    } else {
        const f = a as FreetextAnswer;
        lines.push(`  ${f.text || '—'}`);
    }
    if (q.aim) lines.push(`  [Interviewer focus: ${q.aim}]`);
    return lines.join('\n');
}

function isAnswered(type: QuestionType, answer: Answer | undefined): boolean {
    if (!answer) return false;
    if (type === 'star') {
        const s = answer as StarAnswer;
        return !!(s.situation || s.task || s.action || s.result);
    }
    if (type === 'rating') return (answer as RatingAnswer).score > 0;
    return !!((answer as FreetextAnswer).text?.trim());
}

// ── Sub-components ─────────────────────────────────────────────────────────

function StarInput({ value, onChange, disabled }: {
    value: StarAnswer;
    onChange: (v: StarAnswer) => void;
    disabled?: boolean;
}) {
    const fields: { key: keyof StarAnswer; label: string; placeholder: string }[] = [
        { key: 'situation', label: 'Situation', placeholder: 'Describe the context and background…' },
        { key: 'task',      label: 'Task',      placeholder: 'What was your specific role or responsibility?' },
        { key: 'action',    label: 'Action',    placeholder: 'What steps did you take?' },
        { key: 'result',    label: 'Result',    placeholder: 'What was the outcome? What did you learn?' },
    ];
    return (
        <div className="space-y-3">
            {fields.map(({ key, label, placeholder }) => (
                <div key={key}>
                    <label className="field-label" style={{ color: 'var(--amber, #f59e0b)', fontWeight: 700, fontSize: '0.7rem', textTransform: 'uppercase', letterSpacing: '0.08em' }}>
                        {label}
                    </label>
                    <textarea
                        className="field-input field-textarea"
                        rows={3}
                        placeholder={placeholder}
                        value={value[key]}
                        onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => onChange({ ...value, [key]: e.target.value })}
                        disabled={disabled}
                    />
                </div>
            ))}
        </div>
    );
}

function FreetextInput({ value, onChange, disabled }: {
    value: FreetextAnswer;
    onChange: (v: FreetextAnswer) => void;
    disabled?: boolean;
}) {
    return (
        <textarea
            className="field-input field-textarea"
            rows={6}
            placeholder="Type your answer here…"
            value={value.text}
            onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => onChange({ text: e.target.value })}
            disabled={disabled}
        />
    );
}

function RatingInput({ value, onChange, disabled }: {
    value: RatingAnswer;
    onChange: (v: RatingAnswer) => void;
    disabled?: boolean;
}) {
    return (
        <div className="space-y-4">
            <div>
                <label className="field-label">Your self-rating</label>
                <div style={{ display: 'flex', gap: '10px', marginTop: '6px' }}>
                    {[1, 2, 3, 4, 5].map(n => (
                        <button
                            key={n}
                            type="button"
                            disabled={disabled}
                            onClick={() => onChange({ ...value, score: n })}
                            style={{
                                width: '44px',
                                height: '44px',
                                borderRadius: '50%',
                                border: '2px solid',
                                borderColor: value.score >= n ? 'var(--amber, #f59e0b)' : 'var(--border, #e2e8f0)',
                                background: value.score >= n ? 'var(--amber, #f59e0b)' : 'transparent',
                                color: value.score >= n ? '#fff' : 'var(--text-2, #64748b)',
                                fontWeight: 700,
                                fontSize: '1rem',
                                cursor: disabled ? 'not-allowed' : 'pointer',
                                transition: 'all 0.15s',
                            }}
                        >
                            {n}
                        </button>
                    ))}
                    {value.score > 0 && (
                        <span style={{ alignSelf: 'center', fontSize: '0.85rem', color: 'var(--text-2)', marginLeft: '6px' }}>
                            {['', 'Needs work', 'Developing', 'Competent', 'Confident', 'Excellent'][value.score]}
                        </span>
                    )}
                </div>
            </div>
            <div>
                <label className="field-label">Notes <span style={{ color: 'var(--text-3)', fontWeight: 400 }}>(optional)</span></label>
                <textarea
                    className="field-input field-textarea"
                    rows={4}
                    placeholder="Add any context or examples to support your rating…"
                    value={value.notes}
                    onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => onChange({ ...value, notes: e.target.value })}
                    disabled={disabled}
                />
            </div>
        </div>
    );
}

function TypeBadge({ type }: { type: QuestionType }) {
    const config = {
        star:     { label: 'STAR Method', color: '#3b82f6', bg: '#eff6ff' },
        freetext: { label: 'Open Answer', color: '#7c3aed', bg: '#f5f3ff' },
        rating:   { label: 'Self-Rating', color: '#059669', bg: '#ecfdf5' },
    }[type];
    return (
        <span style={{
            display: 'inline-block',
            padding: '2px 8px',
            borderRadius: '999px',
            fontSize: '0.7rem',
            fontWeight: 700,
            textTransform: 'uppercase',
            letterSpacing: '0.06em',
            color: config.color,
            background: config.bg,
        }}>
            {config.label}
        </span>
    );
}

// ── Main component ─────────────────────────────────────────────────────────

interface Props {
    cvUrl: string;
}

export default function InterviewPrepTab({ cvUrl }: Props) {
    const [screen, setScreen]         = useState<Screen>('input');
    const [jd, setJd]                 = useState('');
    const [cvFile, setCvFile]         = useState<File | null>(null);
    const [loading, setLoading]       = useState(false);
    const [error, setError]           = useState<string | null>(null);
    const [questions, setQuestions]   = useState<InterviewQuestion[]>([]);
    const [currentQ, setCurrentQ]     = useState(0);
    const [answers, setAnswers]       = useState<Record<number, Answer>>({});
    const [copied, setCopied]         = useState(false);
    const [hintOpen, setHintOpen]     = useState(false);
    const [aimOpen, setAimOpen]       = useState(false);

    const total = questions.length;
    const progress = total > 0 ? Math.round(((currentQ + 1) / total) * 100) : 0;

    // ── Input screen handlers ──

    const handleGenerate = async () => {
        if (!jd.trim()) { setError('Please paste the job description.'); return; }
        if (!cvUrl && !cvFile) { setError('Please upload a CV PDF.'); return; }

        setLoading(true);
        setError(null);

        const form = new FormData();
        form.append('job_description', jd.trim());
        if (cvFile) form.append('cv_file', cvFile);

        try {
            const res  = await fetch('/api/member/interview-prep', { method: 'POST', body: form });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to generate questions.');
            const qs: InterviewQuestion[] = data.questions;
            setQuestions(qs);
            setAnswers(Object.fromEntries(qs.map((q, i) => [i, emptyAnswer(q.type)])));
            setCurrentQ(0);
            setHintOpen(false);
            setAimOpen(false);
            setScreen('quiz');
        } catch (err: unknown) {
            setError(err instanceof Error ? err.message : 'Something went wrong.');
        } finally {
            setLoading(false);
        }
    };

    // ── Answer helpers ──

    const setAnswer = (i: number, a: Answer) => setAnswers(prev => ({ ...prev, [i]: a }));

    const goNext = () => {
        setHintOpen(false);
        setAimOpen(false);
        if (currentQ < total - 1) setCurrentQ(q => q + 1);
        else setScreen('summary');
    };

    const goPrev = () => {
        setHintOpen(false);
        setAimOpen(false);
        if (currentQ > 0) setCurrentQ(q => q - 1);
    };

    // ── Summary copy ──

    const handleCopy = () => {
        const text = questions.map((q, i) => formatAnswerForCopy(q, answers[i])).join('\n\n---\n\n');
        navigator.clipboard.writeText(text).then(() => {
            setCopied(true);
            setTimeout(() => setCopied(false), 2000);
        });
    };

    const handleRestart = () => {
        setScreen('input');
        setJd('');
        setCvFile(null);
        setQuestions([]);
        setAnswers({});
        setCurrentQ(0);
        setError(null);
    };

    // ── Render ─────────────────────────────────────────────────────────────

    // Screen 1: Input
    if (screen === 'input') {
        return (
            <div className="card card-p space-y-5">
                <div>
                    <p className="section-title">Interview Prep — Culture-Add Questions</p>
                    <p className="text-sm text-text-2">
                        Paste the job description for a role you&apos;re applying to. Our AI will generate personalised culture-add
                        questions you&apos;re likely to be asked, with an interactive quiz so you can practise your answers.{' '}
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
                            onChange={e => setJd(e.target.value)}
                            disabled={loading}
                        />
                    </div>

                    {cvUrl ? (
                        <p className="text-xs text-text-2">
                            Using your saved CV.{' '}
                            <label htmlFor="prep-cv-file" className="underline cursor-pointer">Use a different file</label>
                            {cvFile && <span className="ml-2 text-brand">✓ {cvFile.name}</span>}
                            <input
                                id="prep-cv-file"
                                type="file"
                                accept=".pdf"
                                className="sr-only"
                                onChange={e => setCvFile(e.target.files?.[0] ?? null)}
                            />
                        </p>
                    ) : (
                        <div>
                            <label className="field-label">CV (PDF) <span className="text-red-400">*</span></label>
                            <label
                                htmlFor="prep-cv-file"
                                className="block card p-4 text-center text-sm text-text-2 cursor-pointer"
                                style={{ borderStyle: 'dashed' }}
                            >
                                {cvFile
                                    ? <span className="text-brand font-medium">✓ {cvFile.name}</span>
                                    : 'Click to upload PDF'}
                            </label>
                            <input
                                id="prep-cv-file"
                                type="file"
                                accept=".pdf"
                                className="sr-only"
                                onChange={e => setCvFile(e.target.files?.[0] ?? null)}
                            />
                        </div>
                    )}

                    {error && <div className="alert alert-red text-sm">{error}</div>}

                    <button onClick={handleGenerate} disabled={loading} className="btn btn-amber">
                        {loading ? 'Generating questions…' : 'Generate interview questions'}
                    </button>
                </div>

                {loading && (
                    <div className="space-y-2 text-center py-4">
                        <div className="h-1.5 w-full bg-border rounded-full overflow-hidden">
                            <div className="h-full bg-brand rounded-full animate-pulse" style={{ width: '60%' }} />
                        </div>
                        <p className="text-sm text-text-2">Analysing your CV against the role — this takes about 15 seconds…</p>
                    </div>
                )}
            </div>
        );
    }

    // Screen 2: Quiz
    if (screen === 'quiz') {
        const q      = questions[currentQ];
        const answer = answers[currentQ];
        const done   = isAnswered(q.type, answer);

        return (
            <div className="space-y-4">
                {/* Progress */}
                <div className="card card-p space-y-2">
                    <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                        <span className="text-xs font-semibold text-text-3 uppercase tracking-wide">
                            Question {currentQ + 1} of {total}
                        </span>
                        <div style={{ display: 'flex', gap: '6px' }}>
                            {questions.map((_, i) => (
                                <button
                                    key={i}
                                    onClick={() => { setCurrentQ(i); setHintOpen(false); setAimOpen(false); }}
                                    style={{
                                        width: '10px',
                                        height: '10px',
                                        borderRadius: '50%',
                                        border: 'none',
                                        cursor: 'pointer',
                                        background: i === currentQ
                                            ? 'var(--amber, #f59e0b)'
                                            : isAnswered(questions[i].type, answers[i])
                                                ? 'var(--green, #16a34a)'
                                                : 'var(--border, #e2e8f0)',
                                        transition: 'background 0.2s',
                                    }}
                                    title={`Question ${i + 1}`}
                                />
                            ))}
                        </div>
                    </div>
                    <div className="h-1.5 w-full bg-border rounded-full overflow-hidden">
                        <div
                            className="h-full bg-brand rounded-full transition-all"
                            style={{ width: `${progress}%` }}
                        />
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
                            <button
                                type="button"
                                className="btn btn-ghost btn-sm"
                                style={{ fontSize: '0.8rem', padding: '4px 10px' }}
                                onClick={() => setHintOpen(h => !h)}
                            >
                                {hintOpen ? '▲ Hide hint' : '💡 Show hint'}
                            </button>
                            {hintOpen && (
                                <div className="alert" style={{ marginTop: '8px', background: 'var(--bg-2)', border: '1px solid var(--border)', borderRadius: '8px', padding: '10px 14px', fontSize: '0.875rem', color: 'var(--text-2)' }}>
                                    {q.hint}
                                </div>
                            )}
                        </div>
                    )}

                    {/* Answer input */}
                    <div>
                        {q.type === 'star' && (
                            <StarInput
                                value={answer as StarAnswer}
                                onChange={a => setAnswer(currentQ, a)}
                            />
                        )}
                        {q.type === 'freetext' && (
                            <FreetextInput
                                value={answer as FreetextAnswer}
                                onChange={a => setAnswer(currentQ, a)}
                            />
                        )}
                        {q.type === 'rating' && (
                            <RatingInput
                                value={answer as RatingAnswer}
                                onChange={a => setAnswer(currentQ, a)}
                            />
                        )}
                    </div>

                    {/* Interviewer aim */}
                    {q.aim && (
                        <div>
                            <button
                                type="button"
                                className="btn btn-ghost btn-sm"
                                style={{ fontSize: '0.8rem', padding: '4px 10px' }}
                                onClick={() => setAimOpen(o => !o)}
                            >
                                {aimOpen ? '▲ Hide' : '🔍 What the interviewer is looking for'}
                            </button>
                            {aimOpen && (
                                <div className="alert" style={{ marginTop: '8px', background: '#f0fdf4', border: '1px solid #bbf7d0', borderRadius: '8px', padding: '10px 14px', fontSize: '0.875rem', color: '#166534' }}>
                                    {q.aim}
                                </div>
                            )}
                        </div>
                    )}

                    {/* Navigation */}
                    <div style={{ display: 'flex', gap: '10px', justifyContent: 'space-between', paddingTop: '8px', borderTop: '1px solid var(--border)' }}>
                        <button
                            className="btn btn-ghost btn-sm"
                            onClick={goPrev}
                            disabled={currentQ === 0}
                        >
                            ← Back
                        </button>
                        <button
                            className="btn btn-amber btn-sm"
                            onClick={goNext}
                        >
                            {currentQ === total - 1
                                ? (done ? 'Finish & review →' : 'Skip & finish →')
                                : (done ? 'Next →' : 'Skip →')}
                        </button>
                    </div>
                </div>

                <button className="btn btn-ghost btn-sm" onClick={handleRestart} style={{ width: '100%' }}>
                    ✕ Start over
                </button>
            </div>
        );
    }

    // Screen 3: Summary
    const answeredCount = questions.filter((q, i) => isAnswered(q.type, answers[i])).length;

    return (
        <div className="space-y-4">
            {/* Header */}
            <div className="card card-p space-y-1">
                <p className="section-title">Your Interview Prep Summary</p>
                <p className="text-sm text-text-2">
                    {answeredCount} of {total} questions answered.
                    Review your answers below and copy the full session to keep for reference.
                </p>
            </div>

            {/* Q&A list */}
            {questions.map((q, i) => {
                const a = answers[i];
                const answered = isAnswered(q.type, a);
                return (
                    <div key={i} className="card card-p space-y-3">
                        <div className="space-y-1">
                            <div style={{ display: 'flex', alignItems: 'center', gap: '8px' }}>
                                <TypeBadge type={q.type} />
                                <span className="text-xs text-text-3">Q{i + 1}</span>
                                {answered && (
                                    <span style={{ marginLeft: 'auto', fontSize: '0.75rem', color: 'var(--green)', fontWeight: 700 }}>✓ Answered</span>
                                )}
                            </div>
                            <p style={{ fontWeight: 600, fontSize: '0.95rem' }}>{q.question}</p>
                        </div>

                        {/* Answer display */}
                        {answered ? (
                            <div className="space-y-1" style={{ fontSize: '0.875rem', color: 'var(--text-2)' }}>
                                {q.type === 'star' && (() => {
                                    const s = a as StarAnswer;
                                    return (
                                        <div className="space-y-1">
                                            {(['situation', 'task', 'action', 'result'] as const).map(k => s[k] && (
                                                <div key={k}>
                                                    <span style={{ fontWeight: 700, color: 'var(--amber, #f59e0b)', textTransform: 'uppercase', fontSize: '0.7rem' }}>{k}</span>
                                                    <p style={{ marginTop: '2px', whiteSpace: 'pre-wrap' }}>{s[k]}</p>
                                                </div>
                                            ))}
                                        </div>
                                    );
                                })()}
                                {q.type === 'freetext' && (
                                    <p style={{ whiteSpace: 'pre-wrap' }}>{(a as FreetextAnswer).text}</p>
                                )}
                                {q.type === 'rating' && (() => {
                                    const r = a as RatingAnswer;
                                    return (
                                        <div className="space-y-1">
                                            <div style={{ display: 'flex', gap: '4px' }}>
                                                {[1,2,3,4,5].map(n => (
                                                    <span key={n} style={{
                                                        display: 'inline-block',
                                                        width: '20px',
                                                        height: '20px',
                                                        borderRadius: '50%',
                                                        background: r.score >= n ? 'var(--amber, #f59e0b)' : 'var(--border, #e2e8f0)',
                                                        fontSize: '0.65rem',
                                                        lineHeight: '20px',
                                                        textAlign: 'center',
                                                        color: r.score >= n ? '#fff' : 'var(--text-3)',
                                                        fontWeight: 700,
                                                    }}>{n}</span>
                                                ))}
                                                <span style={{ alignSelf: 'center', marginLeft: '6px' }}>
                                                    {['', 'Needs work', 'Developing', 'Competent', 'Confident', 'Excellent'][r.score]}
                                                </span>
                                            </div>
                                            {r.notes && <p style={{ whiteSpace: 'pre-wrap', marginTop: '4px' }}>{r.notes}</p>}
                                        </div>
                                    );
                                })()}
                            </div>
                        ) : (
                            <p className="text-sm text-text-3 italic">Not answered</p>
                        )}

                        {/* Aim reveal */}
                        {q.aim && (
                            <div style={{ padding: '8px 12px', background: '#f0fdf4', border: '1px solid #bbf7d0', borderRadius: '8px', fontSize: '0.8rem', color: '#166534' }}>
                                <strong>Interviewer focus:</strong> {q.aim}
                            </div>
                        )}

                        {/* Edit button */}
                        <button
                            className="btn btn-ghost btn-sm"
                            style={{ alignSelf: 'flex-start', fontSize: '0.8rem' }}
                            onClick={() => { setCurrentQ(i); setHintOpen(false); setAimOpen(false); setScreen('quiz'); }}
                        >
                            ✎ Edit answer
                        </button>
                    </div>
                );
            })}

            {/* Actions */}
            <div className="card card-p" style={{ display: 'flex', gap: '10px', flexWrap: 'wrap' }}>
                <button className="btn btn-amber btn-sm" onClick={handleCopy}>
                    {copied ? '✓ Copied!' : '📋 Copy all answers'}
                </button>
                <button className="btn btn-ghost btn-sm" onClick={handleRestart}>
                    ↺ Start over
                </button>
            </div>
        </div>
    );
}
