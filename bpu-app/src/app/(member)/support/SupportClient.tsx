'use client';

import { useState } from 'react';
import type { BPUUser } from '@/lib/auth';

const CATEGORIES = [
    'Membership & Account',
    'CV Clinic',
    'Interview Prep',
    'Work Placements',
    'Job Board',
    'Events & Bookings',
    'Mentorship / PAIRED',
    'Ambassadorship',
    'Technical Issue',
    'Other',
];

export default function SupportClient({ user }: { user: BPUUser }) {
    const [form, setForm] = useState({
        category: '',
        subject: '',
        urgency: 'normal',
        message: '',
    });
    const [attachment, setAttachment] = useState<File | null>(null);
    const [submitting, setSubmitting] = useState(false);
    const [submitted, setSubmitted] = useState(false);
    const [error, setError] = useState('');

    function set(field: string, value: string) {
        setForm(f => ({ ...f, [field]: value }));
    }

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setError('');
        if (!form.category || !form.subject || !form.message) {
            setError('Please fill in all required fields.');
            return;
        }
        setSubmitting(true);
        try {
            const body = new FormData();
            Object.entries(form).forEach(([k, v]) => body.append(k, v));
            if (attachment) body.append('attachment', attachment);
            const res = await fetch('/api/member/support', { method: 'POST', body });
            if (!res.ok) throw new Error('Request failed');
            setSubmitted(true);
        } catch {
            setError('Something went wrong. Please try again.');
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
                        Ticket Raised
                    </h1>
                    <p className="text-base font-medium mb-6" style={{ color: 'var(--text-2)', maxWidth: '44ch', margin: '0 auto 24px' }}>
                        We've received your support request. Our team will respond within <strong>48 hours</strong>. Check your email for a confirmation.
                    </p>
                    <div className="flex gap-3 justify-center">
                        <button
                            onClick={() => { setSubmitted(false); setForm({ category: '', subject: '', urgency: 'normal', message: '' }); setAttachment(null); }}
                            className="btn btn-outline"
                        >
                            Raise Another Ticket
                        </button>
                        <a href="/dashboard" className="btn btn-amber" style={{ border: '2px solid #111317' }}>
                            Back to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="dash-page-wrap">
            <div className="mb-8">
                <p className="text-xs font-black uppercase tracking-widest mb-1" style={{ color: 'var(--brand)' }}>
                    BPU Support Team
                </p>
                <h1 className="font-black uppercase tracking-tight text-2xl sm:text-3xl" style={{ color: '#111317' }}>
                    Member Support
                </h1>
                <p className="text-sm font-medium mt-2" style={{ color: 'var(--text-2)', maxWidth: '60ch' }}>
                    Raise a support ticket and our team will respond within 48 hours with clear, actionable guidance.
                </p>
            </div>

            {/* FAQ hint */}
            <div
                className="flex items-start gap-4 p-4 mb-6 rounded-xl"
                style={{ background: 'var(--brand-bg)', border: '1px solid var(--brand-ring)' }}
            >
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--brand)" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" style={{ flexShrink: 0, marginTop: 1 }}>
                    <circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>
                </svg>
                <p className="text-sm font-medium" style={{ color: 'var(--text-2)' }}>
                    Before raising a ticket, check if your question is answered on the{' '}
                    <a href="/membership/support" style={{ color: 'var(--brand)', fontWeight: 700 }}>Member Support info page</a>.
                </p>
            </div>

            <form onSubmit={handleSubmit} style={{ maxWidth: 720 }}>
                <div className="card" style={{ border: '2px solid #111317', boxShadow: '4px 4px 0px 0px #001478' }}>
                    <div className="card-p" style={{ borderBottom: '2px solid #111317' }}>
                        <p className="text-xs font-black uppercase tracking-widest" style={{ color: 'var(--text-2)' }}>Ticket Details</p>
                    </div>
                    <div className="card-p flex flex-col gap-5">
                        {/* From (read-only) */}
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>Name</label>
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
                                    Category <span style={{ color: 'var(--brand)' }}>*</span>
                                </label>
                                <select
                                    className="field-input"
                                    value={form.category}
                                    onChange={e => set('category', e.target.value)}
                                    required
                                >
                                    <option value="">Select a category…</option>
                                    {CATEGORIES.map(c => <option key={c} value={c}>{c}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                    Urgency
                                </label>
                                <div className="flex gap-2">
                                    {['normal', 'high'].map(u => (
                                        <button
                                            key={u}
                                            type="button"
                                            onClick={() => set('urgency', u)}
                                            className="flex-1 text-xs font-bold uppercase py-2.5 rounded-lg"
                                            style={{
                                                border: '2px solid #111317',
                                                background: form.urgency === u ? (u === 'high' ? 'var(--brand)' : '#111317') : '#fff',
                                                color: form.urgency === u ? '#fff' : 'var(--text)',
                                                cursor: 'pointer',
                                                transition: 'background 0.12s',
                                            }}
                                        >
                                            {u === 'normal' ? 'Normal' : 'High'}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        </div>

                        <div>
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                Subject <span style={{ color: 'var(--brand)' }}>*</span>
                            </label>
                            <input
                                className="field-input"
                                placeholder="Brief description of your issue"
                                value={form.subject}
                                onChange={e => set('subject', e.target.value)}
                                required
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                Message <span style={{ color: 'var(--brand)' }}>*</span>
                            </label>
                            <textarea
                                className="field-input"
                                rows={5}
                                placeholder="Describe your issue in detail. Include any error messages, steps you've already tried, or relevant context…"
                                value={form.message}
                                onChange={e => set('message', e.target.value)}
                                required
                                style={{ resize: 'vertical' }}
                            />
                        </div>

                        <div>
                            <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>
                                Attachment (optional)
                            </label>
                            <input
                                type="file"
                                accept=".pdf,.doc,.docx,.png,.jpg,.jpeg"
                                className="field-input"
                                style={{ padding: '8px 12px' }}
                                onChange={e => setAttachment(e.target.files?.[0] ?? null)}
                            />
                            <p className="text-xs mt-1" style={{ color: 'var(--text-3)' }}>PDF, DOC, DOCX, PNG, JPG · max 5 MB</p>
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
                        {submitting ? 'Submitting…' : 'Submit Ticket'}
                    </button>
                    <a href="/dashboard" className="btn btn-ghost btn-lg">Cancel</a>
                </div>

                <p className="text-xs mt-4" style={{ color: 'var(--text-3)' }}>
                    Response sent to <strong>{user.email}</strong> within 48 hours.
                </p>
            </form>
        </div>
    );
}
