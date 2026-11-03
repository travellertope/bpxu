'use client';

import SiteHeader from '@/components/SiteHeader';
import SiteFooter from '@/components/SiteFooter';
import { useState } from 'react';
import { Target, Buildings, Megaphone, Handshake, Globe, Rocket } from '@phosphor-icons/react';

const WP = 'https://blackprofessionals.uk/wp-content/uploads';

const PARTNERS = [
    { name: 'Barclays',                          href: 'https://www.barclays.co.uk/',          logo: `${WP}/2023/11/Barclays-1.jpg` },
    { name: 'NatWest',                            href: 'https://www.natwest.com/',             logo: `${WP}/2025/07/Natwest.png` },
    { name: 'BlackRock',                          href: 'https://www.blackrock.com/',           logo: `${WP}/2023/11/blackrock-1.jpg` },
    { name: 'BNY',                                href: 'https://www.bny.com/',                 logo: `${WP}/2020/08/bny-log.png` },
    { name: 'Capco',                              href: 'https://www.capco.com/',               logo: `${WP}/2026/08/capco.png` },
    { name: 'FreeAgent',                          href: 'https://www.freeagent.com/en/',        logo: `${WP}/2026/05/free-agent-.png` },
    { name: 'M&G',                                href: 'https://www.mandg.com/',               logo: `${WP}/2026/02/m-and-g.png` },
    { name: 'Walter Scott',                       href: 'https://www.walterscott.com/',         logo: `${WP}/2025/10/walter-scott-1.png` },
    { name: 'Charles River',                      href: 'https://www.criver.com/',              logo: `${WP}/2023/11/charles_river-1.jpg` },
    { name: 'Cairngorms',                         href: 'https://cairngorms.co.uk/',            logo: `${WP}/2023/11/Cairngorms.jpg` },
    { name: 'RSPB',                               href: 'https://www.rspb.org.uk/',             logo: `${WP}/2023/11/RSPB.jpg` },
    { name: 'Scottish National Investment Bank',  href: 'https://www.thebank.scot/',            logo: `${WP}/2023/11/Scottish-National-Investment-Bank.jpg` },
    { name: 'The Skills Network',                 href: 'https://theskillsnetwork.com/',        logo: `${WP}/2023/11/The-Skills-Network.jpg` },
    { name: 'Codebase',                           href: 'https://www.codebase.com/',            logo: `${WP}/2025/03/Codebase.jpg` },
    { name: 'Addleshaw Goddard',                  href: 'https://www.addleshawgoddard.com/en/', logo: `${WP}/2026/05/Addleshaw-Goddard-logo-1-768x512-1.jpg` },
];

const BENEFITS = [
    {
        Icon: Target,
        title: 'Access to Diverse Talent Pool',
        desc: 'Our partners possess privileged access to a diverse and highly skilled talent pool through our dynamic job board and sought-after internship and placement programmes. Connect with exceptional Black professionals from various industries.',
    },
    {
        Icon: Buildings,
        title: 'Organisation Representation',
        desc: "Our partners are displayed and well-represented on our platform. Organisations that engage present their values as diverse and inclusive, demonstrating their unwavering commitment to diversity and social responsibility.",
    },
    {
        Icon: Megaphone,
        title: 'Increased Visibility & Brand Awareness',
        desc: 'Our partnerships provide organisations with increased visibility among our members and the wider professional community. Benefit from extensive exposure through our website, social media platforms, and events.',
    },
    {
        Icon: Handshake,
        title: 'Collaborative Events & Networking',
        desc: 'Forge valuable connections within our community and unlock new business opportunities. Engage in purposeful networking events, connecting with potential clients, partners, and industry leaders.',
    },
    {
        Icon: Globe,
        title: 'Social Impact',
        desc: 'Your partnership with Black Professionals United Kingdom fosters social impact and meaningful change. Showcase your organisation\'s commitment to community and make a positive difference.',
    },
    {
        Icon: Rocket,
        title: 'Collaborative Projects & Initiatives',
        desc: 'Collaborate with BPU on projects and initiatives that address relevant challenges and drive positive change. Together, we can create a more equitable future and foster a diverse and prosperous community.',
    },
];

const ORG_TYPES = [
    'Corporate / Private Sector',
    'Public Sector',
    'Non-profit / Charity',
    'SME / Startup',
    'Education / University',
    'Professional Association',
    'Other',
];

const INTERESTS = [
    'Black Talent Access',
    'Job Board Listing',
    'Event Sponsorship',
    'Internship / Work Placement Hosting',
    'Community Initiatives',
    'Brand Awareness',
    'Other',
];

export default function PartnershipPage() {
    const [orgName, setOrgName]           = useState('');
    const [contactName, setContactName]   = useState('');
    const [email, setEmail]               = useState('');
    const [phone, setPhone]               = useState('');
    const [website, setWebsite]           = useState('');
    const [orgType, setOrgType]           = useState('');
    const [interests, setInterests]       = useState<string[]>([]);
    const [message, setMessage]           = useState('');
    const [submitting, setSubmitting]     = useState(false);
    const [submitted, setSubmitted]       = useState(false);
    const [error, setError]               = useState('');

    function toggleInterest(item: string) {
        setInterests(prev =>
            prev.includes(item) ? prev.filter(i => i !== item) : [...prev, item]
        );
    }

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (!orgName.trim() || !email.trim() || !orgType || interests.length === 0) {
            setError('Please fill in all required fields and select at least one area of interest.');
            return;
        }
        setSubmitting(true);
        setError('');
        try {
            const res = await fetch('/api/partnership', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ org_name: orgName, contact_name: contactName, email, phone, website, org_type: orgType, interests, message }),
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
                            Partner With Us
                        </div>
                        <h1 className="text-4xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight leading-none mb-6">
                            Partnerships
                        </h1>
                        <p className="text-lg sm:text-xl font-semibold text-white/80 max-w-2xl mb-4 leading-relaxed">
                            We are committed to fostering meaningful partnerships that drive diversity, equity, and inclusion in the professional setting.
                        </p>
                        <p className="text-base text-white/70 max-w-3xl leading-relaxed">
                            As our partner, you not only contribute to a stronger and more inclusive community but also gain access to a range of exclusive benefits that contribute to your success stories.
                        </p>
                    </div>
                </section>

                {/* Benefits */}
                <section className="py-16">
                    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-10 text-center">
                            Partnership Benefits
                        </h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            {BENEFITS.map(({ Icon: BenefitIcon, title, desc }) => (
                                <div key={title} className="p-6 rounded-2xl border-brutal shadow-brutal bg-white space-y-3 interactive-card">
                                    <div className="w-10 h-10 rounded-xl bg-hp-accent/10 flex items-center justify-center text-hp-accent">
                                        <BenefitIcon size={22} weight="bold" />
                                    </div>
                                    <p className="font-black uppercase tracking-tight text-sm">{title}</p>
                                    <p className="text-sm text-text-2 leading-relaxed">{desc}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Partner logos */}
                <section className="py-16 bg-hp-bg border-b-2 border-hp-dark">
                    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                        <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-3">
                            Our Partners
                        </h2>
                        <p className="text-text-2 mb-10">Organisations championing diversity and inclusion alongside BPU.</p>
                        <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6 items-center">
                            {PARTNERS.map(p => (
                                <a
                                    key={p.name}
                                    href={p.href}
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    className="flex items-center justify-center p-4 bg-white rounded-xl border-brutal shadow-brutal interactive-card"
                                >
                                    {/* eslint-disable-next-line @next/next/no-img-element */}
                                    <img
                                        src={p.logo}
                                        alt={p.name}
                                        className="h-10 w-auto max-w-full object-contain grayscale hover:grayscale-0 transition-all"
                                    />
                                </a>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Partnership Enquiry Form */}
                <section className="py-20 bg-hp-dark" id="enquiry">
                    <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="inline-block bg-hp-accent text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border-2 border-white mb-6">
                            Get In Touch
                        </div>
                        <h2 className="text-3xl sm:text-4xl font-black uppercase tracking-tight text-white mb-3">
                            Become a Partner
                        </h2>
                        <p className="text-white/70 text-base mb-10">
                            Fill in the form below and a member of our team will be in touch to discuss how we can work together.
                        </p>

                        {submitted ? (
                            <div className="rounded-2xl border-2 border-white p-8 text-center space-y-4">
                                <div className="text-4xl">🎉</div>
                                <h3 className="text-xl font-black uppercase text-white">Enquiry Received!</h3>
                                <p className="text-white/70 text-sm leading-relaxed">
                                    Thank you for reaching out. Our partnerships team will review your enquiry and get back to you shortly.
                                </p>
                            </div>
                        ) : (
                            <form onSubmit={handleSubmit} className="space-y-5">
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-xs font-black uppercase tracking-widest text-white/60 mb-1.5">
                                            Organisation Name <span className="text-hp-accent">*</span>
                                        </label>
                                        <input
                                            type="text"
                                            required
                                            value={orgName}
                                            onChange={e => setOrgName(e.target.value)}
                                            placeholder="Acme Corp"
                                            className="w-full px-4 py-3 rounded-xl bg-white/10 border-2 border-white/20 text-white placeholder-white/30 focus:outline-none focus:border-hp-accent transition-colors text-sm"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-black uppercase tracking-widest text-white/60 mb-1.5">
                                            Contact Name
                                        </label>
                                        <input
                                            type="text"
                                            value={contactName}
                                            onChange={e => setContactName(e.target.value)}
                                            placeholder="Jane Smith"
                                            className="w-full px-4 py-3 rounded-xl bg-white/10 border-2 border-white/20 text-white placeholder-white/30 focus:outline-none focus:border-hp-accent transition-colors text-sm"
                                        />
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-xs font-black uppercase tracking-widest text-white/60 mb-1.5">
                                            Email Address <span className="text-hp-accent">*</span>
                                        </label>
                                        <input
                                            type="email"
                                            required
                                            value={email}
                                            onChange={e => setEmail(e.target.value)}
                                            placeholder="jane@acme.com"
                                            className="w-full px-4 py-3 rounded-xl bg-white/10 border-2 border-white/20 text-white placeholder-white/30 focus:outline-none focus:border-hp-accent transition-colors text-sm"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-black uppercase tracking-widest text-white/60 mb-1.5">
                                            Phone Number
                                        </label>
                                        <input
                                            type="tel"
                                            value={phone}
                                            onChange={e => setPhone(e.target.value)}
                                            placeholder="+44 7700 000000"
                                            className="w-full px-4 py-3 rounded-xl bg-white/10 border-2 border-white/20 text-white placeholder-white/30 focus:outline-none focus:border-hp-accent transition-colors text-sm"
                                        />
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label className="block text-xs font-black uppercase tracking-widest text-white/60 mb-1.5">
                                            Website
                                        </label>
                                        <input
                                            type="url"
                                            value={website}
                                            onChange={e => setWebsite(e.target.value)}
                                            placeholder="https://acme.com"
                                            className="w-full px-4 py-3 rounded-xl bg-white/10 border-2 border-white/20 text-white placeholder-white/30 focus:outline-none focus:border-hp-accent transition-colors text-sm"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs font-black uppercase tracking-widest text-white/60 mb-1.5">
                                            Organisation Type <span className="text-hp-accent">*</span>
                                        </label>
                                        <select
                                            required
                                            value={orgType}
                                            onChange={e => setOrgType(e.target.value)}
                                            className="w-full px-4 py-3 rounded-xl bg-white/10 border-2 border-white/20 text-white focus:outline-none focus:border-hp-accent transition-colors text-sm appearance-none"
                                            style={{ colorScheme: 'dark' }}
                                        >
                                            <option value="" className="bg-hp-dark">Select…</option>
                                            {ORG_TYPES.map(t => (
                                                <option key={t} value={t} className="bg-hp-dark">{t}</option>
                                            ))}
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-xs font-black uppercase tracking-widest text-white/60 mb-2">
                                        Areas of Interest <span className="text-hp-accent">*</span>
                                    </label>
                                    <div className="flex flex-wrap gap-2">
                                        {INTERESTS.map(item => (
                                            <button
                                                key={item}
                                                type="button"
                                                onClick={() => toggleInterest(item)}
                                                className={`px-3 py-1.5 rounded-full text-xs font-black uppercase tracking-wide border-2 transition-colors ${
                                                    interests.includes(item)
                                                        ? 'bg-hp-accent border-hp-accent text-white'
                                                        : 'bg-transparent border-white/30 text-white/60 hover:border-white/60'
                                                }`}
                                            >
                                                {item}
                                            </button>
                                        ))}
                                    </div>
                                </div>

                                <div>
                                    <label className="block text-xs font-black uppercase tracking-widest text-white/60 mb-1.5">
                                        Additional Message
                                    </label>
                                    <textarea
                                        rows={4}
                                        value={message}
                                        onChange={e => setMessage(e.target.value)}
                                        placeholder="Tell us more about your organisation and what you're hoping to achieve through the partnership…"
                                        className="w-full px-4 py-3 rounded-xl bg-white/10 border-2 border-white/20 text-white placeholder-white/30 focus:outline-none focus:border-hp-accent transition-colors text-sm resize-none"
                                    />
                                </div>

                                {error && (
                                    <p className="text-sm text-red-400 font-semibold">{error}</p>
                                )}

                                <button
                                    type="submit"
                                    disabled={submitting}
                                    className="w-full px-8 py-4 bg-hp-accent text-white font-black uppercase tracking-tight text-sm rounded-xl border-brutal shadow-brutal hover:shadow-brutal-lg transition-shadow disabled:opacity-60"
                                >
                                    {submitting ? 'Sending…' : 'Submit Partnership Enquiry'}
                                </button>
                            </form>
                        )}
                    </div>
                </section>

                {/* You May Be Interested In */}
                <section className="py-16 bg-white border-t-2 border-b-2 border-hp-dark">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-8 text-center">
                            You May Be Interested In…
                        </h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div className="p-6 rounded-2xl border-brutal shadow-brutal bg-hp-bg space-y-3">
                                <p className="font-black uppercase tracking-tight">Black Talent Access</p>
                                <p className="text-sm text-text-2 leading-relaxed">
                                    Through this programme, we provide organisations with unparalleled access to a pool of skilled Black talents and interns. Talent knows no bounds.
                                </p>
                                <a href="/membership/talent-access" className="inline-block text-sm font-black uppercase tracking-wide text-hp-accent hover:underline">
                                    Learn More →
                                </a>
                            </div>
                            <div className="p-6 rounded-2xl border-brutal shadow-brutal bg-hp-bg space-y-3">
                                <p className="font-black uppercase tracking-tight">Post a Job</p>
                                <p className="text-sm text-text-2 leading-relaxed">
                                    Tap into our rapidly growing network of skilled Black professionals who bring unique perspectives, experiences, and talents to your organisation.
                                </p>
                                <a href="/employer/jobs" className="inline-block text-sm font-black uppercase tracking-wide text-hp-accent hover:underline">
                                    Learn More →
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </main>
                <SiteFooter />
        </div>
    );
}
