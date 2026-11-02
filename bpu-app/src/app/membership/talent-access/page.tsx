import SiteHeader from '@/components/SiteHeader';
import SiteFooter from '@/components/SiteFooter';
import TalentAccessForm from './TalentAccessForm';

export const metadata = {
    title: 'BPU Talent Access | Black Professionals UK',
    description: 'Connect your organisation with skilled Black and ethnic minority professionals and interns through BPU\'s talent access programmes.',
};

const STEPS = [
    {
        number: '1',
        title: 'Register Your Interest',
        desc: 'Begin your journey by registering your organisation\'s interest on our platform. Share your requirements and preferences, and we will tailor our matching process to identify the most suitable candidates.',
    },
    {
        number: '2',
        title: 'Discover Top Talent',
        desc: 'Our comprehensive screening process ensures that your organisation is connected with candidates who align with your specific needs and values. Whether it\'s internships or full-time positions, we strive to deliver exceptional matches.',
    },
    {
        number: '3',
        title: 'Seamless Connection',
        desc: 'Once you\'ve identified the ideal candidates, we facilitate seamless introductions. We understand the importance of a smooth onboarding process, and our team ensures that the transition is effortless for both your organisation and the selected candidates.',
    },
    {
        number: '4',
        title: 'Ongoing Support',
        desc: 'Our commitment doesn\'t end with the placement. We provide ongoing support and follow-up to ensure that the collaboration is thriving and meeting your organisation\'s goals.',
    },
];

const TESTIMONIALS = [
    { name: 'Ashley McPartlin', org: 'Brodies LLP',       videoId: 'JTqtY7HZsAg' },
    { name: 'Graeme Devlin',    org: 'Devlin Mambo LLP',  videoId: 'S3e8juB0jwA' },
    { name: 'Magdalena Harcus', org: 'Pinsent Masons',    videoId: 'iDDCt1Ge5PM' },
];

export default function TalentAccessPage() {
    return (
        <div className="min-h-screen flex flex-col bg-hp-bg text-hp-dark">
            <SiteHeader />

            <main className="flex-1 pt-20">
                {/* Hero */}
                <section className="py-20 bg-hp-dark text-white">
                    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="inline-block bg-hp-accent text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border-2 border-white mb-6">
                            For Organisations
                        </div>
                        <h1 className="text-4xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight leading-none mb-6">
                            BPU Talent Access
                        </h1>
                        <p className="text-lg sm:text-xl font-semibold text-white/80 max-w-3xl leading-relaxed">
                            Visionary organisations are constantly seeking fresh perspectives, diverse talent, and innovative ideas to stay at the forefront of their industries.
                        </p>
                        <p className="text-base text-white/70 max-w-3xl mt-4 leading-relaxed">
                            These programmes provide organisations with unparalleled access to a pool of skilled Black and other ethnic minority talents and interns. We believe that talent knows no bounds and can be found in every corner of our community.
                        </p>
                    </div>
                </section>

                {/* Programmes overview */}
                <section className="py-16">
                    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-10 text-center">
                            Our Programmes
                        </h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div className="p-6 rounded-2xl border-brutal shadow-brutal bg-white space-y-3">
                                <span className="inline-block text-xs font-black uppercase tracking-widest bg-hp-accent text-white px-3 py-1 rounded-full">Early Career</span>
                                <h3 className="font-black uppercase tracking-tight text-lg">Black Interns Programme</h3>
                                <p className="text-sm text-text-2 leading-relaxed">
                                    Our recognised Black Interns Programme connects forward-thinking organisations with talented Black university undergraduates seeking work experience. This programme serves as a valuable opportunity for organisations to tap into fresh perspectives and cultivate a pipeline of diverse talent.
                                </p>
                            </div>
                            <div className="p-6 rounded-2xl border-brutal shadow-brutal bg-white space-y-3">
                                <span className="inline-block text-xs font-black uppercase tracking-widest bg-hp-dark text-white px-3 py-1 rounded-full">Experienced Professionals</span>
                                <h3 className="font-black uppercase tracking-tight text-lg">Experienced Talent Programme</h3>
                                <p className="text-sm text-text-2 leading-relaxed">
                                    For organisations seeking skilled individuals with Undergraduate, Masters, and PhD degrees and previous international work experience. This initiative enables your organisation to discover exceptional talent ready to contribute their expertise and insights to your team.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                {/* 4-Step Process */}
                <section className="py-16 bg-white border-t-2 border-b-2 border-hp-dark">
                    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="text-center mb-12">
                            <p className="text-xs font-black uppercase tracking-widest text-hp-accent mb-2">How it Works</p>
                            <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight">The 4-Step Process</h2>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            {STEPS.map(s => (
                                <div key={s.number} className="flex items-start gap-4 p-5 rounded-2xl border-brutal shadow-brutal-sm bg-hp-bg">
                                    <span className="flex-shrink-0 w-10 h-10 rounded-full border-brutal shadow-brutal-sm bg-hp-accent text-white flex items-center justify-center font-black text-lg">
                                        {s.number}
                                    </span>
                                    <div>
                                        <p className="font-black uppercase tracking-tight text-sm mb-1">{s.title}</p>
                                        <p className="text-xs text-text-2 leading-relaxed">{s.desc}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Testimonials */}
                <section className="py-16">
                    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="text-center mb-10">
                            <p className="text-xs font-black uppercase tracking-widest text-hp-accent mb-2">What Our Beneficiary Organisations Say</p>
                            <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight">Live Testimonials</h2>
                        </div>
                        <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            {TESTIMONIALS.map(t => (
                                <div key={t.name} className="rounded-2xl border-brutal shadow-brutal overflow-hidden bg-white">
                                    <div className="relative" style={{ paddingTop: '56.25%' }}>
                                        <iframe
                                            src={`https://www.youtube.com/embed/${t.videoId}`}
                                            title={`${t.name} — ${t.org}`}
                                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                            allowFullScreen
                                            className="absolute inset-0 w-full h-full"
                                        />
                                    </div>
                                    <div className="p-4">
                                        <p className="font-black text-sm">{t.name}</p>
                                        <p className="text-xs text-text-3">{t.org}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* Registration form */}
                <section className="py-16 bg-hp-dark">
                    <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="text-center mb-10">
                            <h2 className="text-3xl sm:text-4xl font-black uppercase tracking-tight text-white mb-3">
                                Ready to Join the Programme?
                            </h2>
                            <p className="text-white/70 text-base">
                                Fill out the form below and our partnerships team will be in touch shortly.
                            </p>
                        </div>
                        <div className="bg-hp-bg rounded-2xl border-brutal shadow-brutal p-6 sm:p-8">
                            <TalentAccessForm />
                        </div>
                    </div>
                </section>
            </main>
                <SiteFooter />
        </div>
    );
}
