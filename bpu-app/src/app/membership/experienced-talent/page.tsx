import SiteHeader from '@/components/SiteHeader';
import ExperiencedTalentForm from './ExperiencedTalentForm';

export const metadata = {
    title: 'Experienced Talent Programme | Black Professionals UK',
    description: 'Helping UK organisations connect with highly skilled Black and ethnic minority professionals ready to contribute from day one.',
};

const BENEFITS = [
    {
        icon: '🎯',
        title: 'Targeted Matching',
        desc: 'We connect you directly with employers actively seeking experienced professionals from diverse backgrounds.',
    },
    {
        icon: '🌍',
        title: 'International Experience Valued',
        desc: 'Your global expertise and qualifications are recognised and celebrated, not overlooked.',
    },
    {
        icon: '🤝',
        title: 'Dedicated Support',
        desc: 'Receive personalised guidance throughout your job search and onboarding journey.',
    },
    {
        icon: '📈',
        title: 'Career Progression',
        desc: 'Access roles that reflect your true level of experience and leadership potential.',
    },
    {
        icon: '🏢',
        title: 'Vetted Employers',
        desc: 'We only work with employers committed to inclusive hiring and fair opportunity.',
    },
    {
        icon: '🔗',
        title: 'Professional Network',
        desc: 'Join a community of Black professionals and build lasting connections across industries.',
    },
];

export default function ExperiencedTalentPage() {
    return (
        <div className="min-h-screen flex flex-col bg-hp-bg text-hp-dark">
            <SiteHeader />

            <main className="flex-1 pt-20">
                {/* Hero */}
                <section className="py-20 bg-hp-dark text-white">
                    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="inline-block bg-hp-accent text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border-2 border-white mb-6">
                            Experienced Professionals
                        </div>
                        <h1 className="text-4xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight leading-none mb-6">
                            Experienced<br />Talent Programme
                        </h1>
                        <p className="text-lg sm:text-xl font-semibold text-white/80 max-w-2xl mb-8 leading-relaxed">
                            Helping UK organisations connect with highly skilled Black and ethnic minority professionals ready to contribute from day one.
                        </p>
                        <p className="text-base text-white/70 max-w-3xl leading-relaxed">
                            Many Black and ethnic minority professionals entering the UK workforce bring deep international experience, qualifications, and leadership potential. However, due to a lack of local work history or networks, their talents are underutilised — leaving a gap between available expertise and the UK's growing demand for skilled talent. This programme bridges that gap.
                        </p>
                    </div>
                </section>

                {/* Benefits */}
                <section className="py-16">
                    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-10 text-center">
                            Why Join the Programme?
                        </h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            {BENEFITS.map(b => (
                                <div key={b.title} className="p-6 rounded-2xl border-brutal shadow-brutal bg-white space-y-3 interactive-card">
                                    <div className="text-3xl">{b.icon}</div>
                                    <p className="font-black uppercase tracking-tight text-sm">{b.title}</p>
                                    <p className="text-sm text-text-2 leading-relaxed">{b.desc}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Eligibility */}
                <section className="py-16 bg-white border-t-2 border-b-2 border-hp-dark">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-8">
                            Eligibility Criteria
                        </h2>
                        <ul className="space-y-4">
                            {[
                                'You identify as Black African, Black Caribbean, Black British, or of another Black ethnic background.',
                                'You have at least 3 years of professional work experience (international experience counts).',
                                'You are based in the United Kingdom or have the right to work full time in the UK.',
                                'You are actively seeking employment or a career transition within the UK job market.',
                            ].map((item, i) => (
                                <li key={i} className="flex items-start gap-4">
                                    <span className="flex-shrink-0 w-8 h-8 rounded-full border-brutal shadow-brutal-sm bg-hp-accent text-white flex items-center justify-center text-sm font-black">
                                        {i + 1}
                                    </span>
                                    <p className="font-medium leading-relaxed pt-1">{item}</p>
                                </li>
                            ))}
                        </ul>
                    </div>
                </section>

                {/* Sectors */}
                <section className="py-16">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                        <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-4">
                            Sectors We Cover
                        </h2>
                        <p className="text-text-2 mb-8">We work with employers across a wide range of industries.</p>
                        <div className="flex flex-wrap justify-center gap-3">
                            {['Finance', 'Technology', 'Consulting', 'Legal', 'Engineering', 'Business Operations', 'Marketing', 'Healthcare', 'Media', 'Education'].map(s => (
                                <span key={s} className="px-4 py-2 rounded-full border-brutal shadow-brutal-sm bg-white text-sm font-black uppercase tracking-wide">
                                    {s}
                                </span>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Application Form */}
                <section className="py-16 bg-hp-dark">
                    <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="text-center mb-10">
                            <h2 className="text-3xl sm:text-4xl font-black uppercase tracking-tight text-white mb-3">
                                Apply Now
                            </h2>
                            <p className="text-white/70 text-base">
                                Complete the form below and our team will review your application and be in touch shortly.
                            </p>
                        </div>
                        <div className="bg-hp-bg rounded-2xl border-brutal shadow-brutal p-6 sm:p-8">
                            <ExperiencedTalentForm />
                        </div>
                    </div>
                </section>
            </main>

            <footer className="py-6 text-center text-xs text-text-3 border-t border-border bg-hp-bg">
                © {new Date().getFullYear()} Black Professionals United ·{' '}
                <a href="/" className="hover:underline">Back to Portal</a>
            </footer>
        </div>
    );
}
