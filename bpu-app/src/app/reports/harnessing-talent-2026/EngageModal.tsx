'use client';

import { useState, useEffect, useRef } from 'react';
import { X, PaperPlaneTilt } from '@phosphor-icons/react';

type Status = 'idle' | 'sending' | 'sent' | 'error';

const ENQUIRY_TYPES = [
    'Research Collaboration',
    'Policy Feedback',
    'Media Enquiry',
    'Partnership Interest',
    'Speaking / Events',
    'Other',
];

export default function EngageModal() {
    const [open, setOpen]   = useState(false);
    const [status, setStatus] = useState<Status>('idle');
    const [error, setError]   = useState('');
    const [form, setForm] = useState({
        re_name: '', re_email: '', re_org: '', re_role: '',
        re_enquiry_type: '', re_message: '',
    });
    const dialogRef = useRef<HTMLDivElement>(null);

    // Auto-open when the page is loaded/navigated to with #engage hash
    useEffect(() => {
        if (window.location.hash === '#engage') setOpen(true);
    }, []);

    // Close on Escape
    useEffect(() => {
        function onKey(e: KeyboardEvent) { if (e.key === 'Escape') setOpen(false); }
        if (open) document.addEventListener('keydown', onKey);
        return () => document.removeEventListener('keydown', onKey);
    }, [open]);

    // Lock scroll when open
    useEffect(() => {
        document.body.style.overflow = open ? 'hidden' : '';
        return () => { document.body.style.overflow = ''; };
    }, [open]);

    function set(field: keyof typeof form) {
        return (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) =>
            setForm(f => ({ ...f, [field]: e.target.value }));
    }

    async function submit(e: React.FormEvent) {
        e.preventDefault();
        setStatus('sending');
        setError('');
        try {
            const res = await fetch('/api/report-engage', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(form),
            });
            const data = await res.json().catch(() => ({}));
            if (res.ok) {
                setStatus('sent');
            } else {
                setError(data?.message || 'Something went wrong. Please try again.');
                setStatus('error');
            }
        } catch {
            setError('Network error. Please check your connection and try again.');
            setStatus('error');
        }
    }

    const inputCls = 'w-full px-4 py-3 rounded-xl border-2 border-hp-dark/20 bg-white text-hp-dark font-medium text-sm focus:outline-none focus:border-hp-dark transition-colors';
    const labelCls = 'block text-xs font-black uppercase tracking-wider text-hp-dark/60 mb-1.5';

    return (
        <section id="engage" className="py-20 bg-hp-dark text-white">
            <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
                <p className="text-xs font-black uppercase tracking-widest text-hp-accent">Policy Engagement</p>
                <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight">
                    Engage with BPU on Policy
                </h2>
                <p className="text-white/60 text-sm leading-relaxed max-w-lg mx-auto">
                    Share your interest or enquiry and a member of the BPU team will be in touch.
                </p>
                <button
                    onClick={() => { setStatus('idle'); setError(''); setOpen(true); }}
                    className="inline-flex items-center gap-2 px-8 py-4 bg-hp-accent text-white font-black uppercase tracking-tight text-sm rounded-xl border-brutal shadow-brutal hover:shadow-brutal-lg transition-shadow"
                >
                    <PaperPlaneTilt size={18} weight="bold" />
                    Start Engagement
                </button>
            </div>

            {/* Modal overlay */}
            {open && (
                <div
                    className="fixed inset-0 z-[200] flex items-center justify-center p-4"
                    style={{ background: 'rgba(0,0,0,0.7)' }}
                    onClick={e => { if (!dialogRef.current?.contains(e.target as Node)) setOpen(false); }}
                >
                    <div
                        ref={dialogRef}
                        className="relative w-full max-w-lg bg-hp-bg rounded-2xl border-brutal shadow-brutal overflow-hidden"
                        style={{ maxHeight: '90vh', overflowY: 'auto' }}
                    >
                        {/* Header */}
                        <div className="flex items-center justify-between px-6 pt-6 pb-4 border-b-2 border-hp-dark">
                            <div>
                                <p className="text-xs font-black uppercase tracking-widest text-hp-accent mb-0.5">Report · Policy Engagement</p>
                                <h3 className="text-xl font-black uppercase tracking-tight text-hp-dark">Engage with BPU</h3>
                            </div>
                            <button onClick={() => setOpen(false)} className="p-2 rounded-xl border-brutal shadow-brutal-sm bg-white hover:bg-hp-dark hover:text-white transition-colors" aria-label="Close">
                                <X size={18} weight="bold" />
                            </button>
                        </div>

                        <div className="p-6">
                            {status === 'sent' ? (
                                <div className="text-center py-8 space-y-4">
                                    <div className="w-14 h-14 rounded-full bg-green-100 flex items-center justify-center mx-auto">
                                        <PaperPlaneTilt size={28} weight="bold" className="text-green-600" />
                                    </div>
                                    <p className="font-black uppercase tracking-tight text-hp-dark">Submission received</p>
                                    <p className="text-sm text-text-2 leading-relaxed max-w-xs mx-auto">
                                        Thank you for reaching out. A member of the BPU team will be in touch with you shortly.
                                    </p>
                                    <button onClick={() => setOpen(false)} className="mt-4 px-6 py-3 bg-hp-dark text-white font-black uppercase text-sm rounded-xl border-brutal shadow-brutal">
                                        Close
                                    </button>
                                </div>
                            ) : (
                                <form onSubmit={submit} className="space-y-4">
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label className={labelCls}>Full Name <span className="text-hp-accent">*</span></label>
                                            <input type="text" required value={form.re_name} onChange={set('re_name')} placeholder="Your full name" className={inputCls} />
                                        </div>
                                        <div>
                                            <label className={labelCls}>Email Address <span className="text-hp-accent">*</span></label>
                                            <input type="email" required value={form.re_email} onChange={set('re_email')} placeholder="you@example.com" className={inputCls} />
                                        </div>
                                    </div>
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <label className={labelCls}>Organisation</label>
                                            <input type="text" value={form.re_org} onChange={set('re_org')} placeholder="Your organisation" className={inputCls} />
                                        </div>
                                        <div>
                                            <label className={labelCls}>Role / Job Title</label>
                                            <input type="text" value={form.re_role} onChange={set('re_role')} placeholder="Your role" className={inputCls} />
                                        </div>
                                    </div>
                                    <div>
                                        <label className={labelCls}>Nature of Enquiry <span className="text-hp-accent">*</span></label>
                                        <select required value={form.re_enquiry_type} onChange={set('re_enquiry_type')} className={inputCls}>
                                            <option value="">Select type…</option>
                                            {ENQUIRY_TYPES.map(t => <option key={t} value={t}>{t}</option>)}
                                        </select>
                                    </div>
                                    <div>
                                        <label className={labelCls}>Message <span className="text-hp-accent">*</span></label>
                                        <textarea required rows={4} value={form.re_message} onChange={set('re_message')} placeholder="Describe your enquiry or area of interest…" className={`${inputCls} resize-none`} />
                                    </div>

                                    {status === 'error' && (
                                        <p className="text-sm text-red-600 font-semibold">{error}</p>
                                    )}

                                    <button
                                        type="submit"
                                        disabled={status === 'sending'}
                                        className="w-full bg-hp-dark text-white px-6 py-4 rounded-xl border-brutal shadow-brutal uppercase font-black text-sm disabled:opacity-60 transition-opacity"
                                    >
                                        {status === 'sending' ? 'Sending…' : 'Submit Enquiry'}
                                    </button>
                                </form>
                            )}
                        </div>
                    </div>
                </div>
            )}
        </section>
    );
}
