'use client';

import { useState } from 'react';
import type { BPUUser } from '@/lib/auth';

const REGIONS = [
    'London',
    'Birmingham',
    'Manchester',
    'Leeds',
    'Bristol',
    'Edinburgh',
    'Cardiff',
    'Other UK',
    'Outside UK',
];

const SECTORS = [
    'Finance & Banking',
    'Legal',
    'Technology',
    'Consulting',
    'Marketing & Communications',
    'Healthcare',
    'Public Sector',
    'Education',
    'Creative Industries',
    'Other',
];

export default function AmbassadorshipClient({ user }: { user: BPUUser }) {
    const [form, setForm] = useState({
        region: '',
        sector: '',
        motivation: '',
        communityConnection: '',
        howRepresent: '',
        linkedin: '',
    });
    const [submitting, setSubmitting] = useState(false);
    const [submitted, setSubmitted] = useState(false);
    const [error, setError] = useState('');

    function set(field: string, value: string) {
        setForm(f => ({ ...f, [field]: value }));
    }

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setError('');
        if (!form.region || !form.sector || !form.motivation || !form.communityConnection || !form.howRepresent) {
            setError('Please fill in all required fields.');
            return;
        }
        setSubmitting(true);
        try {
            const res = await fetch('/api/member/ambassadorship', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(form),
            });
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
                        Thank you for applying to become a BPU Ambassador. We'll review your application and invite shortlisted applicants to a short conversation with the team.
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
                    Community Leadership
                </p>
                <h1 className="font-black uppercase tracking-tight text-2xl sm:text-3xl" style={{ color: '#111317' }}>
                    Become a BPU Ambassador
                </h1>
                <p className="text-sm font-medium mt-2" style={{ color: 'var(--text-2)', maxWidth: '60ch' }}>
                    As BPU scales, Ambassadors are the heartbeat that keeps it personal. Apply below and share your story — we want to hear from passionate members who want to give back.
                </p>
            </div>

            {/* What you're signing up for */}
            <div
                className="flex flex-wrap gap-4 p-5 mb-6 rounded-xl"
                style={{ background: '#111317', border: '2px solid #111317' }}
            >
                {[
                    { label: 'Community Leadership', desc: 'Welcome new members & connect them to the right resources' },
                    { label: 'Direct Platform Input', desc: 'Shape BPU\'s direction through a direct line to the team' },
                    { label: 'Exclusive Benefits', desc: 'Early access, priority event invitations, and platform recognition' },
                ].map(item => (
                    <div key={item.label} className="flex gap-3 items-start" style={{ flex: '1 1 220px' }}>
                        <div style={{ width: 6, height: 6, borderRadius: '50%', background: 'var(--brand)', flexShrink: 0, marginTop: 6 }} />
                        <div>
                            <p className="text-xs font-black uppercase tracking-tight text-white">{item.label}</p>
                            <p className="text-xs font-medium mt-0.5" style={{ color: 'rgba(255,255,255,0.6)' }}>{item.desc}</p>
                        </div>
                    </div>
                ))}
            </div>

            <form onSubmit={handleSubmit} style={{ maxWidth: 720 }}>
                <div className="card" style={{ border: '2px solid #111317', boxShadow: '4px 4px 0px 0px #001478' }}>
                    <div className="card-p" style={{ borderBottom: '2px solid #111317' }}>
                        <p className="text-xs font-black uppercase tracking-widest" style={{ color: 'var(--text-2)' }}>Your Application</p>
                    </div>
                    <div className="card-p flex flex-col gap-5">
                        {/* Read-only identity */}
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

                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                            <div>
                                <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                    Region / Location <span style={{ color: 'var(--brand)' }}>*</span>
                                </label>
                                <select
                                    className="field-input"
                                    value={form.region}
                                    onChange={e => set('region', e.target.value)}
                                    required
                                >
                                    <option value="">Select region…</option>
                                    {REGIONS.map(r => <option key={r} value={r}>{r}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                    Industry / Sector <span style={{ color: 'var(--brand)' }}>*</span>
                                </label>
                                <select
                                    className="field-input"
                                    value={form.sector}
                                    onChange={e => set('sector', e.target.value)}
                                    required
                                >
                                    <option value="">Select sector…</option>
                                    {SECTORS.map(s => <option key={s} value={s}>{s}</option>)}
                                </select>
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                What motivates you to become a BPU Ambassador? <span style={{ color: 'var(--brand)' }}>*</span>
                            </label>
                            <textarea
                                className="field-input"
                                rows={4}
                                placeholder="Tell us why you want to represent BPU and what drives your passion for supporting the community…"
                                value={form.motivation}
                                onChange={e => set('motivation', e.target.value)}
                                required
                                style={{ resize: 'vertical' }}
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                Describe your connection to the BPU community <span style={{ color: 'var(--brand)' }}>*</span>
                            </label>
                            <textarea
                                className="field-input"
                                rows={4}
                                placeholder="How long have you been a member? How has BPU supported your journey? What does this community mean to you?…"
                                value={form.communityConnection}
                                onChange={e => set('communityConnection', e.target.value)}
                                required
                                style={{ resize: 'vertical' }}
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                How would you represent and grow the BPU community in your area? <span style={{ color: 'var(--brand)' }}>*</span>
                            </label>
                            <textarea
                                className="field-input"
                                rows={4}
                                placeholder="Share any specific ideas, initiatives, or ways you would bring value as an Ambassador in your region or sector…"
                                value={form.howRepresent}
                                onChange={e => set('howRepresent', e.target.value)}
                                required
                                style={{ resize: 'vertical' }}
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                LinkedIn Profile URL (optional)
                            </label>
                            <input
                                className="field-input"
                                placeholder="https://linkedin.com/in/your-profile"
                                value={form.linkedin}
                                onChange={e => set('linkedin', e.target.value)}
                            />
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
                        {submitting ? 'Submitting…' : 'Submit Application'}
                    </button>
                    <a href="/dashboard" className="btn btn-ghost btn-lg">Cancel</a>
                </div>

                <p className="text-xs mt-4" style={{ color: 'var(--text-3)' }}>
                    Shortlisted applicants will be invited to a short conversation with the BPU team. We'll be in touch at <strong>{user.email}</strong>.
                </p>
            </form>
        </div>
    );
}
