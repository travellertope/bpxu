'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';

const EXPERTISE_OPTIONS = [
    'Technology & Engineering',
    'Finance & Banking',
    'Legal & Compliance',
    'Healthcare & Medicine',
    'Marketing & Creative',
    'HR & People',
    'Entrepreneurship & Business',
    'Education & Academia',
    'Architecture & Design',
    'Media & Journalism',
    'Other',
];

const AVAILABILITY_OPTIONS = [
    '1 hour / month',
    '2 hours / month',
    '4 hours / month (1 hr/week)',
    '8 hours / month (2 hrs/week)',
];

const STYLE_OPTIONS = [
    'Career guidance & goal setting',
    'Technical skills & upskilling',
    'Interview & job search coaching',
    'Entrepreneurship & business advice',
    'Work–life balance & wellbeing',
    'Leadership & progression',
];

export default function MentorApplyPage() {
    const router = useRouter();

    const [form, setForm] = useState({
        job_title:       '',
        employer:        '',
        years_exp:       '',
        expertise:       '',
        mentorship_style: [] as string[],
        availability:    '',
        linkedin_url:    '',
        motivation:      '',
        has_mentored:    '',
    });
    const [loading, setLoading] = useState(false);
    const [error,   setError]   = useState('');
    const [success, setSuccess] = useState(false);

    function set(key: keyof typeof form, val: string) {
        setForm(f => ({ ...f, [key]: val }));
    }

    function toggleStyle(s: string) {
        setForm(f => ({
            ...f,
            mentorship_style: f.mentorship_style.includes(s)
                ? f.mentorship_style.filter(x => x !== s)
                : [...f.mentorship_style, s],
        }));
    }

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (form.mentorship_style.length === 0) { setError('Please select at least one mentoring style.'); return; }
        setError('');
        setLoading(true);
        try {
            const res  = await fetch('/api/paired/mentor-apply', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(form),
            });
            const data = await res.json();
            if (!res.ok || !data.success) {
                setError(data.error || 'Something went wrong. Please try again.');
                setLoading(false);
                return;
            }
            setSuccess(true);
        } catch {
            setError('Something went wrong. Please try again.');
        } finally {
            setLoading(false);
        }
    }

    if (success) {
        return (
            <div className="min-h-[60vh] flex items-center justify-center p-8">
                <div className="text-center max-w-md fade-up">
                    <div className="text-5xl mb-4">🎉</div>
                    <h1 className="text-2xl font-bold mb-2">Application submitted!</h1>
                    <p className="text-text-2 mb-6">
                        Thank you for applying to become a PAIRED mentor. Our team will review your application and be in touch within 5 working days.
                    </p>
                    <a href="/paired" className="btn btn-purple">Back to PAIRED</a>
                </div>
            </div>
        );
    }

    return (
        <div className="wrap-sm py-10 space-y-8 fade-up">

            {/* Header */}
            <div>
                <a href="/paired" className="text-sm text-text-2 hover:text-text inline-flex items-center gap-1 mb-6">
                    ← Back to PAIRED
                </a>
                <h1 className="text-3xl font-bold mb-2">Apply to become a mentor</h1>
                <p className="text-text-2 leading-relaxed max-w-xl">
                    Share your expertise with the next generation of Black professionals. PAIRED mentors commit to 1–4 hours per month and make a real difference to someone&apos;s career.
                </p>
            </div>

            {/* What to expect */}
            <div className="card card-p grid grid-cols-1 sm:grid-cols-3 gap-6 text-center">
                {[
                    { icon: '🤝', title: 'Flexible commitment', body: 'Choose your own availability — from 1 hour per month upwards.' },
                    { icon: '🌱', title: 'Real impact', body: 'Help Black professionals navigate careers, overcome barriers, and thrive.' },
                    { icon: '✅', title: 'Vetted community', body: 'All mentors are approved by the BPU team to ensure quality.' },
                ].map(c => (
                    <div key={c.title} className="space-y-2">
                        <p className="text-3xl">{c.icon}</p>
                        <p className="font-semibold text-sm">{c.title}</p>
                        <p className="text-xs text-text-2">{c.body}</p>
                    </div>
                ))}
            </div>

            {error && <div className="alert alert-red text-sm">{error}</div>}

            <form onSubmit={handleSubmit} className="space-y-6">

                {/* Professional background */}
                <div className="card card-p space-y-4">
                    <p className="text-xs font-bold uppercase tracking-wide text-text-3">Your professional background</p>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label className="field-label">Current job title *</label>
                            <input className="field-input" required placeholder="e.g. Senior Software Engineer"
                                value={form.job_title} onChange={e => set('job_title', e.target.value)} />
                        </div>
                        <div>
                            <label className="field-label">Employer / Organisation *</label>
                            <input className="field-input" required placeholder="e.g. Google, NHS, Self-employed"
                                value={form.employer} onChange={e => set('employer', e.target.value)} />
                        </div>
                        <div>
                            <label className="field-label">Years of professional experience *</label>
                            <select className="field-input" required value={form.years_exp} onChange={e => set('years_exp', e.target.value)}>
                                <option value="">Select…</option>
                                {['3–5', '5–10', '10–15', '15–20', '20+'].map(o => <option key={o}>{o} years</option>)}
                            </select>
                        </div>
                        <div>
                            <label className="field-label">Primary area of expertise *</label>
                            <select className="field-input" required value={form.expertise} onChange={e => set('expertise', e.target.value)}>
                                <option value="">Select…</option>
                                {EXPERTISE_OPTIONS.map(o => <option key={o}>{o}</option>)}
                            </select>
                        </div>
                        <div className="sm:col-span-2">
                            <label className="field-label">LinkedIn profile URL</label>
                            <input type="url" className="field-input" placeholder="https://linkedin.com/in/yourprofile"
                                value={form.linkedin_url} onChange={e => set('linkedin_url', e.target.value)} />
                        </div>
                    </div>
                </div>

                {/* Mentorship style */}
                <div className="card card-p space-y-4">
                    <p className="text-xs font-bold uppercase tracking-wide text-text-3">How you&apos;d like to mentor</p>

                    <div>
                        <label className="field-label mb-3">Mentoring focus areas * (select all that apply)</label>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-2">
                            {STYLE_OPTIONS.map(s => (
                                <label key={s} className="flex items-center gap-3 p-3 rounded-lg border border-border cursor-pointer hover:bg-bg transition-colors">
                                    <input
                                        type="checkbox"
                                        className="w-4 h-4 accent-brand"
                                        checked={form.mentorship_style.includes(s)}
                                        onChange={() => toggleStyle(s)}
                                    />
                                    <span className="text-sm">{s}</span>
                                </label>
                            ))}
                        </div>
                    </div>

                    <div>
                        <label className="field-label">Monthly availability *</label>
                        <div className="grid grid-cols-2 sm:grid-cols-4 gap-2 mt-2">
                            {AVAILABILITY_OPTIONS.map(a => (
                                <button key={a} type="button"
                                    onClick={() => set('availability', a)}
                                    className={`btn btn-sm text-center ${form.availability === a ? 'btn-amber' : 'btn-outline'}`}
                                >
                                    {a}
                                </button>
                            ))}
                        </div>
                    </div>

                    <div>
                        <label className="field-label">Have you formally mentored before? *</label>
                        <div className="flex gap-3 mt-2">
                            {['Yes', 'No', 'Informally'].map(o => (
                                <button key={o} type="button"
                                    onClick={() => set('has_mentored', o)}
                                    className={`btn btn-sm flex-1 ${form.has_mentored === o ? 'btn-amber' : 'btn-outline'}`}
                                >
                                    {o}
                                </button>
                            ))}
                        </div>
                    </div>
                </div>

                {/* Motivation */}
                <div className="card card-p space-y-4">
                    <p className="text-xs font-bold uppercase tracking-wide text-text-3">Your motivation</p>
                    <div>
                        <label className="field-label">Why do you want to be a PAIRED mentor? *</label>
                        <textarea
                            className="field-input field-textarea"
                            rows={5}
                            required
                            placeholder="Tell us what drives you to mentor and what you hope your mentees will gain from working with you…"
                            value={form.motivation}
                            onChange={e => set('motivation', e.target.value)}
                        />
                    </div>
                </div>

                <div className="flex justify-end gap-3">
                    <a href="/paired" className="btn btn-ghost">Cancel</a>
                    <button type="submit" disabled={loading || !form.availability || !form.has_mentored}
                        className="btn btn-purple btn-lg">
                        {loading ? 'Submitting…' : 'Submit application →'}
                    </button>
                </div>

                <p className="text-xs text-text-3 text-center">
                    By applying you agree to our{' '}
                    <a href="/paired" className="underline">Mentor Code of Conduct</a>.
                    Applications are reviewed within 5 working days.
                </p>
            </form>
        </div>
    );
}
