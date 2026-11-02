import { getBPUSession } from '@/lib/auth';
import SiteHeader from '@/components/SiteHeader';
import SiteFooter from '@/components/SiteFooter';
import { Buildings, TrendUp, Compass, CheckCircle, Target, Star } from '@phosphor-icons/react/dist/ssr';

export const metadata = {
    title: 'Work Placements | Black Professionals UK',
    description: 'Combining the Black Interns Programme and the Black Talents Programme — paid UK work placements for Black professionals at leading organisations.',
};

const PROGRAMMES = [
    {
        id: 'interns',
        badge: 'Early Career',
        title: 'Black Interns Programme',
        body: 'Our Black Interns Programme connects students and early-career professionals with meaningful internship opportunities across a wide range of industries. The programme provides participants with practical work experience, exposure to real business environments, and the opportunity to develop key professional skills.',
        sectors: 'Finance, Technology, Consulting, Media, Engineering, Law, Marketing and more.',
        image: 'https://blackprofessionals.uk/wp-content/uploads/elementor/thumbs/black-interns-rpvfdhgxvjv0qzan43320a3czx5u5q2zv7rv132lzw.jpg',
        imageAlt: 'Black Interns Programme',
        cta: { label: 'Apply Now', href: '/membership/black-interns' },
        video: 'https://www.youtube.com/embed/t5aEONuJNhw',
        flip: false,
    },
    {
        id: 'talents',
        badge: 'Experienced Professionals',
        title: 'Experienced Talent Programme',
        body: 'Our Experienced Talent Programme supports professionals with proven experience who are looking to take the next step in their careers. Through strategic collaborations with leading organisations, we connect skilled professionals with mid-level and senior roles.',
        sectors: 'Finance, Technology, Consulting, Legal, Engineering, Business Operations and more.',
        image: 'https://blackprofessionals.uk/wp-content/uploads/elementor/thumbs/black-talents-rpvfdhgxvjv0qzan43320a3czx5u5q2zv7rv132lzw.jpg',
        imageAlt: 'Experienced Talent Programme',
        cta: { label: 'Apply Now', href: '/membership/experienced-talent' },
        video: null,
        flip: true,
    },
    {
        id: 'apprenticeships',
        badge: 'Apprenticeships',
        title: 'Apprenticeship Programme',
        body: 'Our apprenticeship programme spans a variety of sectors, providing structured pathways into employment with full employer support throughout.',
        sectors: 'Business Administration, Digital Marketing, Engineering, Finance, Human Resources, Project Management, Technology & IT, Customer Service, Healthcare, Media and Creative Industries.',
        image: 'https://blackprofessionals.uk/wp-content/uploads/elementor/thumbs/Next-Gen-builders-rpvfdhgxvjv0qzan43320a3czx5u5q2zv7rv132lzw.png',
        imageAlt: 'Apprenticeship Programme – Next Gen Builders',
        cta: { label: 'Apply Now', href: '/membership/nextgen' },
        video: null,
        flip: false,
    },
    {
        id: 'talent-access',
        badge: 'For Organisations',
        title: 'BPU Talent Access',
        body: 'Visionary organisations seeking fresh perspectives and diverse talent can access our full pool of skilled Black and ethnic minority professionals and interns. We connect forward-thinking employers with candidates across all three of our programmes.',
        sectors: 'Finance, Technology, Consulting, Legal, Engineering, Media, Marketing, Healthcare and more.',
        image: 'https://blackprofessionals.uk/wp-content/uploads/elementor/thumbs/black-talents-rpvfdhgxvjv0qzan43320a3czx5u5q2zv7rv132lzw.jpg',
        imageAlt: 'BPU Talent Access – For Organisations',
        cta: { label: 'Register Interest', href: '/membership/talent-access' },
        video: null,
        flip: true,
    },
];

const WHY_JOIN = [
    { Icon: Buildings, title: 'Valuable Work Experience', desc: 'Get hands-on experience and exposure to real-world work scenarios, enriching your academic knowledge.' },
    { Icon: TrendUp, title: 'Skill Development', desc: 'Access workshops and training sessions to develop essential skills needed for professional success.' },
    { Icon: Compass, title: 'Structured Career Transition', desc: 'Our programme is designed to offer a smooth transition from academic life to employment, setting you on the right path for your career.' },
    { Icon: CheckCircle, title: 'Enhanced Employability', desc: 'The practical experience gained through our programme enhances your employability and marketability to potential employers.' },
    { Icon: Target, title: 'Targeted Opportunity Matching', desc: 'We connect you with host organisations that seek professionals with your skillset, ensuring relevance and compatibility.' },
    { Icon: Star, title: 'Demonstrate Your Expertise', desc: 'Showcase your capabilities to potential employers and gain valuable UK work experience to enhance your profile.' },
];

const ELIGIBILITY = {
    interns: [
        { title: 'Academic Background', desc: 'Applicants should be students from the Black and other ethnic minority communities pursuing undergraduate or postgraduate studies in the UK.' },
        { title: 'Lack of UK Work Experience', desc: 'Eligible applicants must have limited or no work experience in the UK, making them eligible for the programme\'s support in gaining UK work exposure.' },
        { title: 'Commitment to Career Development', desc: 'Candidates should demonstrate a strong commitment to their career development and a desire to excel in their chosen field.' },
        { title: 'Right to Work', desc: 'Applicants must possess the appropriate right-to-work documents in the UK, covering a minimum of 3–12 months without restrictions.' },
    ],
    talents: [
        { title: 'Skilled Professionals Only', desc: 'Eligible applicants are Black and other ethnic minority professionals who possess relevant skills, qualifications, and work experience.' },
        { title: 'International Work Experience', desc: 'Applicants must have gained their professional experience in their home country or other international settings outside the UK.' },
        { title: 'Right to Work', desc: 'Applicants must possess the appropriate right-to-work documents in the UK without restrictions on the type of employment they can undertake.' },
    ],
};

const TESTIMONIALS = [
    {
        name: 'Olabisi Oloyede',
        photo: 'https://blackprofessionals.uk/wp-content/uploads/2025/09/IMG_0178.png',
        role: 'Post-Production Intern, BBC Scotland',
        quote: 'I first discovered Black Professional UK (BPU) in January 2024 when a friend shared a BBC Scotland post-production internship. BPU played a crucial role, not only connecting me to the opportunity but also organising a mock interview that helped me secure the role. My experience at BBC Scotland was incredible, offering exposure to cutting-edge technology and talented professionals. After the internship, I was offered another BBC opportunity. My advice to future interns: speak up and share your unique insights — they matter.',
    },
    {
        name: 'Oluwakemi Akiode',
        photo: 'https://blackprofessionals.uk/wp-content/uploads/2025/09/IMG_0179-e1758815065564.jpg',
        role: 'IT Analyst Intern, Tesco Bank',
        quote: 'I interned with Tesco Bank as an IT Analyst between June and December 2022. I must commend the team for providing a valuable pre-interview session where they guided us through expectations and shared tips on how to approach the interview with confidence. As an international student from Nigeria, navigating the UK job market felt daunting at first. The programme provided me with invaluable skills in systems analysis, problem-solving, and teamwork. Today I\'m proud to continue my career in service management, and grateful for the foundation the internship gave me.',
    },
    {
        name: 'Emmanuella Adefuye',
        photo: 'https://blackprofessionals.uk/wp-content/uploads/2025/09/IMG_0180.jpg',
        role: 'Colleague Outcomes Intern → Permanent, Tesco Bank',
        quote: 'In October 2023, BPU reached out to me and said they had an internship opportunity and thought my skills would match. This internship turned out to be a role as a Colleague Outcomes Intern at Tesco Bank. BPU kept in contact with me throughout the 3 months and I was given weekly support. At the end of my experience, these skills helped me secure a permanent position at Tesco Bank. My advice: do not give up at the first hurdle and make the most of any opportunity you have.',
    },
];

export default async function WorkPlacementPage() {
    const session = await getBPUSession();
    const isAuth = session.authenticated;

    return (
        <div className="min-h-screen flex flex-col bg-hp-bg text-hp-dark">
            <SiteHeader />

            <main className="flex-1 pt-20">

                {/* ── Hero ── */}
                <section className="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                        <div className="space-y-6">
                            <div className="inline-flex items-center gap-2 border-brutal rounded-full px-4 py-2 bg-white shadow-brutal-sm">
                                <div className="w-3 h-3 bg-hp-accent rounded-full" />
                                <span className="font-bold text-xs uppercase tracking-widest">Black Interns &amp; Talents Programme</span>
                            </div>
                            <h1 className="font-black uppercase tracking-tighter leading-[0.9]" style={{ fontSize: 'clamp(3rem,7vw,5rem)' }}>
                                WORK<br /><span className="text-hp-accent">PLACEMENTS</span>
                            </h1>
                            <p className="text-lg font-medium leading-relaxed border-l-4 border-hp-accent pl-4 max-w-lg">
                                Work experience from your home country is a valuable asset, but the lack of UK work experience can be a barrier to professional growth. That&rsquo;s why we combined the best of two groundbreaking initiatives — the Black Interns Programme and the Black Talents Programme — to create a transformative experience that shapes the trajectory of your future.
                            </p>
                            <div className="flex flex-wrap gap-3 pt-2">
                                {isAuth ? (
                                    <a
                                        href="/work-placement"
                                        className="bg-hp-dark text-white px-8 py-4 rounded-xl border-brutal shadow-brutal interactive-card uppercase font-black text-sm"
                                    >
                                        Apply Now
                                    </a>
                                ) : (
                                    <>
                                        <a
                                            href="/login?returnTo=/work-placement"
                                            className="bg-hp-dark text-white px-8 py-4 rounded-xl border-brutal shadow-brutal interactive-card uppercase font-black text-sm"
                                        >
                                            Sign in to Apply
                                        </a>
                                        <a
                                            href="/register"
                                            className="bg-white px-8 py-4 rounded-xl border-brutal shadow-brutal-sm hover:shadow-brutal interactive-card uppercase font-black text-sm transition-all"
                                        >
                                            Create Free Account
                                        </a>
                                    </>
                                )}
                            </div>
                        </div>

                        {/* eslint-disable-next-line @next/next/no-img-element */}
                        <div className="relative rounded-2xl overflow-hidden border-brutal shadow-brutal" style={{ minHeight: 360 }}>
                            <img
                                src="https://blackprofessionals.uk/wp-content/uploads/elementor/thumbs/black-interns-rpvfdhgxvjv0qzan43320a3czx5u5q2zv7rv132lzw.jpg"
                                alt="Black Interns Programme"
                                className="w-full h-full object-cover"
                                style={{ minHeight: 360 }}
                            />
                        </div>
                    </div>
                </section>

                {/* ── Programme Sections ── */}
                {PROGRAMMES.map(prog => (
                    <section
                        key={prog.id}
                        className="py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto"
                        style={{ borderTop: '2px solid var(--border, #e5e5e5)' }}
                    >
                        <div className={`grid grid-cols-1 lg:grid-cols-2 gap-12 items-center ${prog.flip ? 'lg:[direction:rtl]' : ''}`}>
                            <div style={prog.flip ? { direction: 'ltr' } : {}}>
                                <span className="inline-block text-xs font-black uppercase tracking-widest mb-3 px-3 py-1 rounded-full border-brutal shadow-brutal-sm bg-white">
                                    {prog.badge}
                                </span>
                                <h2 className="font-black uppercase tracking-tight mb-4" style={{ fontSize: 'clamp(1.75rem,3.5vw,2.75rem)', lineHeight: 1.05 }}>
                                    {prog.title}
                                </h2>
                                <p className="text-base font-medium leading-relaxed mb-3 max-w-lg" style={{ color: '#444' }}>
                                    {prog.body}
                                </p>
                                <p className="text-sm font-semibold mb-6 max-w-lg" style={{ color: '#777' }}>
                                    <span className="font-black uppercase text-xs tracking-widest" style={{ color: '#d9381e' }}>Sectors: </span>
                                    {prog.sectors}
                                </p>
                                <div className="flex flex-wrap gap-3">
                                    <a
                                        href={prog.cta.href}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="bg-hp-dark text-white px-6 py-3 rounded-xl border-brutal shadow-brutal interactive-card uppercase font-black text-sm"
                                    >
                                        {prog.cta.label}
                                    </a>
                                </div>
                            </div>

                            <div style={prog.flip ? { direction: 'ltr' } : {}} className="space-y-4">
                                {/* eslint-disable-next-line @next/next/no-img-element */}
                                {!prog.video && (
                                <div className="rounded-2xl overflow-hidden border-brutal shadow-brutal">
                                    <img
                                        src={prog.image}
                                        alt={prog.imageAlt}
                                        className="w-full object-cover"
                                        style={{ maxHeight: 340 }}
                                    />
                                </div>
                                )}
                                {prog.video && (
                                    <div className="rounded-2xl overflow-hidden border-brutal shadow-brutal" style={{ aspectRatio: '16/9' }}>
                                        <iframe
                                            src={prog.video}
                                            title={`${prog.title} video`}
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowFullScreen
                                            className="w-full h-full"
                                            style={{ border: 0 }}
                                        />
                                    </div>
                                )}
                            </div>
                        </div>
                    </section>
                ))}

                {/* ── Why Join ── */}
                <section className="py-20 px-4 sm:px-6 lg:px-8" style={{ background: '#111317' }}>
                    <div className="max-w-7xl mx-auto">
                        <div className="mb-12 text-center">
                            <p className="text-xs font-black uppercase tracking-widest mb-3" style={{ color: '#d9381e' }}>
                                Why Join
                            </p>
                            <h2 className="font-black uppercase tracking-tight text-white" style={{ fontSize: 'clamp(2rem,4vw,3rem)' }}>
                                Why Join the Programme?
                            </h2>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            {WHY_JOIN.map(({ Icon: ItemIcon, title, desc }) => (
                                <div
                                    key={title}
                                    className="p-6 rounded-2xl"
                                    style={{ background: '#1a1a1a', border: '2px solid #2a2a2a' }}
                                >
                                    <div className="w-10 h-10 rounded-xl flex items-center justify-center mb-4" style={{ background: 'rgba(217,56,30,0.15)', color: '#d9381e' }}>
                                        <ItemIcon size={22} weight="bold" />
                                    </div>
                                    <h3 className="font-black uppercase text-white text-sm tracking-wide mb-2">{title}</h3>
                                    <p className="text-sm leading-relaxed" style={{ color: 'rgba(255,255,255,0.6)' }}>{desc}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* ── Eligibility ── */}
                <section className="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
                    <div className="mb-12">
                        <p className="text-xs font-black uppercase tracking-widest mb-3" style={{ color: '#d9381e' }}>
                            Who Can Apply
                        </p>
                        <h2 className="font-black uppercase tracking-tight" style={{ fontSize: 'clamp(2rem,4vw,3rem)', lineHeight: 1.05 }}>
                            Eligibility
                        </h2>
                    </div>
                    <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
                        {/* Interns */}
                        <div>
                            <h3
                                className="font-black uppercase tracking-wide text-base mb-6 px-4 py-3 rounded-xl border-brutal shadow-brutal-sm"
                                style={{ background: '#fff', display: 'inline-block' }}
                            >
                                For Interns
                            </h3>
                            <div className="space-y-4">
                                {ELIGIBILITY.interns.map(item => (
                                    <div key={item.title} className="flex gap-4 p-5 rounded-xl" style={{ background: '#fff', border: '2px solid #111317', boxShadow: '3px 3px 0 #111317' }}>
                                        <div className="shrink-0 w-8 h-8 rounded-lg border-2 border-hp-dark flex items-center justify-center font-black text-sm bg-hp-accent text-white">✓</div>
                                        <div>
                                            <p className="font-black text-sm uppercase tracking-wide mb-1">{item.title}</p>
                                            <p className="text-sm text-text-2 leading-relaxed">{item.desc}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>

                        {/* Experienced */}
                        <div>
                            <h3
                                className="font-black uppercase tracking-wide text-base mb-6 px-4 py-3 rounded-xl border-brutal shadow-brutal-sm"
                                style={{ background: '#fff', display: 'inline-block' }}
                            >
                                For Experienced Talent
                            </h3>
                            <div className="space-y-4">
                                {ELIGIBILITY.talents.map(item => (
                                    <div key={item.title} className="flex gap-4 p-5 rounded-xl" style={{ background: '#fff', border: '2px solid #111317', boxShadow: '3px 3px 0 #111317' }}>
                                        <div className="shrink-0 w-8 h-8 rounded-lg border-2 border-hp-dark flex items-center justify-center font-black text-sm bg-hp-accent text-white">✓</div>
                                        <div>
                                            <p className="font-black text-sm uppercase tracking-wide mb-1">{item.title}</p>
                                            <p className="text-sm text-text-2 leading-relaxed">{item.desc}</p>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </section>

                {/* ── Testimonials ── */}
                <section className="py-20 px-4 sm:px-6 lg:px-8" style={{ background: '#f5f5f0', borderTop: '2px solid #111317' }}>
                    <div className="max-w-7xl mx-auto">
                        <div className="mb-12 text-center">
                            <p className="text-xs font-black uppercase tracking-widest mb-3" style={{ color: '#d9381e' }}>
                                Success Stories
                            </p>
                            <h2 className="font-black uppercase tracking-tight" style={{ fontSize: 'clamp(2rem,4vw,3rem)' }}>
                                Real People. Real Placements.
                            </h2>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                            {TESTIMONIALS.map(t => (
                                <div
                                    key={t.name}
                                    className="p-6 rounded-2xl flex flex-col"
                                    style={{ background: '#fff', border: '2px solid #111317', boxShadow: '4px 4px 0 #111317' }}
                                >
                                    <div className="flex items-center gap-4 mb-5">
                                        {/* eslint-disable-next-line @next/next/no-img-element */}
                                        <img
                                            src={t.photo}
                                            alt={t.name}
                                            className="w-14 h-14 rounded-xl object-cover border-2 border-hp-dark"
                                        />
                                        <div>
                                            <p className="font-black text-sm">{t.name}</p>
                                            <p className="text-xs text-text-2 leading-tight mt-0.5">{t.role}</p>
                                        </div>
                                    </div>
                                    <p className="text-sm leading-relaxed flex-1" style={{ color: '#333' }}>
                                        &ldquo;{t.quote}&rdquo;
                                    </p>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* ── Final CTA ── */}
                <section className="py-20 px-4 sm:px-6 lg:px-8 text-center" style={{ background: '#111317' }}>
                    <div className="max-w-2xl mx-auto space-y-6">
                        <h2 className="font-black uppercase tracking-tight text-white" style={{ fontSize: 'clamp(2rem,5vw,3.5rem)', lineHeight: 1.05 }}>
                            Ready to Take the Step?
                        </h2>
                        <p className="text-lg font-medium" style={{ color: 'rgba(255,255,255,0.6)' }}>
                            Join thousands of Black professionals who have used BPU placements to launch and advance their UK careers.
                        </p>
                        <div className="flex flex-wrap gap-3 justify-center">
                            {isAuth ? (
                                <a
                                    href="/work-placement"
                                    className="bg-white text-hp-dark px-8 py-4 rounded-xl border-brutal shadow-brutal interactive-card uppercase font-black text-sm"
                                    style={{ borderColor: '#fff' }}
                                >
                                    Apply Now
                                </a>
                            ) : (
                                <>
                                    <a
                                        href="/register"
                                        className="bg-white text-hp-dark px-8 py-4 rounded-xl border-brutal shadow-brutal interactive-card uppercase font-black text-sm"
                                        style={{ borderColor: '#fff' }}
                                    >
                                        Create Free Account
                                    </a>
                                    <a
                                        href="/login?returnTo=/work-placement"
                                        className="border-2 border-white text-white px-8 py-4 rounded-xl hover:bg-white hover:text-hp-dark transition-colors uppercase font-black text-sm"
                                    >
                                        Sign In
                                    </a>
                                </>
                            )}
                        </div>
                        <p className="text-xs" style={{ color: 'rgba(255,255,255,0.35)' }}>
                            Also interested in employer opportunities?{' '}
                            <a href="https://blackprofessionals.uk/black-talent-access/" target="_blank" rel="noreferrer" className="underline" style={{ color: 'rgba(255,255,255,0.55)' }}>
                                Talent Access for Employers →
                            </a>
                        </p>
                    </div>
                </section>

            </main>
            <SiteFooter />
        </div>
    );
}
