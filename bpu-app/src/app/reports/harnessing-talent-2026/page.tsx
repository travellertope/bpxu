import SiteHeader from '@/components/SiteHeader';
import SiteFooter from '@/components/SiteFooter';
import EngageModal from './EngageModal';
import {
    GraduationCap, Warning, TrendDown, UsersThree,
    MapPin, Target, ArrowDown,
    Buildings, Globe, Lightbulb,
} from '@phosphor-icons/react/dist/ssr';

export const metadata = {
    title: 'Harnessing Talent: Unlocking Scotland\'s Competitive Edge | Black Professionals UK',
    description: 'The BPU Big Survey — a snapshot of the lived realities of Black professionals in Scotland, the barriers they face, and recommendations for employers and policymakers.',
};

const WP = 'https://blackprofessionals.uk/wp-content/uploads';
const PDF_URL = 'https://blackprofessionals.uk/download/57158/';

const CONTEXT = [
    {
        title: 'A changing demographic reality',
        desc: "Scotland's 2022 Census revealed that 13% of the population identifies as minority ethnic, up from 8% in 2011. Migration is now the primary driver of population growth.",
    },
    {
        title: 'Structural barriers persist',
        desc: "The Scottish Government's Race Equality Framework confirms that minority ethnic people face disproportionate obstacles to fair work, from bias in recruitment to under-recognition of qualifications.",
    },
    {
        title: 'Economic cost of exclusion',
        desc: "Scotland's ethnicity pay gap stands at 10%. The employment rate for minority ethnic groups is 12 percentage points below the white population. This is an economic failure.",
    },
    {
        title: 'The purpose of this report',
        desc: 'Black Professionals UK surveyed its Scotland members to capture lived experiences, barriers to progression, and form an evidence base for coordinated action.',
    },
];

const FINDINGS = [
    {
        Icon: GraduationCap,
        stat: '73%',
        label: 'Highly Educated, Underutilised',
        desc: "Over 73% hold a Master's degree or higher. Yet many remain underemployed or unemployed, signalling systemic inefficiencies in the labour market.",
    },
    {
        Icon: Warning,
        stat: '48%',
        label: 'Personal Experience of Racism',
        desc: 'Nearly half of respondents have personally experienced racism. A further 16.1% report their children have faced racism.',
    },
    {
        Icon: UsersThree,
        stat: '28% vs 49%',
        label: 'Gender Compounded Barriers',
        desc: 'Only 28% of Black women are in their desired role, compared to 49% of Black men, compounding layers of discrimination.',
    },
    {
        Icon: Globe,
        stat: '58%',
        label: 'First-Generation Immigrants',
        desc: '58% are first-generation immigrants, bringing global skills — an enormous asset to organisations seeking international competitiveness.',
    },
    {
        Icon: MapPin,
        stat: '47%',
        label: 'Recently Settled',
        desc: 'Almost half have lived in Scotland for under three years, highlighting the urgent need for targeted integration.',
    },
    {
        Icon: TrendDown,
        stat: '76%',
        label: 'Prime Working Age',
        desc: 'Over three-quarters are between 25–44, the prime age for productivity. Underemployment in this group costs Scotland dearly.',
    },
];

const EMPLOYER_RECS = [
    'Invest in Equitable Career Progression',
    'Create Safe and Supportive Workplaces For All',
    'Embed Inclusive Recruitment Practices',
    'Journey With a Trusted Advisor',
];

const POLICY_RECS = [
    'Strengthen Equality Legislation',
    'Allocate Investment In Talent',
    'Improve Data Transparency',
    'Lead By Example',
];

export default function HarnessingTalentPage() {
    return (
        <div className="min-h-screen flex flex-col bg-hp-bg text-hp-dark">
            <SiteHeader />

            <main className="flex-1 pt-20">

                {/* ── Hero ── */}
                <section className="relative overflow-hidden bg-hp-dark text-white">
                    {/* Background pattern */}
                    <div className="absolute inset-0 opacity-5" style={{
                        backgroundImage: 'radial-gradient(circle at 20% 50%, #ffffff 1px, transparent 1px), radial-gradient(circle at 80% 20%, #ffffff 1px, transparent 1px)',
                        backgroundSize: '60px 60px',
                    }} />

                    <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
                        <div className="max-w-3xl">

                            {/* Title */}
                            <div className="space-y-6">
                                <h1 className="text-5xl sm:text-6xl lg:text-7xl font-black uppercase tracking-tight leading-none">
                                    Harnessing<br />Talent
                                </h1>
                                <p className="text-2xl sm:text-3xl font-black uppercase tracking-tight text-white/60 leading-tight">
                                    Unlocking Scotland's<br />Competitive Edge
                                </p>
                                <p className="text-sm font-semibold uppercase tracking-widest text-white/40">
                                    Black Professionals UK · Big Survey
                                </p>
                                <p className="text-base text-white/70 max-w-lg leading-relaxed">
                                    This report presents findings from the Black Professionals UK Big Survey, offering a snapshot of the lived realities of Black professionals in Scotland. It builds on BPU's mission to dismantle systemic barriers and empower Scotland to unlock its competitive edge.
                                </p>
                                <div className="flex flex-wrap gap-4 pt-2">
                                    <a
                                        href={PDF_URL}
                                        target="_blank"
                                        rel="noreferrer"
                                        className="inline-flex items-center gap-2 px-6 py-3 bg-hp-accent text-white font-black uppercase tracking-tight text-sm rounded-xl border-brutal shadow-brutal hover:shadow-brutal-lg transition-shadow"
                                    >
                                        <ArrowDown size={16} weight="bold" />
                                        Download the Report
                                    </a>
                                    <a
                                        href="#findings"
                                        className="inline-block px-6 py-3 bg-white/10 border border-white/20 text-white font-black uppercase tracking-tight text-sm rounded-xl hover:bg-white/20 transition-colors"
                                    >
                                        Explore the Findings
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {/* ── Context & Background ── */}
                <section className="py-20 bg-white border-b-2 border-hp-dark">
                    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="mb-12">
                            <p className="text-xs font-black uppercase tracking-widest text-hp-accent mb-2">Context &amp; Background</p>
                            <h2 className="text-3xl sm:text-4xl font-black uppercase tracking-tight leading-tight max-w-2xl">
                                Talent isn&apos;t Scotland&apos;s problem.<br />Opportunity Is.
                            </h2>
                            <p className="mt-4 text-base text-text-2 max-w-2xl leading-relaxed">
                                Scotland has set its sights on becoming a Fair Work Nation. Yet Black professionals, many holding postgraduate qualifications and international experience, are still being left behind. This report interrogates why.
                            </p>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                            {CONTEXT.map(c => (
                                <div key={c.title} className="p-6 rounded-2xl border-brutal shadow-brutal bg-hp-bg space-y-3">
                                    <p className="font-black uppercase tracking-tight text-sm">{c.title}</p>
                                    <p className="text-sm text-text-2 leading-relaxed">{c.desc}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* ── Key Findings ── */}
                <section id="findings" className="py-20 bg-hp-dark text-white">
                    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="mb-12 text-center">
                            <p className="text-xs font-black uppercase tracking-widest text-hp-accent mb-2">Key Findings</p>
                            <h2 className="text-3xl sm:text-4xl font-black uppercase tracking-tight">
                                The data tells a clear story
                            </h2>
                            <p className="mt-4 text-base text-white/60 max-w-2xl mx-auto leading-relaxed">
                                Our members are among the most qualified professionals in Scotland. The systemic gaps between their credentials and their career outcomes are not a reflection of their ability — they are a reflection of structural inequality.
                            </p>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            {FINDINGS.map(({ Icon: FindingIcon, stat, label, desc }) => (
                                <div key={label} className="p-6 rounded-2xl border border-white/20 space-y-3">
                                    <div className="flex items-start gap-4">
                                        <div className="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0" style={{ background: 'rgba(217,56,30,0.15)', color: '#d9381e' }}>
                                            <FindingIcon size={22} weight="bold" />
                                        </div>
                                        <p className="text-4xl font-black text-hp-accent leading-none">{stat}</p>
                                    </div>
                                    <p className="font-black uppercase tracking-tight text-sm text-white">{label}</p>
                                    <p className="text-sm text-white/60 leading-relaxed">{desc}</p>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* ── Report Insights ── */}
                <section className="py-20 bg-white border-b-2 border-hp-dark">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                        <p className="text-xs font-black uppercase tracking-widest text-hp-accent mb-2">Report Insights</p>
                        <h2 className="text-3xl sm:text-4xl font-black uppercase tracking-tight mb-6">
                            What this report reveals
                        </h2>
                        <p className="text-base text-text-2 leading-relaxed mb-8">
                            The BPU Big Survey highlights a pattern that runs throughout Scotland&apos;s labour market: highly qualified Black professionals are not progressing at a rate that reflects their skills and experience. Understanding and addressing the structural reasons for this gap is the starting point for meaningful change.
                        </p>
                        <blockquote className="relative p-8 rounded-2xl border-brutal shadow-brutal bg-hp-dark text-white overflow-hidden">
                            <div className="absolute top-4 left-6 text-8xl font-black leading-none opacity-10 select-none">&ldquo;</div>
                            <p className="relative text-lg sm:text-xl font-semibold leading-relaxed italic text-white/90">
                                In Scotland&apos;s meritocratic narrative, talent should determine success. Yet the reality for many Black professionals is that structural barriers, rather than ability, dictate career trajectories.
                            </p>
                        </blockquote>
                        <p className="mt-8 text-base text-text-2 leading-relaxed">
                            Common barriers identified include cultural biases in recruitment, limited access to influential professional networks, and the persistent under-recognition of internationally earned qualifications.
                        </p>
                    </div>
                </section>

                {/* ── Call to Action (Policymakers & Employers) ── */}
                <section className="py-20">
                    <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="mb-12">
                            <p className="text-xs font-black uppercase tracking-widest text-hp-accent mb-2">For Policymakers &amp; Employers</p>
                            <h2 className="text-3xl sm:text-4xl font-black uppercase tracking-tight mb-4">
                                A call for coordinated action
                            </h2>
                            <p className="text-base text-text-2 max-w-3xl leading-relaxed">
                                The findings of this survey highlight systemic challenges that cannot be solved by individuals alone. They require coordinated action from employers, policymakers, and community organisations like BPU. These recommendations are designed to dismantle barriers, promote equity, and unlock the full potential of Black professionals in Scotland, aligned with the Equality Act 2010, SDG 8: Decent Work and Economic Growth, and SDG 10: Reduced Inequalities.
                            </p>
                        </div>
                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            {/* For Employers */}
                            <div className="p-8 rounded-2xl border-brutal shadow-brutal bg-hp-bg">
                                <div className="flex items-center gap-3 mb-6">
                                    <div className="w-10 h-10 rounded-xl bg-hp-accent/10 flex items-center justify-center text-hp-accent">
                                        <Buildings size={22} weight="bold" />
                                    </div>
                                    <p className="font-black uppercase tracking-tight text-sm">For Employers</p>
                                </div>
                                <ul className="space-y-3">
                                    {EMPLOYER_RECS.map(r => (
                                        <li key={r} className="flex items-start gap-3">
                                            <span className="flex-shrink-0 w-5 h-5 rounded-full bg-hp-accent text-white flex items-center justify-center mt-0.5">
                                                <Target size={10} weight="bold" />
                                            </span>
                                            <span className="text-sm font-semibold leading-snug">{r}</span>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                            {/* For Policymakers */}
                            <div className="p-8 rounded-2xl border-brutal shadow-brutal bg-hp-dark text-white">
                                <div className="flex items-center gap-3 mb-6">
                                    <div className="w-10 h-10 rounded-xl flex items-center justify-center" style={{ background: 'rgba(217,56,30,0.15)', color: '#d9381e' }}>
                                        <Lightbulb size={22} weight="bold" />
                                    </div>
                                    <p className="font-black uppercase tracking-tight text-sm text-white">For Policymakers</p>
                                </div>
                                <ul className="space-y-3">
                                    {POLICY_RECS.map(r => (
                                        <li key={r} className="flex items-start gap-3">
                                            <span className="flex-shrink-0 w-5 h-5 rounded-full bg-hp-accent text-white flex items-center justify-center mt-0.5">
                                                <Target size={10} weight="bold" />
                                            </span>
                                            <span className="text-sm font-semibold text-white/80 leading-snug">{r}</span>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        </div>
                    </div>
                </section>

                {/* ── Download CTA ── */}
                <section className="py-20 bg-hp-accent text-white">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6">
                        <p className="text-xs font-black uppercase tracking-widest text-white/60">2026 Full Report</p>
                        <h2 className="text-3xl sm:text-4xl lg:text-5xl font-black uppercase tracking-tight leading-tight">
                            Read the full findings.<br />Drive real change.
                        </h2>
                        <p className="text-base text-white/80 max-w-xl mx-auto leading-relaxed">
                            Download the complete BPU Harnessing Talent report, including all survey data, demographic analysis, insights, and recommendations, free of charge.
                        </p>
                        <div className="flex flex-wrap justify-center gap-4 pt-2">
                            <a
                                href={PDF_URL}
                                target="_blank"
                                rel="noreferrer"
                                className="inline-flex items-center gap-2 px-8 py-4 bg-white text-hp-accent font-black uppercase tracking-tight text-sm rounded-xl border-2 border-white shadow-brutal hover:shadow-brutal-lg transition-shadow"
                            >
                                <ArrowDown size={16} weight="bold" />
                                Download PDF
                            </a>
                            <EngageModal />
                        </div>
                    </div>
                </section>


            </main>
                <SiteFooter />
        </div>
    );
}
