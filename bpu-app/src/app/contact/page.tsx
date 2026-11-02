'use client';

import SiteHeader from '@/components/SiteHeader';
import SiteFooter from '@/components/SiteFooter';
import { useState } from 'react';
import { Lightbulb, Handshake, Globe, Rocket } from '@phosphor-icons/react';

const CONTACT_REASONS = [
    { Icon: Lightbulb, text: 'Share an innovative idea to empower Black professionals' },
    { Icon: Handshake, text: 'Reach out for support on your professional journey' },
    { Icon: Globe, text: 'Connect with industry leaders and like-minded peers' },
    { Icon: Rocket, text: 'Join a community that celebrates your uniqueness' },
];

const SUBJECTS = [
    'General Enquiry',
    'Membership',
    'Partnerships',
    'Job Board / Hiring',
    'Events',
    'Technical Support',
    'Press / Media',
    'Other',
];

export default function ContactPage() {
    const [name, setName]         = useState('');
    const [email, setEmail]       = useState('');
    const [subject, setSubject]   = useState('');
    const [message, setMessage]   = useState('');
    const [submitting, setSubmitting] = useState(false);
    const [submitted, setSubmitted]   = useState(false);
    const [error, setError]           = useState('');

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (!name.trim() || !email.trim() || !message.trim()) {
            setError('Please fill in all required fields.');
            return;
        }
        setSubmitting(true);
        setError('');
        try {
            const res = await fetch('/api/contact', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ name, email, subject, message }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Submission failed');
            setSubmitted(true);
        } catch (err) {
            setError(err instanceof Error ? err.message : 'Something went wrong. Please try again.');
        } finally {
            setSubmitting(false);
        }
    }

    return (
        <div className="min-h-screen flex flex-col bg-hp-bg text-hp-dark">
            <SiteHeader />

            <main className="flex-1 pt-20">
                {/* Hero */}
                <section className="py-20 bg-hp-dark text-white">
                    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="inline-block bg-hp-accent text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border-2 border-white mb-6">
                            Get In Touch
                        </div>
                        <h1 className="text-4xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight leading-none mb-6">
                            Contact Us
                        </h1>
                        <p className="text-lg sm:text-xl font-semibold text-white/80 max-w-2xl leading-relaxed">
                            Got a brilliant idea? Need a helping hand on your professional journey? Want to expand your circle? We're here.
                        </p>
                    </div>
                </section>

                {/* Reasons to contact */}
                <section className="py-16 bg-white border-b-2 border-hp-dark">
                    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                            {CONTACT_REASONS.map(({ Icon: ReasonIcon, text }) => (
                                <div key={text} className="p-5 rounded-2xl border-brutal shadow-brutal bg-hp-bg space-y-3">
                                    <div className="w-10 h-10 rounded-xl bg-hp-accent/10 flex items-center justify-center text-hp-accent">
                                        <ReasonIcon size={22} weight="bold" />
                                    </div>
                                    <p className="text-sm font-semibold text-text-2 leading-snug">{text}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Form + contact details */}
                <section className="py-20">
                    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="grid grid-cols-1 lg:grid-cols-3 gap-12">

                            {/* Contact details */}
                            <div className="space-y-8">
                                <div>
                                    <h2 className="text-xl font-black uppercase tracking-tight mb-4">Our Details</h2>
                                    <div className="space-y-4">
                                        <div>
                                            <p className="text-xs font-black uppercase tracking-widest text-text-3 mb-1">Email</p>
                                            <a href="mailto:info@blackprofessionals.uk" className="text-sm font-semibold text-hp-accent hover:underline break-all">
                                                info@blackprofessionals.uk
                                            </a>
                                        </div>
                                        <div>
                                            <p className="text-xs font-black uppercase tracking-widest text-text-3 mb-1">Address</p>
                                            <p className="text-sm text-text-2 leading-relaxed">
                                                5 South Charlotte Street<br />
                                                Edinburgh EH2 4AN<br />
                                                United Kingdom
                                            </p>
                                        </div>
                                        <div>
                                            <p className="text-xs font-black uppercase tracking-widest text-text-3 mb-1">Follow Us</p>
                                            <div className="flex gap-3 mt-1">
                                                <a href="https://linkedin.com/company/blackprofessionalsuk" target="_blank" rel="noreferrer" className="text-sm font-black uppercase tracking-wide text-hp-accent hover:underline">LinkedIn</a>
                                                <a href="https://instagram.com/bprofessionals_uk" target="_blank" rel="noreferrer" className="text-sm font-black uppercase tracking-wide text-hp-accent hover:underline">Instagram</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div className="p-5 rounded-2xl border-brutal shadow-brutal bg-hp-dark text-white space-y-2">
                                    <p className="text-xs font-black uppercase tracking-widest text-white/50">Member Support</p>
                                    <p className="text-sm text-white/80 leading-relaxed">
                                        Already a member? For faster support, log in and use the Member Support section in your dashboard.
                                    </p>
                                    <a href="/support" className="inline-block text-sm font-black uppercase tracking-wide text-hp-accent hover:underline">
                                        Member Support →
                                    </a>
                                </div>
                            </div>

                            {/* Form */}
                            <div className="lg:col-span-2">
                                <h2 className="text-xl font-black uppercase tracking-tight mb-6">Send Us a Message</h2>

                                {submitted ? (
                                    <div className="rounded-2xl border-brutal shadow-brutal p-8 text-center space-y-4 bg-white">
                                        <div className="text-4xl">✅</div>
                                        <h3 className="text-xl font-black uppercase">Message Sent!</h3>
                                        <p className="text-text-2 text-sm leading-relaxed">
                                            Thanks for reaching out. We'll get back to you as soon as possible.
                                        </p>
                                    </div>
                                ) : (
                                    <form onSubmit={handleSubmit} className="space-y-4">
                                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                            <div>
                                                <label className="block text-xs font-black uppercase tracking-widest text-text-3 mb-1.5">
                                                    Your Name <span className="text-hp-accent">*</span>
                                                </label>
                                                <input
                                                    type="text"
                                                    required
                                                    value={name}
                                                    onChange={e => setName(e.target.value)}
                                                    placeholder="Jane Smith"
                                                    className="w-full px-4 py-3 rounded-xl border-brutal bg-white text-hp-dark placeholder-text-3 focus:outline-none focus:ring-2 focus:ring-hp-accent text-sm"
                                                />
                                            </div>
                                            <div>
                                                <label className="block text-xs font-black uppercase tracking-widest text-text-3 mb-1.5">
                                                    Email Address <span className="text-hp-accent">*</span>
                                                </label>
                                                <input
                                                    type="email"
                                                    required
                                                    value={email}
                                                    onChange={e => setEmail(e.target.value)}
                                                    placeholder="jane@example.com"
                                                    className="w-full px-4 py-3 rounded-xl border-brutal bg-white text-hp-dark placeholder-text-3 focus:outline-none focus:ring-2 focus:ring-hp-accent text-sm"
                                                />
                                            </div>
                                        </div>

                                        <div>
                                            <label className="block text-xs font-black uppercase tracking-widest text-text-3 mb-1.5">
                                                Subject
                                            </label>
                                            <select
                                                value={subject}
                                                onChange={e => setSubject(e.target.value)}
                                                className="w-full px-4 py-3 rounded-xl border-brutal bg-white text-hp-dark focus:outline-none focus:ring-2 focus:ring-hp-accent text-sm appearance-none"
                                            >
                                                <option value="">Select a topic…</option>
                                                {SUBJECTS.map(s => <option key={s} value={s}>{s}</option>)}
                                            </select>
                                        </div>

                                        <div>
                                            <label className="block text-xs font-black uppercase tracking-widest text-text-3 mb-1.5">
                                                Message <span className="text-hp-accent">*</span>
                                            </label>
                                            <textarea
                                                required
                                                rows={6}
                                                value={message}
                                                onChange={e => setMessage(e.target.value)}
                                                placeholder="Tell us how we can help…"
                                                className="w-full px-4 py-3 rounded-xl border-brutal bg-white text-hp-dark placeholder-text-3 focus:outline-none focus:ring-2 focus:ring-hp-accent text-sm resize-none"
                                            />
                                        </div>

                                        {error && <p className="text-sm text-red-500 font-semibold">{error}</p>}

                                        <button
                                            type="submit"
                                            disabled={submitting}
                                            className="px-8 py-4 bg-hp-dark text-white font-black uppercase tracking-tight text-sm rounded-xl border-brutal shadow-brutal hover:shadow-brutal-lg transition-shadow disabled:opacity-60"
                                        >
                                            {submitting ? 'Sending…' : 'Send Message'}
                                        </button>
                                    </form>
                                )}
                            </div>
                        </div>
                    </div>
                </section>
            </main>
                <SiteFooter />
        </div>
    );
}
