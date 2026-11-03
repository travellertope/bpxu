'use client';

import { useState } from 'react';
import SiteHeader from '@/components/SiteHeader';
import SiteFooter from '@/components/SiteFooter';

const WP_UPLOADS = 'https://blackprofessionals.uk/wp-content/uploads/2026/05';

const OFFERS = [
    {
        title: 'Culturally informed support',
        desc: 'We provide guidance that respects faith, family dynamics, identity, and lived experience.',
    },
    {
        title: 'For Black professionals',
        desc: 'A dedicated space for Black professionals navigating work, well-being, and life transitions.',
    },
    {
        title: 'Confidential and safe',
        desc: 'Your information is treated with care and only used to respond to your request or connect you with support.',
    },
    {
        title: 'Guidance and signposting',
        desc: 'We do not provide therapy. Instead, we help you understand your options and connect you to appropriate support at the right time.',
    },
];

const NOT_LIST = [
    'Not a crisis or emergency service',
    'Not clinical therapy or diagnosis',
    'Not a substitute for professional mental health care',
];

const INTENT_OPTIONS = [
    'General emotional support',
    'Work-related stress or burnout',
    'Feeling overwhelmed or low',
    'Identity or belonging at work',
    'Looking for information or resources',
    'Not sure. I just need to talk',
];

const SUPPORT_OPTIONS = [
    'Someone to talk to',
    'A referral or signposting',
    'Community or peer support',
    'Not sure yet',
];

export default function SafeSpacePage() {
    const [form, setForm]         = useState({ ss_name: '', ss_phone: '', ss_email: '', ss_intent: '', ss_support_type: '', ss_experience: '', ss_mindful: '' });
    const [submitting, setSubmit] = useState(false);
    const [sent, setSent]         = useState(false);
    const [error, setError]       = useState('');

    function update(field: string, value: string) {
        setForm(f => ({ ...f, [field]: value }));
    }

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setSubmit(true);
        setError('');
        try {
            const res = await fetch('/api/safe-space', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(form),
            });
            if (res.ok) {
                setSent(true);
            } else {
                setError('Something went wrong. Please try again or email us directly.');
            }
        } catch {
            setError('Unable to send. Please check your connection and try again.');
        } finally {
            setSubmit(false);
        }
    }

    return (
        <div className="min-h-screen flex flex-col">
            <SiteHeader />

            <main className="flex-1 pt-20">

                {/* Hero */}
                <section className="bg-hp-bg py-20 border-b-2 border-hp-dark">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                            <div>
                                <p className="text-xs font-black uppercase tracking-widest text-hp-accent mb-4">
                                    Powered by Black Professionals UK
                                </p>
                                <h1 className="text-5xl sm:text-6xl font-black uppercase tracking-tight leading-none mb-6">
                                    Safe Space
                                </h1>
                                <p className="text-xl font-semibold leading-relaxed mb-4">
                                    A confidential signposting and support platform for Black professionals navigating stress, burnout, identity, and emotional wellbeing.
                                </p>
                                <p className="text-gray-600 leading-relaxed">
                                    You do not need to be in crisis to reach out. Sometimes, the right conversation is the first step. This is a space built for you.
                                </p>
                                <a
                                    href="#contact"
                                    className="inline-block mt-8 bg-hp-dark text-white px-8 py-4 rounded-xl border-brutal shadow-brutal hover:shadow-brutal-lg transition-shadow uppercase font-black text-sm"
                                >
                                    Reach Out Confidentially
                                </a>
                            </div>
                            <div>
                                {/* eslint-disable-next-line @next/next/no-img-element */}
                                <img
                                    src={`${WP_UPLOADS}/mental-health-page2-e1779821110220.jpeg`}
                                    alt="Safe Space Support"
                                    className="w-full max-h-[500px] object-cover rounded-2xl border-brutal shadow-brutal"
                                />
                            </div>
                        </div>
                    </div>
                </section>

                {/* What We Offer vs What We Are Not */}
                <section className="py-20 bg-white border-b-2 border-hp-dark">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-16">

                            {/* Offers */}
                            <div>
                                <h2 className="text-2xl font-black uppercase tracking-tight mb-8 pb-4 border-b-2 border-hp-dark">
                                    What Safe Space By BPU Offers
                                </h2>
                                <div className="space-y-6">
                                    {OFFERS.map(({ title, desc }) => (
                                        <div key={title} className="flex gap-4">
                                            <span className="text-green-600 font-black text-xl flex-shrink-0 mt-0.5">✓</span>
                                            <div>
                                                <h3 className="font-black text-base mb-1">{title}</h3>
                                                <p className="text-sm text-gray-600 leading-relaxed">{desc}</p>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>

                            {/* Is Not */}
                            <div>
                                <h2 className="text-2xl font-black uppercase tracking-tight mb-8 pb-4 border-b-2 border-hp-accent text-hp-accent">
                                    What Safe Space By BPU Is Not
                                </h2>
                                <div className="space-y-3">
                                    {NOT_LIST.map(item => (
                                        <div key={item} className="flex items-center gap-4 bg-hp-bg px-5 py-4 rounded-xl border-l-4 border-hp-accent">
                                            <span className="text-hp-accent font-black flex-shrink-0">✕</span>
                                            <p className="font-semibold text-sm">{item}</p>
                                        </div>
                                    ))}
                                </div>
                                <div className="mt-8 p-5 bg-red-50 border border-red-200 rounded-xl text-center">
                                    <p className="text-sm font-semibold text-red-800">
                                        If you are in immediate distress, we will always direct you to urgent support services.
                                    </p>
                                </div>
                            </div>
                        </div>

                        {/* Image banner */}
                        <div className="mt-16">
                            {/* eslint-disable-next-line @next/next/no-img-element */}
                            <img
                                src={`${WP_UPLOADS}/mental-health-page1.png`}
                                alt="Safe Space — Mental Health Support"
                                className="w-full h-64 sm:h-80 object-cover rounded-2xl border-brutal shadow-brutal"
                            />
                        </div>
                    </div>
                </section>

                {/* Contact Form */}
                <section id="contact" className="py-20 bg-hp-bg border-b-2 border-hp-dark">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="grid grid-cols-1 lg:grid-cols-5 gap-16 items-start">

                            {/* Sticky image */}
                            <div className="hidden lg:block lg:col-span-2 lg:sticky lg:top-28">
                                {/* eslint-disable-next-line @next/next/no-img-element */}
                                <img
                                    src={`${WP_UPLOADS}/mental-health-page-1-e1779820943780.jpeg`}
                                    alt="Contact Safe Space"
                                    className="w-full aspect-[4/5] object-cover rounded-2xl border-brutal shadow-brutal"
                                />
                            </div>

                            {/* Form */}
                            <div className="lg:col-span-3 bg-white rounded-2xl border-brutal shadow-brutal p-8 sm:p-10">
                                <div className="mb-8 text-center">
                                    <h2 className="text-3xl font-black uppercase tracking-tight mb-3">Get in Touch</h2>
                                    <p className="text-gray-600">
                                        Tell us a little about what you're experiencing. There's no right or wrong way to answer. A few words is enough. We'll come back to you with guidance and next steps.
                                    </p>
                                </div>

                                {sent ? (
                                    <div className="text-center py-12 space-y-4">
                                        <div className="text-5xl">✓</div>
                                        <h3 className="text-xl font-black uppercase tracking-tight">Request Received</h3>
                                        <p className="text-gray-600">
                                            Thank you for reaching out. Your message has been received confidentially. Someone will be in touch with you shortly.
                                        </p>
                                    </div>
                                ) : (
                                    <form onSubmit={handleSubmit} className="space-y-5">
                                        {/* Name */}
                                        <div>
                                            <label className="block text-sm font-black uppercase tracking-wide mb-1">
                                                Name <span className="text-hp-accent">*</span>
                                            </label>
                                            <input
                                                type="text"
                                                required
                                                placeholder="First name or initials is fine"
                                                value={form.ss_name}
                                                onChange={e => update('ss_name', e.target.value)}
                                                className="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-hp-dark focus:outline-none font-medium text-sm"
                                            />
                                        </div>

                                        {/* Phone */}
                                        <div>
                                            <label className="block text-sm font-black uppercase tracking-wide mb-1">
                                                Phone Number <span className="text-gray-400 font-medium normal-case">(optional)</span>
                                            </label>
                                            <input
                                                type="tel"
                                                placeholder="e.g. 07123 456789"
                                                value={form.ss_phone}
                                                onChange={e => update('ss_phone', e.target.value)}
                                                className="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-hp-dark focus:outline-none font-medium text-sm"
                                            />
                                        </div>

                                        {/* Email */}
                                        <div>
                                            <label className="block text-sm font-black uppercase tracking-wide mb-1">
                                                Email Address <span className="text-hp-accent">*</span>
                                            </label>
                                            <input
                                                type="email"
                                                required
                                                placeholder="For us to respond to you"
                                                value={form.ss_email}
                                                onChange={e => update('ss_email', e.target.value)}
                                                className="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-hp-dark focus:outline-none font-medium text-sm"
                                            />
                                        </div>

                                        {/* Intent */}
                                        <div>
                                            <label className="block text-sm font-black uppercase tracking-wide mb-1">
                                                What best describes what you're looking for right now? <span className="text-hp-accent">*</span>
                                            </label>
                                            <select
                                                required
                                                value={form.ss_intent}
                                                onChange={e => update('ss_intent', e.target.value)}
                                                className="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-hp-dark focus:outline-none font-medium text-sm bg-white"
                                            >
                                                <option value="">Please select…</option>
                                                {INTENT_OPTIONS.map(o => <option key={o} value={o}>{o}</option>)}
                                            </select>
                                        </div>

                                        {/* Support type */}
                                        <div>
                                            <label className="block text-sm font-black uppercase tracking-wide mb-1">
                                                What kind of support would feel most helpful right now? <span className="text-hp-accent">*</span>
                                            </label>
                                            <select
                                                required
                                                value={form.ss_support_type}
                                                onChange={e => update('ss_support_type', e.target.value)}
                                                className="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-hp-dark focus:outline-none font-medium text-sm bg-white"
                                            >
                                                <option value="">Please select…</option>
                                                {SUPPORT_OPTIONS.map(o => <option key={o} value={o}>{o}</option>)}
                                            </select>
                                        </div>

                                        {/* Experience */}
                                        <div>
                                            <label className="block text-sm font-black uppercase tracking-wide mb-1">
                                                Is there anything you'd like us to know about what you're experiencing?
                                            </label>
                                            <p className="text-xs text-gray-500 mb-2">A few words is enough, there's no right or wrong answer.</p>
                                            <textarea
                                                rows={4}
                                                placeholder="Share as much or as little as you like…"
                                                value={form.ss_experience}
                                                onChange={e => update('ss_experience', e.target.value)}
                                                className="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-hp-dark focus:outline-none font-medium text-sm resize-y"
                                            />
                                        </div>

                                        {/* Mindful */}
                                        <div>
                                            <label className="block text-sm font-black uppercase tracking-wide mb-1">
                                                Is there anything we should be mindful of when responding to you?
                                            </label>
                                            <p className="text-xs text-gray-500 mb-2">E.g. faith or spiritual considerations, language preferences, communication needs.</p>
                                            <textarea
                                                rows={3}
                                                placeholder="Optional – share anything that helps us respond thoughtfully"
                                                value={form.ss_mindful}
                                                onChange={e => update('ss_mindful', e.target.value)}
                                                className="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-hp-dark focus:outline-none font-medium text-sm resize-y"
                                            />
                                        </div>

                                        {error && (
                                            <p className="text-sm text-red-600 font-medium">{error}</p>
                                        )}

                                        <button
                                            type="submit"
                                            disabled={submitting}
                                            className="w-full px-8 py-4 bg-hp-dark text-white font-black uppercase tracking-tight text-sm rounded-xl border-brutal shadow-brutal hover:shadow-brutal-lg transition-shadow disabled:opacity-60"
                                        >
                                            {submitting ? 'Sending…' : 'Send Confidential Request'}
                                        </button>

                                        <div className="mt-4 p-4 bg-gray-50 rounded-xl text-xs text-gray-500 leading-relaxed">
                                            <strong>NOTE:</strong> This initiative provides guidance and signposting support. It is not an emergency or crisis service. All submissions are treated as confidential and are used solely to respond to your enquiry and provide appropriate support pathways.
                                        </div>
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
