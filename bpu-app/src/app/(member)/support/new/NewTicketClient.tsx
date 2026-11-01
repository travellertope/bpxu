'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';
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

export default function NewTicketClient({ user }: { user: BPUUser }) {
    const router = useRouter();
    const [form, setForm] = useState({
        category: '',
        subject: '',
        urgency: 'normal',
        message: '',
    });
    const [attachment, setAttachment] = useState<File | null>(null);
    const [submitting, setSubmitting] = useState(false);
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
            const res = await fetch('/api/member/support/tickets', { method: 'POST', body });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to submit');
            router.push(data.id ? `/support/${data.id}` : '/support');
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Something went wrong. Please try again.');
            setSubmitting(false);
        }
    }

    return (
        <div className="dash-page-wrap">
            {/* Breadcrumb */}
            <div className="flex items-center gap-2 mb-6 text-xs font-bold" style={{ color: 'var(--text-3)' }}>
                <a href="/support" style={{ color: 'var(--brand)' }}>Support</a>
                <span>/</span>
                <span>New Ticket</span>
            </div>

            <div className="mb-7">
                <h1 className="font-black uppercase tracking-tight text-2xl sm:text-3xl" style={{ color: '#111317' }}>
                    Raise a Support Ticket
                </h1>
                <p className="text-sm font-medium mt-1" style={{ color: 'var(--text-2)' }}>
                    Our team reviews every ticket personally and responds within 48 hours.
                </p>
            </div>

            <form onSubmit={handleSubmit} style={{ maxWidth: 720 }}>
                <div className="card" style={{ border: '2px solid #111317', boxShadow: '4px 4px 0px 0px #001478' }}>
                    <div className="card-p" style={{ borderBottom: '2px solid #111317' }}>
                        <p className="text-xs font-black uppercase tracking-widest" style={{ color: 'var(--text-2)' }}>Ticket Details</p>
                    </div>
                    <div className="card-p flex flex-col gap-5">
                        {/* From */}
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
                                <select className="field-input" value={form.category} onChange={e => set('category', e.target.value)} required>
                                    <option value="">Select a category…</option>
                                    {CATEGORIES.map(c => <option key={c} value={c}>{c}</option>)}
                                </select>
                            </div>
                            <div>
                                <label className="block text-xs font-extrabold uppercase mb-1.5 tracking-wide" style={{ color: 'var(--text-2)' }}>Urgency</label>
                                <div className="flex gap-2 h-[42px]">
                                    {(['normal', 'high'] as const).map(u => (
                                        <button
                                            key={u}
                                            type="button"
                                            onClick={() => set('urgency', u)}
                                            className="flex-1 text-xs font-bold uppercase rounded-lg"
                                            style={{
                                                border: '2px solid #111317',
                                                background: form.urgency === u ? (u === 'high' ? 'var(--brand)' : '#111317') : '#fff',
                                                color: form.urgency === u ? '#fff' : 'var(--text)',
                                                cursor: 'pointer',
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
                                placeholder="Describe your issue in detail — include any error messages, steps you've already tried, or relevant context…"
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
                    <a href="/support" className="btn btn-ghost btn-lg">Cancel</a>
                </div>

                <p className="text-xs mt-4" style={{ color: 'var(--text-3)' }}>
                    We'll respond to <strong>{user.email}</strong> within 48 hours.
                </p>
            </form>
        </div>
    );
}
