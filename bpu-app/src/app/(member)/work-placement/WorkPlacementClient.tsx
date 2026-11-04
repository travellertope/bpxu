'use client';

import type { BPUUser } from '@/lib/auth';

const PROGRAMMES = [
    {
        href: '/programmes/black-interns',
        label: 'Black Interns',
        tag: 'Students & Graduates',
        desc: 'Paid summer internships with partner organisations across the UK. For undergraduates and recent graduates looking to build real-world experience.',
        cta: 'Apply to Black Interns',
        accent: '#111317',
    },
    {
        href: '/programmes/experienced-talent',
        label: 'Experienced Talent',
        tag: 'Mid-Career Professionals',
        desc: 'Placement and secondment opportunities for professionals with 3+ years of experience who are ready for the next step in their career.',
        cta: 'Apply to Experienced Talent',
        accent: '#111317',
    },
    {
        href: '/programmes/nextgen',
        label: '#NextGen',
        tag: 'Early Career',
        desc: 'For young professionals 1–3 years into their career. Access structured placements, mentoring, and professional development built for growth.',
        cta: 'Apply to #NextGen',
        accent: '#111317',
    },
    {
        href: '/talent-pool',
        label: 'Talent Pool',
        tag: 'Open Registration',
        desc: 'Join the BPU Talent Pool so partner employers can discover and approach you directly for roles, placements, and projects.',
        cta: 'Join the Talent Pool',
        accent: '#111317',
    },
];

export default function WorkPlacementClient({ user }: { user: BPUUser }) {
    const firstName = user.profile?.first_name || user.display_name.split(' ')[0];

    return (
        <div className="dash-page-wrap">
            <div className="mb-8">
                <p className="text-xs font-black uppercase tracking-widest mb-1" style={{ color: 'var(--brand)' }}>
                    Black Interns &amp; Talents Programme
                </p>
                <h1 className="font-black uppercase tracking-tight text-2xl sm:text-3xl" style={{ color: '#111317' }}>
                    Work Placements
                </h1>
                <p className="text-sm font-medium mt-2" style={{ color: 'var(--text-2)', maxWidth: '64ch' }}>
                    Hi {firstName} — choose the programme that best fits your experience and career stage.
                    Each has a dedicated application form with tailored questions.
                </p>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5" style={{ maxWidth: 760 }}>
                {PROGRAMMES.map(prog => (
                    <a
                        key={prog.href}
                        href={prog.href}
                        className="flex flex-col rounded-2xl p-6 group transition-shadow"
                        style={{
                            background: '#fff',
                            border: '2px solid #111317',
                            boxShadow: '4px 4px 0px 0px #001478',
                            textDecoration: 'none',
                            color: 'inherit',
                        }}
                        onMouseEnter={e => (e.currentTarget.style.boxShadow = '6px 6px 0px 0px #001478')}
                        onMouseLeave={e => (e.currentTarget.style.boxShadow = '4px 4px 0px 0px #001478')}
                    >
                        <span
                            className="inline-block self-start text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full mb-4"
                            style={{ background: 'var(--brand-bg)', color: 'var(--brand)', border: '1px solid var(--brand)' }}
                        >
                            {prog.tag}
                        </span>

                        <p className="font-black uppercase tracking-tight text-lg leading-tight mb-2" style={{ color: '#111317' }}>
                            {prog.label}
                        </p>

                        <p className="text-sm leading-relaxed flex-1 mb-5" style={{ color: 'var(--text-2)' }}>
                            {prog.desc}
                        </p>

                        <span
                            className="inline-flex items-center gap-2 text-xs font-black uppercase tracking-wider"
                            style={{ color: '#111317', borderBottom: '2px solid #111317' }}
                        >
                            {prog.cta}
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="3" strokeLinecap="round" strokeLinejoin="round">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </span>
                    </a>
                ))}
            </div>
        </div>
    );
}
