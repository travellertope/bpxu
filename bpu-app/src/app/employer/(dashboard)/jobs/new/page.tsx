'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';

interface Question { id: string; question: string; required: boolean; }

export default function NewJobPage() {
    const router = useRouter();
    const [form, setForm] = useState({
        title: '', company: '', location: '', employment_type: '', industry: '',
        job_type: 'outbound' as 'inbound' | 'outbound', description: '',
        apply_url: '', salary_min: '', salary_max: '', expires_date: '',
    });
    const [questions, setQuestions] = useState<Question[]>([]);
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    const addQuestion = () => setQuestions(qs => [...qs, { id: crypto.randomUUID(), question: '', required: false }]);
    const removeQuestion = (id: string) => setQuestions(qs => qs.filter(q => q.id !== id));
    const updateQuestion = (id: string, field: keyof Question, value: string | boolean) =>
        setQuestions(qs => qs.map(q => q.id === id ? { ...q, [field]: value } : q));

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        try {
            const payload = {
                ...form,
                salary_min: form.salary_min ? parseInt(form.salary_min) : undefined,
                salary_max: form.salary_max ? parseInt(form.salary_max) : undefined,
                screening_questions: questions.filter(q => q.question.trim()),
            };
            const res = await fetch('/api/employer/jobs', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to post job.');
            router.push('/employer/jobs');
        } catch (err: unknown) {
            setError(err instanceof Error ? err.message : 'Failed to post job.');
        } finally {
            setLoading(false);
        }
    };

    const set = (key: string, val: string) => setForm(f => ({ ...f, [key]: val }));

    return (
        <div className="fade-up space-y-6">
                <div>
                    <h1 className="text-2xl font-bold">Post a new job</h1>
                    <p className="section-sub">Jobs posted by employers are reviewed before going live.</p>
                </div>

                {error && <div className="alert alert-red text-sm">{error}</div>}

                <form onSubmit={handleSubmit} className="space-y-5">
                    {/* Job type toggle */}
                    <div className="card card-p space-y-3">
                        <p className="text-xs font-bold uppercase tracking-wide text-text-3">Job type</p>
                        <div className="flex gap-3">
                            {(['inbound', 'outbound'] as const).map(t => (
                                <button key={t} type="button"
                                    onClick={() => set('job_type', t)}
                                    className={`btn btn-sm flex-1 ${form.job_type === t ? 'btn-amber' : 'btn-ghost'}`}
                                >
                                    {t === 'inbound' ? '📥 Direct application (in-platform)' : '🔗 External link (partner site)'}
                                </button>
                            ))}
                        </div>
                        <p className="text-xs text-text-2">
                            {form.job_type === 'inbound'
                                ? 'Candidates apply directly on BPU. You receive CV, cover letter and answers in your dashboard.'
                                : 'Candidates click through to your website. We track every click.'}
                        </p>
                    </div>

                    {/* Basic info */}
                    <div className="card card-p space-y-4">
                        <p className="text-xs font-bold uppercase tracking-wide text-text-3">Role details</p>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div className="sm:col-span-2">
                                <label className="field-label">Job title *</label>
                                <input className="field-input" required placeholder="e.g. Senior Software Engineer" value={form.title} onChange={e => set('title', e.target.value)} />
                            </div>
                            <div>
                                <label className="field-label">Company name *</label>
                                <input className="field-input" required value={form.company} onChange={e => set('company', e.target.value)} />
                            </div>
                            <div>
                                <label className="field-label">Location *</label>
                                <input className="field-input" required placeholder="e.g. London, UK (Hybrid)" value={form.location} onChange={e => set('location', e.target.value)} />
                            </div>
                            <div>
                                <label className="field-label">Employment type</label>
                                <select className="field-input" value={form.employment_type} onChange={e => set('employment_type', e.target.value)}>
                                    <option value="">Select…</option>
                                    {['Full-time', 'Part-time', 'Contract', 'Freelance', 'Internship'].map(o => <option key={o}>{o}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="field-label">Industry</label>
                                <select className="field-input" value={form.industry} onChange={e => set('industry', e.target.value)}>
                                    <option value="">Select…</option>
                                    {['Technology','Finance','Legal','Healthcare','Engineering','Marketing','Education','Other'].map(o => <option key={o}>{o}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="field-label">Salary min (£)</label>
                                <input type="number" className="field-input" placeholder="30000" value={form.salary_min} onChange={e => set('salary_min', e.target.value)} />
                            </div>
                            <div>
                                <label className="field-label">Salary max (£)</label>
                                <input type="number" className="field-input" placeholder="50000" value={form.salary_max} onChange={e => set('salary_max', e.target.value)} />
                            </div>
                            <div>
                                <label className="field-label">Closing date</label>
                                <input type="date" className="field-input" value={form.expires_date} onChange={e => set('expires_date', e.target.value)} />
                            </div>
                            {form.job_type === 'outbound' && (
                                <div className="sm:col-span-2">
                                    <label className="field-label">Application URL *</label>
                                    <input type="url" className="field-input" required placeholder="https://careers.yourcompany.com/..." value={form.apply_url} onChange={e => set('apply_url', e.target.value)} />
                                </div>
                            )}
                        </div>
                    </div>

                    {/* Description */}
                    <div className="card card-p space-y-3">
                        <p className="text-xs font-bold uppercase tracking-wide text-text-3">Job description *</p>
                        <textarea
                            className="field-input field-textarea"
                            rows={10}
                            required
                            placeholder="Describe the role, responsibilities, requirements…"
                            value={form.description}
                            onChange={e => set('description', e.target.value)}
                        />
                    </div>

                    {/* Screening questions — inbound only */}
                    {form.job_type === 'inbound' && (
                        <div className="card card-p space-y-4">
                            <div className="flex items-center justify-between">
                                <p className="text-xs font-bold uppercase tracking-wide text-text-3">Screening questions</p>
                                <button type="button" onClick={addQuestion} className="btn btn-ghost btn-sm">+ Add question</button>
                            </div>
                            {questions.length === 0 && (
                                <p className="text-sm text-text-2">No screening questions yet. Candidates will only be asked for their CV and cover letter.</p>
                            )}
                            {questions.map((q, i) => (
                                <div key={q.id} className="flex items-start gap-3">
                                    <span className="text-text-3 text-xs mt-3 shrink-0">Q{i + 1}</span>
                                    <input
                                        className="field-input flex-1"
                                        placeholder="e.g. Are you eligible to work in the UK?"
                                        value={q.question}
                                        onChange={e => updateQuestion(q.id, 'question', e.target.value)}
                                    />
                                    <label className="flex items-center gap-1 text-xs mt-3 shrink-0 cursor-pointer">
                                        <input type="checkbox" checked={q.required} onChange={e => updateQuestion(q.id, 'required', e.target.checked)} className="w-3 h-3" />
                                        Required
                                    </label>
                                    <button type="button" onClick={() => removeQuestion(q.id)} className="btn btn-ghost btn-sm mt-1.5 shrink-0 text-red-400">✕</button>
                                </div>
                            ))}
                        </div>
                    )}

                    <div className="flex justify-end gap-3">
                        <a href="/employer/jobs" className="btn btn-ghost">Cancel</a>
                        <button type="submit" disabled={loading} className="btn btn-amber">
                            {loading ? 'Submitting…' : 'Submit for review →'}
                        </button>
                    </div>
                </form>
        </div>
    );
}
