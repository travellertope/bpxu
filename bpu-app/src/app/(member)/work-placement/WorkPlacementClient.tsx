'use client';

import { useState } from 'react';
import type { BPUUser } from '@/lib/auth';

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

const EXPERIENCE_LEVELS = [
    'A-Level / Year 12–13',
    'Undergraduate (Year 1–2)',
    'Undergraduate (Final Year)',
    'Graduate / Recent Graduate',
    'Early Career (1–3 years)',
    'Mid-Career (3+ years)',
];

export default function WorkPlacementClient({ user }: { user: BPUUser }) {
    const [form, setForm] = useState({
        currentLevel: '',
        careerGoals: '',
        howHeard: '',
    });
    const [industries, setIndustries] = useState<string[]>([]);
    const [cv, setCv] = useState<File | null>(null);
    const [submitting, setSubmitting] = useState(false);
    const [submitted, setSubmitted] = useState(false);
    const [error, setError] = useState('');

    function set(field: string, value: string) {
        setForm(f => ({ ...f, [field]: value }));
    }

    function toggleIndustry(ind: string) {
        setIndustries(prev =>
            prev.includes(ind) ? prev.filter(i => i !== ind) : [...prev, ind]
        );
    }

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setError('');
        if (!form.currentLevel || industries.length === 0 || !form.careerGoals) {
            setError('Please fill in all required fields and select at least one area of interest.');
            return;
        }
        if (!cv) {
            setError('Please upload your CV to continue.');
            return;
        }
        setSubmitting(true);
        try {
            const body = new FormData();
            Object.entries(form).forEach(([k, v]) => body.append(k, v));
            body.append('industries', industries.join(','));
            body.append('cv', cv);
            const res = await fetch('/api/member/work-placement', { method: 'POST', body });
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
                        Application Received
                    </h1>
                    <p className="text-base font-medium mb-6" style={{ color: 'var(--text-2)', maxWidth: '44ch', margin: '0 auto 24px' }}>
                        Thank you for applying. Our team reviews every application personally and will be in touch within <strong>5–7 working days</strong>.
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
            <div className="mb-8">
                <p className="text-xs font-black uppercase tracking-widest mb-1" style={{ color: 'var(--brand)' }}>
                    Black Interns & Talents Programme
                </p>
                <h1 className="font-black uppercase tracking-tight text-2xl sm:text-3xl" style={{ color: '#111317' }}>
                    Work Placements
                </h1>
                <p className="text-sm font-medium mt-2" style={{ color: 'var(--text-2)', maxWidth: '60ch' }}>
                    Apply for a paid placement with one of our partner organisations. We review every application personally and match you to opportunities aligned with your ambitions.
                </p>
            </div>

            <form onSubmit={handleSubmit} style={{ maxWidth: 720 }}>
                {/* Personal details */}
                <div className="card mb-5" style={{ border: '2px solid #111317', boxShadow: '4px 4px 0px 0px #001478' }}>
                    <div className="card-p" style={{ borderBottom: '2px solid #111317' }}>
                        <p className="text-xs font-black uppercase tracking-widest" style={{ color: 'var(--text-2)' }}>Your Details</p>
                    </div>
                    <div className="card-p">
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>Full Name</label>
                                <input className="field-input" value={user.display_name} readOnly style={{ background: 'var(--bg)', cursor: 'default' }} />
                            </div>
                            <div>
                                <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>Email</label>
                                <input className="field-input" value={user.email} readOnly style={{ background: 'var(--bg)', cursor: 'default' }} />
                            </div>
                        </div>
                    </div>
                </div>

                {/* Application details */}
                <div className="card mb-5" style={{ border: '2px solid #111317', boxShadow: '4px 4px 0px 0px #001478' }}>
                    <div className="card-p" style={{ borderBottom: '2px solid #111317' }}>
                        <p className="text-xs font-black uppercase tracking-widest" style={{ color: 'var(--text-2)' }}>Application Details</p>
                    </div>
                    <div className="card-p flex flex-col gap-5">
                        <div>
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                Current Level / Stage <span style={{ color: 'var(--brand)' }}>*</span>
                            </label>
                            <select
                                className="field-input"
                                value={form.currentLevel}
                                onChange={e => set('currentLevel', e.target.value)}
                                required
                            >
                                <option value="">Select your current level…</option>
                                {EXPERIENCE_LEVELS.map(l => <option key={l} value={l}>{l}</option>)}
                            </select>
                        </div>

                        <div>
                            <label className="block text-xs font-extrabold uppercase mb-2 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                Areas of Interest <span style={{ color: 'var(--brand)' }}>*</span>
                            </label>
                            <div className="flex flex-wrap gap-2">
                                {INDUSTRIES.map(ind => (
                                    <button
                                        key={ind}
                                        type="button"
                                        onClick={() => toggleIndustry(ind)}
                                        className="text-xs font-bold uppercase px-3 py-1.5 rounded-lg"
                                        style={{
                                            border: '2px solid #111317',
                                            background: industries.includes(ind) ? '#111317' : '#fff',
                                            color: industries.includes(ind) ? '#fff' : 'var(--text)',
                                            cursor: 'pointer',
                                            transition: 'background 0.12s, color 0.12s',
                                        }}
                                    >
                                        {ind}
                                    </button>
                                ))}
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                Career Goals <span style={{ color: 'var(--brand)' }}>*</span>
                            </label>
                            <textarea
                                className="field-input"
                                rows={4}
                                placeholder="Tell us about your short and long-term career ambitions, what you hope to gain from a placement, and any specific skills you want to develop…"
                                value={form.careerGoals}
                                onChange={e => set('careerGoals', e.target.value)}
                                required
                                style={{ resize: 'vertical' }}
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                Upload Your CV <span style={{ color: 'var(--brand)' }}>*</span>
                            </label>
                            <input
                                type="file"
                                accept=".pdf,.doc,.docx"
                                className="field-input"
                                style={{ padding: '8px 12px' }}
                                onChange={e => setCv(e.target.files?.[0] ?? null)}
                                required
                            />
                            <p className="text-xs mt-1" style={{ color: 'var(--text-3)' }}>PDF, DOC, or DOCX · max 5 MB</p>
                        </div>

                        <div>
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                How did you hear about the programme?
                            </label>
                            <select
                                className="field-input"
                                value={form.howHeard}
                                onChange={e => set('howHeard', e.target.value)}
                            >
                                <option value="">Select…</option>
                                <option value="social-media">Social Media</option>
                                <option value="word-of-mouth">Word of Mouth</option>
                                <option value="university">University / College</option>
                                <option value="event">BPU Event</option>
                                <option value="search">Search Engine</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                    </div>
                </div>

                {error && (
                    <p className="text-sm font-bold mb-4" style={{ color: 'var(--brand)' }}>{error}</p>
                )}

                <div className="flex items-center gap-4">
                    <button
                        type="submit"
                        className="btn btn-amber btn-lg"
                        style={{ border: '2px solid #111317', boxShadow: '4px 4px 0px 0px #001478' }}
                        disabled={submitting}
                    >
                        {submitting ? 'Submitting…' : 'Submit Application'}
                    </button>
                    <a href="/dashboard" className="btn btn-ghost btn-lg">Cancel</a>
                </div>

                <p className="text-xs mt-4" style={{ color: 'var(--text-3)' }}>
                    Our team reviews every application personally. Confirmation will be sent to <strong>{user.email}</strong>.
                </p>
            </form>
        </div>
    );
}
