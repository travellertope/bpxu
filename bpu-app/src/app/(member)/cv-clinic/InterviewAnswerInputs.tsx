'use client';

import React from 'react';
import { QuestionType, StarAnswer, FreetextAnswer, RatingAnswer, RATING_LABELS } from './interview-prep-types';

// ── Type badge ─────────────────────────────────────────────────────────────

const TYPE_CONFIG: Record<QuestionType, { label: string; color: string; bg: string }> = {
    star:     { label: 'STAR Method', color: '#3b82f6', bg: '#eff6ff' },
    freetext: { label: 'Open Answer', color: '#7c3aed', bg: '#f5f3ff' },
    rating:   { label: 'Self-Rating', color: '#059669', bg: '#ecfdf5' },
};

export function TypeBadge({ type }: { type: QuestionType }) {
    const { label, color, bg } = TYPE_CONFIG[type];
    return (
        <span style={{
            display: 'inline-block',
            padding: '2px 8px',
            borderRadius: '999px',
            fontSize: '0.7rem',
            fontWeight: 700,
            textTransform: 'uppercase' as const,
            letterSpacing: '0.06em',
            color,
            background: bg,
        }}>
            {label}
        </span>
    );
}

// ── STAR input ─────────────────────────────────────────────────────────────

const STAR_FIELDS: { key: keyof StarAnswer; label: string; placeholder: string }[] = [
    { key: 'situation', label: 'Situation', placeholder: 'Describe the context and background…' },
    { key: 'task',      label: 'Task',      placeholder: 'What was your specific role or responsibility?' },
    { key: 'action',    label: 'Action',    placeholder: 'What steps did you take?' },
    { key: 'result',    label: 'Result',    placeholder: 'What was the outcome? What did you learn?' },
];

export function StarInput({ value, onChange }: {
    value: StarAnswer;
    onChange: (v: StarAnswer) => void;
}) {
    return (
        <div className="space-y-3">
            {STAR_FIELDS.map(({ key, label, placeholder }) => (
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
                    />
                </div>
            ))}
        </div>
    );
}

// ── Free-text input ────────────────────────────────────────────────────────

export function FreetextInput({ value, onChange }: {
    value: FreetextAnswer;
    onChange: (v: FreetextAnswer) => void;
}) {
    return (
        <textarea
            className="field-input field-textarea"
            rows={6}
            placeholder="Type your answer here…"
            value={value.text}
            onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => onChange({ text: e.target.value })}
        />
    );
}

// ── Rating input ───────────────────────────────────────────────────────────

export function RatingInput({ value, onChange }: {
    value: RatingAnswer;
    onChange: (v: RatingAnswer) => void;
}) {
    return (
        <div className="space-y-4">
            <div>
                <label className="field-label">Your self-rating</label>
                <div style={{ display: 'flex', gap: '10px', marginTop: '6px', alignItems: 'center' }}>
                    {[1, 2, 3, 4, 5].map(n => (
                        <button
                            key={n}
                            type="button"
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
                                cursor: 'pointer',
                                transition: 'all 0.15s',
                                flexShrink: 0,
                            }}
                        >
                            {n}
                        </button>
                    ))}
                    {value.score > 0 && (
                        <span style={{ fontSize: '0.85rem', color: 'var(--text-2)', marginLeft: '4px' }}>
                            {RATING_LABELS[value.score]}
                        </span>
                    )}
                </div>
            </div>
            <div>
                <label className="field-label">
                    Notes <span style={{ color: 'var(--text-3)', fontWeight: 400 }}>(optional)</span>
                </label>
                <textarea
                    className="field-input field-textarea"
                    rows={4}
                    placeholder="Add context or examples to support your rating…"
                    value={value.notes}
                    onChange={(e: React.ChangeEvent<HTMLTextAreaElement>) => onChange({ ...value, notes: e.target.value })}
                />
            </div>
        </div>
    );
}

// ── Rating display (summary / read-only) ───────────────────────────────────

export function RatingSummary({ value }: { value: RatingAnswer }) {
    return (
        <div className="space-y-1">
            <div style={{ display: 'flex', gap: '4px', alignItems: 'center' }}>
                {[1, 2, 3, 4, 5].map(n => (
                    <span key={n} style={{
                        display: 'inline-block',
                        width: '20px',
                        height: '20px',
                        borderRadius: '50%',
                        background: value.score >= n ? 'var(--amber, #f59e0b)' : 'var(--border, #e2e8f0)',
                        fontSize: '0.65rem',
                        lineHeight: '20px',
                        textAlign: 'center' as const,
                        color: value.score >= n ? '#fff' : 'var(--text-3)',
                        fontWeight: 700,
                    }}>{n}</span>
                ))}
                <span style={{ marginLeft: '6px', fontSize: '0.875rem' }}>
                    {RATING_LABELS[value.score]}
                </span>
            </div>
            {value.notes && (
                <p style={{ whiteSpace: 'pre-wrap', fontSize: '0.875rem', marginTop: '4px' }}>{value.notes}</p>
            )}
        </div>
    );
}
