'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';

interface Question {
    id: string;
    question: string;
    required: boolean;
}

interface Employer {
    id: number;
    name: string;
    logo_url: string;
    job_count: number;
}

export interface JobData {
    id?: number;
    title: string;
    company: string;
    location: string;
    employment_type: string;
    industry: string;
    job_type: 'inbound' | 'outbound';
    description: string;
    apply_url: string;
    salary_min: number | null;
    salary_max: number | null;
    salary_currency: string;
    expires: string;
    remote: boolean;
    featured: boolean;
    filled: boolean;
    screening_questions: Question[];
    employer?: { id: number; name: string } | null;
}

interface AdminJobFormProps {
    initialData?: JobData;
    mode: 'create' | 'edit';
}

const EMPLOYMENT_TYPES = ['Full-time', 'Part-time', 'Contract', 'Freelance', 'Internship'];
const INDUSTRIES = [
    'Technology', 'Finance', 'Healthcare', 'Education', 'Legal',
    'Marketing', 'Engineering', 'HR & Recruitment', 'Creative & Media',
    'Public Sector', 'Consulting', 'Other',
];

export default function AdminJobForm({ initialData, mode }: AdminJobFormProps) {
    const router = useRouter();
    const [employers, setEmployers] = useState<Employer[]>([]);
    const [employerSearch, setEmployerSearch] = useState('');
    const [showEmployerDropdown, setShowEmployerDropdown] = useState(false);
    const [selectedEmployer, setSelectedEmployer] = useState<Employer | null>(
        initialData?.employer ? { id: initialData.employer.id, name: initialData.employer.name, logo_url: '', job_count: 0 } : null
    );

    const [form, setForm] = useState({
        title: initialData?.title || '',
        company: initialData?.company || '',
        location: initialData?.location || '',
        employment_type: initialData?.employment_type || '',
        industry: initialData?.industry || '',
        job_type: (initialData?.job_type || 'outbound') as 'inbound' | 'outbound',
        description: initialData?.description || '',
        apply_url: initialData?.apply_url || '',
        salary_min: initialData?.salary_min != null ? String(initialData.salary_min) : '',
        salary_max: initialData?.salary_max != null ? String(initialData.salary_max) : '',
        salary_currency: initialData?.salary_currency || 'GBP',
        expires_date: initialData?.expires || '',
        remote: initialData?.remote || false,
        featured: initialData?.featured || false,
        filled: initialData?.filled || false,
    });
    const [questions, setQuestions] = useState<Question[]>(
        initialData?.screening_questions?.map(q => ({
            id: crypto.randomUUID(),
            question: q.question,
            required: q.required,
        })) || []
    );
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    const fetchEmployers = useCallback(async () => {
        try {
            const res = await fetch('/api/paired/admin/employers');
            if (res.ok) {
                const data = await res.json();
                setEmployers(data.employers || []);
            }
        } catch { /* ignore */ }
    }, []);

    useEffect(() => { fetchEmployers(); }, [fetchEmployers]);

    const filteredEmployers = employers.filter(e =>
        e.name.toLowerCase().includes(employerSearch.toLowerCase())
    );

    const selectEmployer = (emp: Employer) => {
        setSelectedEmployer(emp);
        setForm(f => ({ ...f, company: emp.name }));
        setEmployerSearch('');
        setShowEmployerDropdown(false);
    };

    const clearEmployer = () => {
        setSelectedEmployer(null);
        setForm(f => ({ ...f, company: '' }));
    };

    const addQuestion = () =>
        setQuestions(qs => [...qs, { id: crypto.randomUUID(), question: '', required: false }]);
    const removeQuestion = (id: string) =>
        setQuestions(qs => qs.filter(q => q.id !== id));
    const updateQuestion = (id: string, field: keyof Question, value: string | boolean) =>
        setQuestions(qs => qs.map(q => (q.id === id ? { ...q, [field]: value } : q)));

    const set = (key: string, val: string | boolean) => setForm(f => ({ ...f, [key]: val }));

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        setError('');
        try {
            const payload = {
                title: form.title,
                company: form.company,
                location: form.location,
                employment_type: form.employment_type,
                industry: form.industry,
                job_type: form.job_type,
                description: form.description,
                apply_url: form.apply_url,
                salary_min: form.salary_min ? parseInt(form.salary_min) : undefined,
                salary_max: form.salary_max ? parseInt(form.salary_max) : undefined,
                salary_currency: form.salary_currency,
                expires_date: form.expires_date,
                remote: form.remote,
                featured: form.featured,
                filled: form.filled,
                screening_questions: questions.filter(q => q.question.trim()),
            };

            const url =
                mode === 'create'
                    ? '/api/paired/admin/jobs/create'
                    : `/api/paired/admin/jobs/${initialData?.id}`;
            const method = mode === 'create' ? 'POST' : 'PUT';

            const res = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || `Failed to ${mode} job.`);
            router.push('/admin/jobs');
        } catch (err: unknown) {
            setError(err instanceof Error ? err.message : `Failed to ${mode} job.`);
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="fade-up space-y-6">
            {error && <div className="card card-p" style={{ borderLeft: '4px solid #ef4444', background: 'rgba(239,68,68,0.08)' }}><p className="text-sm" style={{ color: '#ef4444' }}>{error}</p></div>}

            <form onSubmit={handleSubmit} className="space-y-5">
                {/* Job type toggle */}
                <div className="card card-p space-y-3">
                    <p className="text-xs font-bold uppercase tracking-wide text-text-3">Job type</p>
                    <div className="flex gap-3">
                        {(['inbound', 'outbound'] as const).map(t => (
                            <button
                                key={t}
                                type="button"
                                onClick={() => set('job_type', t)}
                                className={`btn btn-sm flex-1 ${form.job_type === t ? 'btn-purple' : 'btn-ghost'}`}
                            >
                                {t === 'inbound' ? 'Direct application (in-platform)' : 'External link (partner site)'}
                            </button>
                        ))}
                    </div>
                    <p className="text-xs text-text-2">
                        {form.job_type === 'inbound'
                            ? 'Candidates apply directly on BPU. You receive CV, cover letter and answers in the dashboard.'
                            : 'Candidates click through to an external website. We track every click.'}
                    </p>
                </div>

                {/* Employer selection */}
                <div className="card card-p space-y-3">
                    <p className="text-xs font-bold uppercase tracking-wide text-text-3">Employer</p>
                    {selectedEmployer ? (
                        <div className="flex items-center gap-3 p-3 rounded-lg" style={{ background: 'var(--surface-2)' }}>
                            {selectedEmployer.logo_url && (
                                <img src={selectedEmployer.logo_url} alt="" className="w-8 h-8 rounded object-contain" style={{ background: '#fff' }} />
                            )}
                            <div className="flex-1 min-w-0">
                                <p className="font-semibold text-sm">{selectedEmployer.name}</p>
                                <p className="text-xs text-text-3">{selectedEmployer.job_count} jobs posted</p>
                            </div>
                            <button type="button" onClick={clearEmployer} className="btn btn-ghost btn-sm text-xs">Change</button>
                        </div>
                    ) : (
                        <div className="relative">
                            <input
                                className="field-input"
                                placeholder="Search employers..."
                                value={employerSearch}
                                onChange={e => { setEmployerSearch(e.target.value); setShowEmployerDropdown(true); }}
                                onFocus={() => setShowEmployerDropdown(true)}
                            />
                            {showEmployerDropdown && (
                                <div
                                    className="absolute z-10 left-0 right-0 mt-1 max-h-48 overflow-y-auto rounded-lg shadow-lg"
                                    style={{ background: 'var(--surface)', border: '1px solid var(--border)' }}
                                >
                                    {filteredEmployers.length === 0 ? (
                                        <div className="px-4 py-3 text-sm text-text-3">
                                            {employers.length === 0 ? 'Loading employers...' : 'No employers match'}
                                        </div>
                                    ) : (
                                        filteredEmployers.map(emp => (
                                            <button
                                                key={emp.id}
                                                type="button"
                                                onClick={() => selectEmployer(emp)}
                                                className="w-full text-left px-4 py-2 flex items-center gap-3 hover:bg-surface-2 transition-colors"
                                            >
                                                {emp.logo_url ? (
                                                    <img src={emp.logo_url} alt="" className="w-6 h-6 rounded object-contain" style={{ background: '#fff' }} />
                                                ) : (
                                                    <span className="w-6 h-6 rounded flex items-center justify-center text-xs font-bold" style={{ background: 'var(--surface-3)', color: 'var(--text-3)' }}>
                                                        {emp.name.charAt(0)}
                                                    </span>
                                                )}
                                                <span className="text-sm">{emp.name}</span>
                                                <span className="text-xs text-text-3 ml-auto">{emp.job_count} jobs</span>
                                            </button>
                                        ))
                                    )}
                                </div>
                            )}
                            <p className="text-xs text-text-3 mt-2">
                                Select an existing employer or type a company name below to use custom text.
                            </p>
                        </div>
                    )}
                    {!selectedEmployer && (
                        <div>
                            <label className="field-label">Or enter company name manually</label>
                            <input
                                className="field-input"
                                placeholder="Company name"
                                value={form.company}
                                onChange={e => set('company', e.target.value)}
                            />
                        </div>
                    )}
                </div>

                {/* Role details */}
                <div className="card card-p space-y-4">
                    <p className="text-xs font-bold uppercase tracking-wide text-text-3">Role details</p>
                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div className="sm:col-span-2">
                            <label className="field-label">Job title *</label>
                            <input
                                className="field-input"
                                required
                                placeholder="e.g. Senior Software Engineer"
                                value={form.title}
                                onChange={e => set('title', e.target.value)}
                            />
                        </div>
                        <div>
                            <label className="field-label">Location *</label>
                            <input
                                className="field-input"
                                required
                                placeholder="e.g. London, UK (Hybrid)"
                                value={form.location}
                                onChange={e => set('location', e.target.value)}
                            />
                        </div>
                        <div>
                            <label className="field-label">Employment type</label>
                            <select
                                className="field-input"
                                value={form.employment_type}
                                onChange={e => set('employment_type', e.target.value)}
                            >
                                <option value="">Select...</option>
                                {EMPLOYMENT_TYPES.map(o => (
                                    <option key={o}>{o}</option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="field-label">Industry</label>
                            <select
                                className="field-input"
                                value={form.industry}
                                onChange={e => set('industry', e.target.value)}
                            >
                                <option value="">Select...</option>
                                {INDUSTRIES.map(o => (
                                    <option key={o}>{o}</option>
                                ))}
                            </select>
                        </div>
                        <div>
                            <label className="field-label">Closing date</label>
                            <input
                                type="date"
                                className="field-input"
                                value={form.expires_date}
                                onChange={e => set('expires_date', e.target.value)}
                            />
                        </div>
                        {form.job_type === 'outbound' && (
                            <div className="sm:col-span-2">
                                <label className="field-label">Application URL *</label>
                                <input
                                    type="url"
                                    className="field-input"
                                    required
                                    placeholder="https://careers.yourcompany.com/..."
                                    value={form.apply_url}
                                    onChange={e => set('apply_url', e.target.value)}
                                />
                            </div>
                        )}
                    </div>
                </div>

                {/* Salary */}
                <div className="card card-p space-y-4">
                    <p className="text-xs font-bold uppercase tracking-wide text-text-3">Salary</p>
                    <div className="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div>
                            <label className="field-label">Min</label>
                            <input
                                type="number"
                                className="field-input"
                                placeholder="30000"
                                value={form.salary_min}
                                onChange={e => set('salary_min', e.target.value)}
                            />
                        </div>
                        <div>
                            <label className="field-label">Max</label>
                            <input
                                type="number"
                                className="field-input"
                                placeholder="50000"
                                value={form.salary_max}
                                onChange={e => set('salary_max', e.target.value)}
                            />
                        </div>
                        <div>
                            <label className="field-label">Currency</label>
                            <select
                                className="field-input"
                                value={form.salary_currency}
                                onChange={e => set('salary_currency', e.target.value)}
                            >
                                <option value="GBP">GBP (£)</option>
                                <option value="USD">USD ($)</option>
                                <option value="EUR">EUR (€)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {/* Admin toggles */}
                <div className="card card-p space-y-4">
                    <p className="text-xs font-bold uppercase tracking-wide text-text-3">Options</p>
                    <div className="flex flex-wrap gap-6">
                        <label className="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                checked={form.remote}
                                onChange={e => set('remote', e.target.checked)}
                                className="w-4 h-4"
                            />
                            <span className="text-sm">Remote position</span>
                        </label>
                        <label className="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                checked={form.featured}
                                onChange={e => set('featured', e.target.checked)}
                                className="w-4 h-4"
                            />
                            <span className="text-sm">Featured listing</span>
                        </label>
                        <label className="flex items-center gap-2 cursor-pointer">
                            <input
                                type="checkbox"
                                checked={form.filled}
                                onChange={e => set('filled', e.target.checked)}
                                className="w-4 h-4"
                            />
                            <span className="text-sm">Position filled</span>
                        </label>
                    </div>
                </div>

                {/* Description */}
                <div className="card card-p space-y-3">
                    <p className="text-xs font-bold uppercase tracking-wide text-text-3">Job description *</p>
                    <textarea
                        className="field-input field-textarea"
                        rows={10}
                        required
                        placeholder="Describe the role, responsibilities, requirements..."
                        value={form.description}
                        onChange={e => set('description', e.target.value)}
                    />
                </div>

                {/* Screening questions -- inbound only */}
                {form.job_type === 'inbound' && (
                    <div className="card card-p space-y-4">
                        <div className="flex items-center justify-between">
                            <p className="text-xs font-bold uppercase tracking-wide text-text-3">
                                Screening questions
                            </p>
                            <button type="button" onClick={addQuestion} className="btn btn-ghost btn-sm">
                                + Add question
                            </button>
                        </div>
                        {questions.length === 0 && (
                            <p className="text-sm text-text-2">
                                No screening questions yet. Candidates will only be asked for their CV and cover letter.
                            </p>
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
                                    <input
                                        type="checkbox"
                                        checked={q.required}
                                        onChange={e => updateQuestion(q.id, 'required', e.target.checked)}
                                        className="w-3 h-3"
                                    />
                                    Required
                                </label>
                                <button
                                    type="button"
                                    onClick={() => removeQuestion(q.id)}
                                    className="btn btn-ghost btn-sm mt-1.5 shrink-0 text-red-400"
                                >
                                    x
                                </button>
                            </div>
                        ))}
                    </div>
                )}

                <div className="flex justify-end gap-3">
                    <a href="/admin/jobs" className="btn btn-ghost">
                        Cancel
                    </a>
                    <button type="submit" disabled={loading} className="btn btn-purple">
                        {loading
                            ? 'Saving...'
                            : mode === 'create'
                              ? 'Create Job'
                              : 'Save Changes'}
                    </button>
                </div>
            </form>
        </div>
    );
}
