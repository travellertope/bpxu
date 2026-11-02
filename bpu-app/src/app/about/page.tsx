import SiteHeader from '@/components/SiteHeader';
import SiteFooter from '@/components/SiteFooter';

export const metadata = {
    title: 'About Us | Black Professionals United Kingdom',
    description: 'Founded in 2016 as Edinburgh Black Professionals, we have grown into Black Professionals United Kingdom — a platform championing Black professionals across the UK.',
};

const LEADERSHIP = [
    {
        name:  'Enoch Adeyemi',
        role:  'Founder / CEO',
        photo: 'https://blackprofessionals.uk/wp-content/uploads/2024/01/Enoch-Adeyemi-3.jpg',
    },
    {
        name:  'Asabea Owusu',
        role:  'Director, Communications',
        photo: 'https://blackprofessionals.uk/wp-content/uploads/2024/01/Asabea-Owusu.jpg',
    },
    {
        name:  'Joshua Adeyemi',
        role:  'Director, Digital',
        photo: 'https://blackprofessionals.uk/wp-content/uploads/2024/01/Joshua-Adeyemi.jpg',
    },
    {
        name:  'Tosin Ogunlesi',
        role:  'Director, Technology',
        photo: 'https://blackprofessionals.uk/wp-content/uploads/2024/01/Tosin-Ogunlesi.jpg',
    },
    {
        name:  'Taofeek Giwa',
        role:  'Director, Initiatives',
        photo: 'https://blackprofessionals.uk/wp-content/uploads/2024/01/Taofeek-Giwa.jpg',
    },
];

const MILESTONES = [
    { year: '2016', text: 'Founded in Edinburgh as a networking platform fuelled by engaging, speaker-led events.' },
    { year: '2019', text: 'Expanded to cover the whole of Scotland as Black Professionals Scotland, with a growing membership across major cities.' },
    { year: '2023', text: 'Extended our reach to the rest of the UK, becoming Black Professionals United Kingdom.' },
    { year: 'Today', text: 'A thriving platform serving professionals in Tech, Finance, the Arts, and more across the entire United Kingdom.' },
];

export default function AboutPage() {
    return (
        <div className="min-h-screen flex flex-col bg-hp-bg text-hp-dark">
            <SiteHeader />

            <main className="flex-1 pt-20">

                {/* Hero */}
                <section className="py-20 bg-hp-dark text-white">
                    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="inline-block bg-hp-accent text-white text-xs font-black uppercase tracking-widest px-4 py-2 rounded-full border-2 border-white mb-6">
                            Our Story
                        </div>
                        <h1 className="text-4xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight leading-none mb-6">
                            About Black<br />Professionals UK
                        </h1>
                        <p className="text-lg sm:text-xl font-semibold text-white/80 max-w-2xl leading-relaxed">
                            Our journey began in 2016 with Edinburgh Black Professionals — a networking platform fuelled by engaging, speaker-led events.
                        </p>
                    </div>
                </section>

                {/* Origin story */}
                <section className="py-16 bg-white border-b-2 border-hp-dark">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-10 items-start">
                            <div>
                                <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-5">
                                    How We Got Here
                                </h2>
                                <p className="text-base text-text-2 leading-relaxed mb-4">
                                    Since our founding in Edinburgh, we have expanded to cover the whole of Scotland as Black Professionals Scotland, building a membership across major cities and towns, boasting expertise in Tech, Finance, the Arts, and more.
                                </p>
                                <p className="text-base text-text-2 leading-relaxed mb-4">
                                    Today, we proudly extend our reach to the rest of the UK as Black Professionals United Kingdom.
                                </p>
                                <p className="text-base text-text-2 leading-relaxed">
                                    Our expansion was driven by a commitment to addressing the unique obstacles faced by Black professionals in various industries. We recognise the importance of creating a safe space where our members can share their experiences, seek support, and connect with like-minded individuals who understand their journey.
                                </p>
                            </div>
                            <div className="space-y-4">
                                {MILESTONES.map((m, i) => (
                                    <div key={i} className="flex gap-4 items-start">
                                        <div className="flex-shrink-0 w-16 h-16 rounded-full border-brutal shadow-brutal-sm bg-hp-accent text-white flex items-center justify-center text-xs font-black text-center leading-tight px-1">
                                            {m.year}
                                        </div>
                                        <p className="text-sm text-text-2 leading-relaxed pt-2">{m.text}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </section>

                {/* Mission & Vision */}
                <section className="py-16">
                    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                        <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-10 text-center">
                            Mission &amp; Vision
                        </h2>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div className="p-8 rounded-2xl border-brutal shadow-brutal bg-hp-dark text-white space-y-3">
                                <div className="inline-block bg-hp-accent text-white text-xs font-black uppercase tracking-widest px-3 py-1 rounded-full border border-white mb-1">
                                    Mission
                                </div>
                                <h3 className="text-xl font-black uppercase tracking-tight">Mission Statement</h3>
                                <p className="text-white/75 text-base leading-relaxed">
                                    We aim to be a catalyst for change, advocating for increased representation and opportunities for people and organisations, while challenging systemic barriers and biases.
                                </p>
                            </div>
                            <div className="p-8 rounded-2xl border-brutal shadow-brutal bg-white space-y-3">
                                <div className="inline-block bg-hp-accent text-white text-xs font-black uppercase tracking-widest px-3 py-1 rounded-full border-2 border-hp-dark mb-1">
                                    Vision
                                </div>
                                <h3 className="text-xl font-black uppercase tracking-tight">Our Vision</h3>
                                <p className="text-text-2 text-base leading-relaxed">
                                    Our vision is to create a world where Black professionals excel in all sectors, and where organisations thrive in equality and representation.
                                </p>
                            </div>
                        </div>
                    </div>
                </section>

                {/* Platform purpose */}
                <section className="py-16 bg-white border-t-2 border-b-2 border-hp-dark">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                        <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-5">
                            Built for Individuals &amp; Organisations
                        </h2>
                        <p className="text-base text-text-2 leading-relaxed max-w-2xl mx-auto">
                            Our updated platform has been designed with both individuals and organisations in mind. It seeks to harness the power of diversity and inclusion — which are essential for success in the modern workplace — connecting talent with opportunity at every level.
                        </p>
                    </div>
                </section>

                {/* Leadership */}
                <section className="py-16">
                    <div className="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="text-center mb-10">
                            <h2 className="text-2xl sm:text-3xl font-black uppercase tracking-tight mb-3">
                                Leadership
                            </h2>
                            <p className="text-text-2 text-base max-w-xl mx-auto">
                                We take pride in a leadership team that excels in both diversity and inclusion, while also possessing practical expertise in their respective roles.
                            </p>
                        </div>
                        <div className="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-5">
                            {LEADERSHIP.map(person => (
                                <div key={person.name} className="flex flex-col items-center text-center gap-3">
                                    <div className="w-24 h-24 rounded-full border-brutal shadow-brutal overflow-hidden bg-hp-bg flex-shrink-0">
                                        <img
                                            src={person.photo}
                                            alt={person.name}
                                            className="w-full h-full object-cover"
                                            loading="lazy"
                                        />
                                    </div>
                                    <div>
                                        <p className="font-black text-sm uppercase tracking-tight leading-snug">{person.name}</p>
                                        <p className="text-xs text-text-3 mt-0.5 leading-snug">{person.role}</p>
                                    </div>
                                </div>
                            ))}
                        </div>
                    </div>
                </section>

                {/* You may be interested */}
                <section className="py-16 bg-hp-dark">
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                        <p className="text-xs font-black uppercase tracking-widest text-white/50 mb-6 text-center">You May Be Interested In…</p>
                        <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div className="p-6 rounded-2xl border-2 border-white/20 hover:border-white/60 transition-colors space-y-3">
                                <p className="font-black uppercase tracking-tight text-white">Awards &amp; Recognition</p>
                                <p className="text-sm text-white/60 leading-relaxed">
                                    We are honoured to share the awards and achievements that highlight our impactful contributions to the community.
                                </p>
                                <a href="/awards" className="inline-block text-sm font-black uppercase tracking-wide text-hp-accent hover:underline">
                                    Learn More →
                                </a>
                            </div>
                            <div className="p-6 rounded-2xl border-2 border-white/20 hover:border-white/60 transition-colors space-y-3">
                                <p className="font-black uppercase tracking-tight text-white">Partnership</p>
                                <p className="text-sm text-white/60 leading-relaxed">
                                    We are committed to fostering meaningful partnerships that drive diversity, equity, and inclusion in the professional setting.
                                </p>
                                <a href="/partnership" className="inline-block text-sm font-black uppercase tracking-wide text-hp-accent hover:underline">
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
