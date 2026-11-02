import SiteHeader from '@/components/SiteHeader';
import SiteFooter from '@/components/SiteFooter';

export const metadata = {
    title: '#NextGen Apprenticeship Programme | Black Professionals UK',
    description: 'Kickstart your career with the BPU Apprenticeship Programme — practical, hands-on training while earning a wage for young people aged 16–19.',
};

const WP = 'https://blackprofessionals.uk/wp-content/uploads';

const STATS = [
    {
        value: '91%',
        label: 'of apprentices either secure employment',
        photo: `${WP}/2025/09/area-1.png`,
    },
    {
        value: '89%',
        label: 'remain in sustained employment',
        photo: `${WP}/2025/09/area-3.png`,
    },
    {
        value: '73%',
        label: 'transition into full-time employment',
        photo: `${WP}/2025/09/area-4.png`,
    },
];

const INFO_PANELS = [
    {
        title: 'How It Works',
        image: `${WP}/elementor/thumbs/photo-group-black-teenage-freshy-student-university-generative-ai_35887-16973-rpvfdhgxvjv0qzan43320a3czx5u5q2zv7rv132lzw.avif`,
        imageAlt: 'Young apprentices working together',
        desc: 'Young individuals between the ages of 16 and 19 who are passionate, motivated, and eager to develop their careers. Whether in high school, college, or exploring post-education options, this programme provides real-world experience in a supportive and inclusive environment.',
        cta: { label: 'Learn More', href: '/membership/nextgen' },
    },
    {
        title: 'Apprenticeship Areas',
        image: `${WP}/elementor/thumbs/Next-Gen-builders-rpvfdhgxvjv0qzan43320a3czx5u5q2zv7rv132lzw.png`,
        imageAlt: 'Apprenticeship programme areas',
        desc: 'Our apprenticeship programme spans a variety of sectors, including but not limited to: Business Administration, Digital Marketing, Engineering, Finance, Human Resources, Project Management, Technology & IT, Customer Service, Healthcare, Media and creative industries.',
        cta: { label: 'Learn More', href: '/membership/nextgen' },
    },
    {
        title: 'Who Can Register?',
        image: `${WP}/elementor/thumbs/program-complete-rpvfdies2dwb2l99ylhokrutlb17df6q7cfcid17to.png`,
        imageAlt: 'Who can register for the programme',
        desc: 'Young individuals between the ages of 16 and 19 who are passionate, motivated, and eager to develop their careers. Whether in high school, college, or exploring post-education options.',
        cta: { label: 'Register Now', href: '/login?returnTo=/programmes/nextgen' },
    },
];

const PARTNER_LOGOS = [
    {
        src: `${WP}/elementor/thumbs/united-kingdom-flag-flag-of-uk-free-vector-rpvfdiekfup8eaauk3chk85w6bxsszqt58z5z6xclc.jpg`,
        alt: 'United Kingdom',
    },
    {
        src: `${WP}/elementor/thumbs/51l6hscPd1L._UF10001000_QL80_-e1754299048363-rpvfdiekfup8eaauk3chk85w6bxsszqt58z5z6xclc.jpg`,
        alt: 'Partner',
    },
    {
        src: `${WP}/elementor/thumbs/360_F_103974715_FhR2b6EuH0XTLGshKVFVrcrGf8WBptwu-rpvfdiekfup8eaauk3chk85w6bxsszqt58z5z6xclc.jpg`,
        alt: 'Partner',
    },
    {
        src: `${WP}/elementor/thumbs/bpau-rpvfdiekfup8eaauk3chk85w6bxsszqt58z5z6xclc.png`,
        alt: 'BPAU',
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
    {
        q: 'How do I get matched?',
        a: 'After you apply, our team reviews your profile and matches you with suitable host organisations based on your skills, interests, and location.',
    },
    {
        q: 'How do I get started?',
        a: 'Create a free BPU member account, complete your profile, and apply through the #NextGen application form. Our team will be in touch within 5 working days.',
    },
];

export default function NextGenPage() {
    return (
        <div className="min-h-screen flex flex-col bg-hp-bg text-hp-dark">
            <SiteHeader />

            <main className="flex-1 pt-20">

                {/* ── Hero ── */}
                <section className="relative overflow-hidden bg-hp-dark text-white">
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img
                        src={`${WP}/elementor/thumbs/Next-Gen-builders-rpvfdhgxvjv0qzan43320a3czx5u5q2zv7rv132lzw.png`}
                        alt=""
                        aria-hidden="true"
                        className="absolute inset-0 w-full h-full object-cover opacity-30"
                    />
                    <div className="relative max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-24 text-center">
                        <div className="inline-block bg-hp-accent text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border-2 border-white mb-6">
                            Apprenticeships
                        </div>
                        <h1 className="text-5xl sm:text-6xl lg:text-7xl font-black uppercase tracking-tight leading-none mb-4">
                            #NextGen
                        </h1>
                        <p className="text-xl sm:text-2xl font-semibold text-white/80 max-w-2xl mx-auto mb-8 leading-relaxed">
                            Kickstart Your Career with the BPU Apprenticeship Programme
                        </p>
                        <div className="max-w-4xl mx-auto bg-white/10 backdrop-blur-sm rounded-2xl border border-white/20 p-8 text-left">
                            <p className="text-base text-white/90 leading-relaxed">
                                The BPU Apprenticeship Programme (#NextGen) is a strategic initiative designed to help young talent kick-start their careers through practical, hands-on training while earning a wage. #NextGen aims to close skills gaps, address early access barriers, and build a sustainable pipeline of diverse, future-ready talent. The programme supports employers in cultivating early-career professionals while offering structured, real-world experience to young people at a pivotal stage in their development.
                            </p>
                        </div>
                    </div>
                </section>

                {/* ── Why section ── */}
                <section className="py-16 bg-white border-b-2 border-hp-dark">
                    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                            <div>
                                <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-4">
                                    Why are we doing this?
                                </h2>
                                <p className="text-base leading-relaxed text-text-2">
                                    Apprenticeships bridge the gap between education and employment, ensuring young people gain skills while contributing meaningfully to the workforce. The statistics below demonstrate the effectiveness of apprenticeships in facilitating successful career transitions.
                                </p>
                            </div>
                            {/* eslint-disable-next-line @next/next/no-img-element */}
                            <div className="rounded-2xl overflow-hidden border-brutal shadow-brutal">
                                <img
                                    src={`${WP}/elementor/thumbs/photo-group-black-teenage-freshy-student-university-generative-ai_35887-16973-rpvfdhgxvjv0qzan43320a3czx5u5q2zv7rv132lzw.avif`}
                                    alt="Young apprentices"
                                    className="w-full object-cover"
                                    style={{ maxHeight: 340 }}
                                />
                            </div>
                        </div>
                    </div>
                </section>

                {/* ── Stats ── */}
                <section className="py-16 bg-hp-dark text-white">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            {STATS.map(s => (
                                <div key={s.value} className="rounded-2xl overflow-hidden border-2 border-white/20 bg-white/5 text-center">
                                    {/* eslint-disable-next-line @next/next/no-img-element */}
                                    <img
                                        src={s.photo}
                                        alt=""
                                        aria-hidden="true"
                                        className="w-full object-cover"
                                        style={{ maxHeight: 200 }}
                                    />
                                    <div className="p-6 space-y-2">
                                        <p className="text-5xl font-black text-hp-accent">{s.value}</p>
                                        <p className="text-sm font-semibold leading-snug text-white/70">{s.label}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* ── Why Black Professionals UK ── */}
                <section className="py-16 bg-hp-dark border-t border-white/10 text-white text-center">
                    <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                        <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight">
                            Why Black Professionals United Kingdom?
                        </h2>
                        <p className="text-base leading-relaxed text-white/70">
                            Black Professionals United Kingdom (BPU) is a dynamic network of Black and other ethnic minority professionals and students. We provide a robust support system for our members while actively engaging and supporting UK businesses in achieving their workforce diversity, inclusion and talent development goals.
                        </p>
                        <a
                            href="/partnership"
                            className="inline-block text-xs font-black uppercase tracking-widest text-hp-accent hover:underline"
                        >
                            Why Partner With Us &rsaquo;
                        </a>
                    </div>
                </section>

                {/* ── Info panels ── */}
                <section className="py-16">
                    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            {INFO_PANELS.map(p => (
                                <div key={p.title} className="rounded-2xl border-brutal shadow-brutal bg-white overflow-hidden flex flex-col interactive-card">
                                    {/* eslint-disable-next-line @next/next/no-img-element */}
                                    <img
                                        src={p.image}
                                        alt={p.imageAlt}
                                        className="w-full object-cover"
                                        style={{ height: 200 }}
                                    />
                                    <div className="p-6 flex flex-col flex-1 space-y-3">
                                        <p className="font-black uppercase tracking-tight text-sm">{p.title}</p>
                                        <p className="text-sm text-text-2 leading-relaxed flex-1">{p.desc}</p>
                                        <a
                                            href={p.cta.href}
                                            className="text-xs font-black uppercase tracking-widest text-hp-accent hover:underline"
                                        >
                                            {p.cta.label} &rsaquo;
                                        </a>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* ── FAQ ── */}
                <section className="py-16 bg-white border-t-2 border-b-2 border-hp-dark">
                    <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-3">
                            Frequently Asked Questions
                        </h2>
                        <p className="text-text-2 mb-8">If you&apos;ve still got questions about the apprenticeship programme, these could help.</p>
                        <div className="space-y-3">
                            {FAQS.map((faq, i) => (
                                <details
                                    key={i}
                                    className="group rounded-xl border-brutal bg-white overflow-hidden"
                                    open={i === 0}
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

                {/* ── CTA ── */}
                <section className="py-16 bg-hp-accent text-white text-center">
                    <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
                        <h2 className="text-3xl sm:text-4xl font-black uppercase tracking-tight leading-tight">
                            Applications now open for 2027 placements
                        </h2>
                        <a
                            href="/login?returnTo=/programmes/nextgen"
                            className="inline-block text-xs font-black uppercase tracking-widest text-white/90 hover:text-white border-b-2 border-white/50 hover:border-white pb-0.5 transition-colors"
                        >
                            Register Now &rsaquo;
                        </a>
                    </div>
                </section>

                {/* ── Partner logos ── */}
                <section className="py-10 bg-hp-dark border-t border-white/10">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="flex flex-wrap items-center justify-center gap-8">
                            {PARTNER_LOGOS.map((logo, i) => (
                                // eslint-disable-next-line @next/next/no-img-element
                                <img
                                    key={i}
                                    src={logo.src}
                                    alt={logo.alt}
                                    className="h-12 w-auto object-contain opacity-80 hover:opacity-100 transition-opacity"
                                />
                            ))}
                        </div>
                    </div>
                </section>

            </main>
                <SiteFooter />
        </div>
    );
}
