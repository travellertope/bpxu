import SiteHeader from '@/components/SiteHeader';

export const metadata = {
    title: '#NextGen Apprenticeship Programme | Black Professionals UK',
    description: 'Kickstart your career with the BPU Apprenticeship Programme — practical, hands-on training while earning a wage for young people aged 16–19.',
};

const STATS = [
    { value: '91%', label: 'of apprentices either secure employment' },
    { value: '89%', label: 'remain in sustained employment' },
    { value: '73%', label: 'transition into full-time employment' },
];

const INFO_PANELS = [
    {
        icon: '🔧',
        title: 'How It Works',
        desc: 'Young individuals between the ages of 16 and 19 who are passionate, motivated, and eager to develop their careers. Whether in high school, college, or exploring post-education options, this programme provides real-world experience in a supportive and inclusive environment.',
    },
    {
        icon: '📋',
        title: 'Apprenticeship Areas',
        desc: 'Our apprenticeship programme spans a variety of sectors, including but not limited to: Business Administration, Digital Marketing, Engineering, Finance, Human Resources, Project Management, Technology & IT, Customer Service, Healthcare, Media and Creative Industries.',
    },
    {
        icon: '✅',
        title: 'Who Can Register?',
        desc: 'Young individuals between the ages of 16 and 19 who are passionate, motivated, and eager to develop their careers. Whether in high school, college, or exploring post-education options.',
    },
];

const FAQS = [
    {
        q: 'Do I need a CV?',
        a: 'No stress! A CV is optional. If you have one, upload it. If not, the form will capture all the info we need.',
    },
    {
        q: 'Will I get paid?',
        a: "Yes! All apprenticeships are paid. You'll earn while you learn.",
    },
    {
        q: 'How long is the apprenticeship?',
        a: 'It can last anywhere from a few months to up to 2 years depending on the employer and role.',
    },
];

export default function NextGenPage() {
    return (
        <div className="min-h-screen flex flex-col bg-hp-bg text-hp-dark">
            <SiteHeader />

            <main className="flex-1 pt-20">
                {/* Hero */}
                <section className="py-20 bg-hp-dark text-white">
                    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="inline-block bg-hp-accent text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border-2 border-white mb-6">
                            Apprenticeships
                        </div>
                        <h1 className="text-4xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight leading-none mb-4">
                            #NextGen
                        </h1>
                        <p className="text-lg sm:text-xl font-semibold text-white/80 max-w-2xl mb-6 leading-relaxed">
                            Kickstart Your Career with the BPU Apprenticeship Programme
                        </p>
                        <p className="text-base text-white/70 max-w-3xl leading-relaxed">
                            The BPU Apprenticeship Programme (#NextGen) is a strategic initiative designed to help young talent kick-start their careers through practical, hands-on training while earning a wage. #NextGen aims to close skills gaps, address early access barriers, and build a sustainable pipeline of diverse, future-ready talent.
                        </p>
                    </div>
                </section>

                {/* Stats */}
                <section className="py-16">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            {STATS.map(s => (
                                <div key={s.value} className="p-8 rounded-2xl border-brutal shadow-brutal bg-white text-center space-y-2 interactive-card">
                                    <p className="text-5xl font-black text-hp-accent">{s.value}</p>
                                    <p className="text-sm font-semibold leading-snug text-text-2">{s.label}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Why */}
                <section className="py-12 bg-white border-t-2 border-b-2 border-hp-dark">
                    <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-4">
                            Why are we doing this?
                        </h2>
                        <p className="text-base leading-relaxed text-text-2">
                            Apprenticeships bridge the gap between education and employment, ensuring young people gain skills while contributing meaningfully to the workforce. The statistics above demonstrate the effectiveness of apprenticeships in facilitating successful career transitions.
                        </p>
                    </div>
                </section>

                {/* Info panels */}
                <section className="py-16">
                    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            {INFO_PANELS.map(p => (
                                <div key={p.title} className="p-6 rounded-2xl border-brutal shadow-brutal bg-white space-y-3 interactive-card">
                                    <div className="text-3xl">{p.icon}</div>
                                    <p className="font-black uppercase tracking-tight text-sm">{p.title}</p>
                                    <p className="text-sm text-text-2 leading-relaxed">{p.desc}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* FAQ */}
                <section className="py-16 bg-white border-t-2 border-b-2 border-hp-dark">
                    <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-6">
                            Frequently Asked Questions
                        </h2>
                        <p className="text-text-2 mb-8">If you&apos;ve still got questions about the apprenticeship programme, these could help.</p>
                        <div className="space-y-3">
                            {FAQS.map((faq, i) => (
                                <details
                                    key={i}
                                    className="group rounded-xl border-brutal bg-white overflow-hidden"
                                >
                                    <summary className="flex justify-between items-center px-5 py-4 cursor-pointer font-black uppercase tracking-tight text-sm select-none list-none">
                                        {faq.q}
                                        <span className="text-hp-accent text-lg font-black group-open:rotate-45 transition-transform inline-block">+</span>
                                    </summary>
                                    <div className="px-5 pb-4 text-sm text-text-2 leading-relaxed border-t-2 border-hp-dark pt-3">
                                        {faq.a}
                                    </div>
                                </details>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Application Form */}
                {/* Member Gate */}
                <section className="py-20 bg-hp-dark">
                    <div className="max-w-xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                        <div className="inline-block bg-hp-accent text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border-2 border-white mb-6">
                            Members Only
                        </div>
                        <h2 className="text-3xl sm:text-4xl font-black uppercase tracking-tight text-white mb-4">
                            Ready to Apply?
                        </h2>
                        <p className="text-white/70 text-base mb-8">
                            Sign in to your BPU member account to apply for the #NextGen Apprenticeship Programme. Your profile details will be pre-filled for you.
                        </p>
                        <div className="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="/login?returnTo=/programmes/nextgen"
                                className="inline-block px-8 py-4 bg-hp-accent text-white font-black uppercase tracking-tight text-sm rounded-xl border-brutal shadow-brutal hover:shadow-brutal-lg transition-shadow">
                                Sign In to Apply
                            </a>
                            <a href="/register"
                                className="inline-block px-8 py-4 bg-white text-hp-dark font-black uppercase tracking-tight text-sm rounded-xl border-brutal shadow-brutal hover:shadow-brutal-lg transition-shadow">
                                Become a Member
                            </a>
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
