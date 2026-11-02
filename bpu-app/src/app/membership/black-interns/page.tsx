import SiteHeader from '@/components/SiteHeader';
import BlackInternsForm from './BlackInternsForm';

export const metadata = {
    title: 'Black Interns Programme | Black Professionals UK',
    description: 'Breaking down the barriers often encountered by individuals from Black and other ethnic minority backgrounds when seeking opportunities within the UK workforce.',
};

const WHY_JOIN = [
    { icon: '🏢', title: 'Valuable Work Experience', desc: 'Get hands-on experience and exposure to real-world work scenarios, enriching your academic knowledge.' },
    { icon: '📈', title: 'Skill Development', desc: 'Access workshops and training sessions to develop essential skills needed for professional success.' },
    { icon: '🗺️', title: 'Structured Career Transition', desc: 'Our programme is designed to offer a smooth transition from academic life to employment.' },
    { icon: '✅', title: 'Enhanced Employability', desc: 'The practical experience gained through our programme enhances your employability to potential employers.' },
    { icon: '🎯', title: 'Targeted Opportunity Matching', desc: 'We connect you with host organisations that seek professionals with your skillset, ensuring relevance and compatibility.' },
    { icon: '🌟', title: 'Demonstrate Your Expertise', desc: 'Showcase your capabilities to potential employers and gain valuable UK work experience.' },
];

const ELIGIBILITY = [
    { title: 'Academic Background', desc: 'Applicants should be students from the Black and other ethnic minority communities pursuing undergraduate or postgraduate studies in the UK.' },
    { title: 'Lack of UK Work Experience', desc: "Eligible applicants must have limited or no work experience in the UK, making them eligible for the programme's support in gaining UK work exposure." },
    { title: 'Commitment to Career Development', desc: 'Candidates should demonstrate a strong commitment to their career development and a desire to excel in their chosen field.' },
    { title: 'Right to Work', desc: 'Applicants must possess the appropriate right-to-work documents in the UK, covering a minimum of 3–12 months without restrictions.' },
];

export default function BlackInternsPage() {
    return (
        <div className="min-h-screen flex flex-col bg-hp-bg text-hp-dark">
            <SiteHeader />

            <main className="flex-1 pt-20">
                {/* Hero */}
                <section className="py-20 bg-hp-dark text-white">
                    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="inline-block bg-hp-accent text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border-2 border-white mb-6">
                            Early Career
                        </div>
                        <h1 className="text-4xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight leading-none mb-6">
                            Black Interns<br />Programme
                        </h1>
                        <p className="text-lg sm:text-xl font-semibold text-white/80 max-w-2xl mb-6 leading-relaxed">
                            Breaking down the barriers often encountered by individuals from Black and other ethnic minority backgrounds when seeking opportunities within the UK workforce.
                        </p>
                        <p className="text-base text-white/70 max-w-3xl leading-relaxed">
                            The goal of the Black Interns Programme is to ensure that individuals from all ethnic minority backgrounds have fair and equal access to career opportunities. By doing so, we aim to foster a diverse and inclusive workforce that reflects the richness of our society.
                        </p>
                    </div>
                </section>

                {/* Video */}
                <section className="py-16 bg-white border-b-2 border-hp-dark">
                    <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                        <p className="text-xs font-black uppercase tracking-widest mb-4 text-center">Hear from our interns</p>
                        <div className="relative w-full" style={{ paddingBottom: '56.25%' }}>
                            <iframe
                                src="https://www.youtube.com/embed/t5aEONuJNhw"
                                title="Black Interns Programme testimonials"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowFullScreen
                                className="absolute inset-0 w-full h-full rounded-2xl border-brutal shadow-brutal"
                            />
                        </div>
                    </div>
                </section>

                {/* Why Join */}
                <section className="py-16">
                    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-10 text-center">
                            Why Join the Programme?
                        </h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            {WHY_JOIN.map(b => (
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
                            {ELIGIBILITY.map((item, i) => (
                                <li key={i} className="flex items-start gap-4">
                                    <span className="flex-shrink-0 w-8 h-8 rounded-full border-brutal shadow-brutal-sm bg-hp-accent text-white flex items-center justify-center text-sm font-black">
                                        {i + 1}
                                    </span>
                                    <div className="pt-1">
                                        <p className="font-black text-sm uppercase tracking-tight mb-1">{item.title}</p>
                                        <p className="font-medium text-sm leading-relaxed text-text-2">{item.desc}</p>
                                    </div>
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
                        <p className="text-text-2 mb-8">We connect interns with employers across a wide range of industries.</p>
                        <div className="flex flex-wrap justify-center gap-3">
                            {['Finance', 'Technology', 'Consulting', 'Media', 'Engineering', 'Law', 'Marketing', 'Healthcare'].map(s => (
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
                            <BlackInternsForm />
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
