'use client';

import { useState, useEffect } from 'react';
import type { BPUUser } from '@/lib/auth';

const INTERVIEW_TYPES = [
    'Competency-Based',
    'Technical',
    'Case Study',
    'Assessment Centre',
    'Partner / Senior Leadership',
    'Video / Async Interview',
    'Other',
];

const INDUSTRIES = [
    'Finance & Banking',
    'Legal',
    'Technology',
    'Consulting',
    'Marketing & Communications',
    'Healthcare',
    'Public Sector',
    'Creative Industries',
    'Other',
];

export default function InterviewPrepClient({ user }: { user: BPUUser }) {
    const [form, setForm] = useState({
        organisation: '',
        role: '',
        interviewType: '',
        interviewDate: '',
        industry: '',
        roundsCompleted: '',
        specificHelp: '',
    });
    const [file, setFile] = useState<File | null>(null);
    const [submitting, setSubmitting] = useState(false);
    const [submitted, setSubmitted] = useState(false);
    const [error, setError] = useState('');
    const [pastRequests, setPastRequests] = useState<{ id: number; status: string; data: Record<string, string>; created_at: string }[]>([]);

    useEffect(() => {
        fetch('/api/member/interview-prep-submit')
            .then(r => r.ok ? r.json() : { requests: [] })
            .then(d => setPastRequests(d.requests ?? []))
            .catch(() => {});
    }, [submitted]);

    function set(field: string, value: string) {
        setForm(f => ({ ...f, [field]: value }));
    }

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setError('');
        if (!form.organisation || !form.role || !form.interviewType || !form.industry || !form.specificHelp) {
            setError('Please fill in all required fields.');
            return;
        }
        setSubmitting(true);
        try {
            const body = new FormData();
            Object.entries(form).forEach(([k, v]) => body.append(k, v));
            if (file) body.append('file', file);
            const res = await fetch('/api/member/interview-prep-submit', { method: 'POST', body });
            if (!res.ok) throw new Error('Request failed');
            setSubmitted(true);
        } catch {
            setError('Something went wrong. Please try again or contact support.');
        } finally {
            setSubmitting(false);
        }
    }

    if (submitted) {
        return (
            <div className="dash-page-wrap">
                <div style={{ maxWidth: 600, margin: '0 auto', textAlign: 'center', paddingTop: 40 }}>
                    <div
                        style={{
                            width: 64, height: 64, borderRadius: '50%',
                            background: 'var(--brand)', border: '2px solid #111317',
                            display: 'flex', alignItems: 'center', justifyContent: 'center',
                            margin: '0 auto 20px',
                        }}
                    >
                        <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#fff" strokeWidth="2.5" strokeLinecap="round" strokeLinejoin="round">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                    </div>
                    <h1 className="font-black uppercase tracking-tight text-2xl mb-3" style={{ color: '#111317' }}>
                        Request Submitted
                    </h1>
                    <p className="text-base font-medium mb-6" style={{ color: 'var(--text-2)', maxWidth: '44ch', margin: '0 auto 24px' }}>
                        We've received your interview prep request. Our team will match you with a suitable coach and be in touch within <strong>48 hours</strong>.
                    </p>
                    <a href="/dashboard" className="btn btn-amber" style={{ border: '2px solid #111317' }}>
                        Back to Dashboard
                    </a>
                </div>
            </div>
        );
    }

    return (
        <div className="dash-page-wrap">
            {/* Page header */}
            <div className="mb-8">
                <p className="text-xs font-black uppercase tracking-widest mb-1" style={{ color: 'var(--brand)' }}>
                    Personalised Coaching
                </p>
                <h1 className="font-black uppercase tracking-tight text-2xl sm:text-3xl" style={{ color: '#111317' }}>
                    Interview Prep
                </h1>
                <p className="text-sm font-medium mt-2" style={{ color: 'var(--text-2)', maxWidth: '60ch' }}>
                    Tell us about your upcoming interview and we'll pair you with a coach who has direct experience in your industry and interview format.
                </p>
            </div>

            <form onSubmit={handleSubmit} style={{ maxWidth: 720 }}>
                <div className="card" style={{ border: '2px solid #111317', boxShadow: '4px 4px 0px 0px #001478' }}>
                    <div className="card-p" style={{ borderBottom: '2px solid #111317' }}>
                        <p className="text-xs font-black uppercase tracking-widest mb-0" style={{ color: 'var(--text-2)' }}>
                            About the Interview
                        </p>
                    </div>
                    <div className="card-p flex flex-col gap-5">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                    Organisation <span style={{ color: 'var(--brand)' }}>*</span>
                                </label>
                                <input
                                    className="field-input"
                                    placeholder="e.g. Goldman Sachs"
                                    value={form.organisation}
                                    onChange={e => set('organisation', e.target.value)}
                                    required
                                />
                            </div>
                            <div>
                                <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                    Role / Position <span style={{ color: 'var(--brand)' }}>*</span>
                                </label>
                                <input
                                    className="field-input"
                                    placeholder="e.g. Investment Banking Analyst"
                                    value={form.role}
                                    onChange={e => set('role', e.target.value)}
                                    required
                                />
                            </div>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                    Interview Type <span style={{ color: 'var(--brand)' }}>*</span>
                                </label>
                                <select
                                    className="field-input"
                                    value={form.interviewType}
                                    onChange={e => set('interviewType', e.target.value)}
                                    required
                                >
                                    <option value="">Select type…</option>
                                    {INTERVIEW_TYPES.map(t => <option key={t} value={t}>{t}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                    Industry / Sector <span style={{ color: 'var(--brand)' }}>*</span>
                                </label>
                                <select
                                    className="field-input"
                                    value={form.industry}
                                    onChange={e => set('industry', e.target.value)}
                                    required
                                >
                                    <option value="">Select industry…</option>
                                    {INDUSTRIES.map(i => <option key={i} value={i}>{i}</option>)}
                                </select>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                    Interview Date (if known)
                                </label>
                                <input
                                    type="date"
                                    className="field-input"
                                    value={form.interviewDate}
                                    onChange={e => set('interviewDate', e.target.value)}
                                />
                            </div>
                            <div>
                                <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                    Rounds Completed
                                </label>
                                <select
                                    className="field-input"
                                    value={form.roundsCompleted}
                                    onChange={e => set('roundsCompleted', e.target.value)}
                                >
                                    <option value="">Select…</option>
                                    <option value="0">This is my first round</option>
                                    <option value="1">1 round completed</option>
                                    <option value="2">2 rounds completed</option>
                                    <option value="3+">3 or more rounds completed</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                What specific help do you need? <span style={{ color: 'var(--brand)' }}>*</span>
                            </label>
                            <textarea
                                className="field-input"
                                rows={4}
                                placeholder="Describe the areas you're finding most challenging — e.g. structuring answers, technical questions, nerves, case frameworks…"
                                value={form.specificHelp}
                                onChange={e => set('specificHelp', e.target.value)}
                                required
                                style={{ resize: 'vertical' }}
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                Upload CV or Job Description (optional)
                            </label>
                            <input
                                type="file"
                                accept=".pdf,.doc,.docx"
                                className="field-input"
                                style={{ padding: '8px 12px' }}
                                onChange={e => setFile(e.target.files?.[0] ?? null)}
                            />
                            <p className="text-xs mt-1" style={{ color: 'var(--text-3)' }}>PDF, DOC, or DOCX · max 5 MB</p>
                        </div>
                    </div>
                </div>

                {error && (
                    <p className="text-sm font-bold mt-4" style={{ color: 'var(--brand)' }}>{error}</p>
                )}

                <div className="flex items-center gap-4 mt-6">
                    <button
                        type="submit"
                        className="btn btn-amber btn-lg"
                        style={{ border: '2px solid #111317', boxShadow: '4px 4px 0px 0px #001478' }}
                        disabled={submitting}
                    >
                        {submitting ? 'Submitting…' : 'Submit Request'}
                    </button>
                    <a href="/dashboard" className="btn btn-ghost btn-lg">Cancel</a>
                </div>

                <p className="text-xs mt-4" style={{ color: 'var(--text-3)' }}>
                    We respond to all requests within 48 hours. You will receive a confirmation email at <strong>{user.email}</strong>.
                </p>
            </form>

            {pastRequests.length > 0 && (
                <div className="mt-10" style={{ maxWidth: 720 }}>
                    <h2 className="font-black uppercase tracking-tight text-lg mb-4" style={{ color: '#111317' }}>Past Requests</h2>
                    <div className="flex flex-col gap-3">
                        {pastRequests.map(req => (
                            <div
                                key={req.id}
                                className="rounded-xl p-4"
                                style={{ background: '#fff', border: '2px solid #111317' }}
                            >
                                <div className="flex items-start justify-between gap-3 flex-wrap">
                                    <div>
                                        <p className="font-bold text-sm" style={{ color: '#111317' }}>
                                            {req.data?.role || 'Interview Prep Request'}{req.data?.organisation ? ` · ${req.data.organisation}` : ''}
                                        </p>
                                        <p className="text-xs mt-0.5" style={{ color: 'var(--text-3)' }}>
                                            {req.data?.interview_type || ''}{req.data?.interview_date ? ` · ${req.data.interview_date}` : ''}
                                        </p>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <span
                                            className="text-[10px] font-black uppercase px-2 py-0.5 rounded"
                                            style={{
                                                background: req.status === 'completed' ? '#f0fdf4' : req.status === 'accepted' ? '#eff6ff' : '#fff7ed',
                                                color: req.status === 'completed' ? '#16a34a' : req.status === 'accepted' ? '#1d4ed8' : '#c2410c',
                                                border: `1px solid currentColor`,
                                            }}
                                        >
                                            {req.status.replace('_', ' ')}
                                        </span>
                                        <span className="text-xs" style={{ color: 'var(--text-3)' }}>
                                            {new Date(req.created_at).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}
