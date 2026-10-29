'use client';

import { useState, useEffect, useRef } from 'react';
import {
  List, ArrowRight,
  UsersThree, Buildings, Briefcase, MagnifyingGlass,
  ListMagnifyingGlass, MicrophoneStage, FileText, Users,
  GraduationCap, CalendarStar, Lifebuoy, ChartBar, Play,
  LinkedinLogo, TwitterLogo, InstagramLogo,
} from '@phosphor-icons/react';

/* ─── WordPress post shape (minimal) ─── */
interface WpPost {
  id: number;
  link: string;
  title: { rendered: string };
  excerpt: { rendered: string };
  _embedded?: {
    'wp:featuredmedia'?: Array<{ source_url: string }>;
    'wp:term'?: Array<Array<{ name: string }>>;
  };
}

/* ─── cycling cities ─── */
const CITIES = [
  'Glasgow', 'London', 'Birmingham', 'Manchester', 'Leeds',
  'Edinburgh', 'Bristol', 'Cardiff', 'Liverpool', 'Nottingham',
  'Sheffield', 'Leicester', 'Newcastle', 'Brighton', 'Oxford',
];

/* ─── partners ─── */
const PARTNERS = [
  { name: 'Codebase',                        href: 'https://www.codebase.com/',            logo: 'https://blackprofessionals.uk/wp-content/uploads/2025/03/Codebase.jpg' },
  { name: 'M&G',                             href: 'https://www.mandg.com/',               logo: 'https://blackprofessionals.uk/wp-content/uploads/2026/02/m-and-g.png' },
  { name: 'RSPB',                            href: 'https://www.rspb.org.uk/',             logo: 'https://blackprofessionals.uk/wp-content/uploads/2023/11/RSPB.jpg' },
  { name: 'Cairngorms',                      href: 'https://cairngorms.co.uk/',            logo: 'https://blackprofessionals.uk/wp-content/uploads/2023/11/Cairngorms.jpg' },
  { name: 'Scottish National Investment Bank', href: 'https://www.thebank.scot/',          logo: 'https://blackprofessionals.uk/wp-content/uploads/2023/11/Scottish-National-Investment-Bank.jpg' },
  { name: 'Barclays',                        href: 'https://www.barclays.co.uk/',          logo: 'https://blackprofessionals.uk/wp-content/uploads/2023/11/Barclays-1.jpg' },
  { name: 'The Skills Network',              href: 'https://theskillsnetwork.com/',        logo: 'https://blackprofessionals.uk/wp-content/uploads/2023/11/The-Skills-Network.jpg' },
  { name: 'BlackRock',                       href: 'https://www.blackrock.com/',           logo: 'https://blackprofessionals.uk/wp-content/uploads/2023/11/blackrock-1.jpg' },
  { name: 'Charles River',                   href: 'https://www.criver.com/',              logo: 'https://blackprofessionals.uk/wp-content/uploads/2023/11/charles_river-1.jpg' },
  { name: 'BNY',                             href: 'https://www.bny.com/',                 logo: 'https://blackprofessionals.uk/wp-content/uploads/2020/08/bny-log.png' },
  { name: 'FreeAgent',                       href: 'https://www.freeagent.com/en/',        logo: 'https://blackprofessionals.uk/wp-content/uploads/2026/05/free-agent-.png' },
  { name: 'NatWest',                         href: 'https://www.natwest.com/',             logo: 'https://blackprofessionals.uk/wp-content/uploads/2025/07/Natwest.png' },
  { name: 'Walter Scott',                    href: 'https://www.walterscott.com/',         logo: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/walter-scott-1.png' },
  { name: 'Addleshaw Goddard',               href: 'https://www.addleshawgoddard.com/en/', logo: 'https://blackprofessionals.uk/wp-content/uploads/2026/05/Addleshaw-Goddard-logo-1-768x512-1.jpg' },
  { name: 'Capco',                           href: 'https://www.capco.com/',               logo: 'https://blackprofessionals.uk/wp-content/uploads/2026/08/capco.png' },
];

/* ─── hero photo data ─── */
const HERO_PHOTOS = [
  { src: '/images/hero/bcc26-keynote-full-room.jpg',     alt: 'BCC2026 keynote speaker with packed auditorium', tall: true  },
  { src: '/images/hero/bcc26-group-backdrop-wide.jpg',   alt: 'BCC2026 group at sponsor backdrop',              tall: false },
  { src: '/images/hero/bcc26-audience-diverse.jpg',      alt: 'Diverse BCC2026 audience',                       tall: true  },
  { src: '/images/hero/bcc26-networking-laugh.jpg',      alt: 'Professionals networking at BCC2026',            tall: false },
  { src: '/images/hero/bcc26-group-backdrop-portrait.jpg', alt: 'BCC2026 delegates at sponsor wall',            tall: true  },
  { src: '/images/hero/bcc26-auditorium-wide.jpg',       alt: 'BCC2026 full auditorium',                        tall: false },
  { src: '/images/hero/bcc26-attendee-portrait.jpg',     alt: 'Engaged BCC2026 attendee',                       tall: true  },
  { src: '/images/hero/bcc26-atrium-networking.jpg',     alt: 'BCC2026 atrium networking',                      tall: false },
  { src: '/images/hero/bcc26-two-women-audience.jpg',    alt: 'BCC2026 delegates listening intently',           tall: true  },
  { src: '/images/hero/bcc26-expo-hall.jpg',             alt: 'BCC2026 expo and networking hall',               tall: false },
  { src: '/images/hero/bcc26-blue-suit-smile.jpg',       alt: 'BCC2026 professional delegate',                  tall: true  },
  { src: '/images/hero/bcc26-speaker-behind.jpg',        alt: 'BCC2026 speaker addressing the room',            tall: false },
  { src: '/images/hero/bcc26-three-pros-seated.jpg',     alt: 'BCC2026 well-dressed delegates',                 tall: true  },
  { src: '/images/hero/bcc26-audience-from-stage.jpg',   alt: 'BCC2026 audience from stage view',               tall: false },
  { src: '/images/hero/bcc26-focused-attendees.jpg',     alt: 'BCC2026 focused attendees',                      tall: true  },
  { src: '/images/hero/bcc26-audience-side.jpg',         alt: 'BCC2026 audience side view',                     tall: false },
  { src: '/images/hero/bcc26-keynote-charge.jpg',        alt: 'BCC2026 keynote and charge session',             tall: true  },
  { src: '/images/hero/bcc26-front-row-audience.jpg',    alt: 'BCC2026 front row delegates',                    tall: false },
  { src: '/images/hero/bcc26-women-audience.jpg',        alt: 'BCC2026 women delegates',                        tall: true  },
  { src: '/images/hero/bcc26-speaker-podium.jpg',        alt: 'BCC2026 speaker at podium',                      tall: false },
];
const heroLeft  = HERO_PHOTOS.filter((_, i) => i % 2 === 0); // starts tall
const heroRight = HERO_PHOTOS.filter((_, i) => i % 2 === 1); // starts short

/* ─── counter hook ─── */
function useCounter(target: number, active: boolean) {
  const [val, setVal] = useState(0);
  useEffect(() => {
    if (!active) return;
    let n = 0;
    const inc = target / 200;
    const id = setInterval(() => {
      n += inc;
      if (n >= target) { setVal(target); clearInterval(id); }
      else setVal(Math.ceil(n));
    }, 10);
    return () => clearInterval(id);
  }, [target, active]);
  return val;
}

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

/* Score how well a display_name matches a search name — count shared tokens */
function nameMatchScore(displayName: string, searchName: string): number {
  const clean = (s: string) => s.toLowerCase().replace(/\bdr\.?\b/g, '').trim();
  const tokens = clean(searchName).split(/\s+/).filter(t => t.length > 2);
  const haystack = clean(displayName);
  return tokens.filter(t => haystack.includes(t)).length;
}

/* ─── Static showcase members ─── */
const STATIC_MEMBERS = [
  { name: 'Wosilat Zachariah',       role: 'Mental Health Nurse',                  img: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/e85e2496-aefb-48ec-9092-25cd3f3316b8-Wosilat-Zachariah.jpeg' },
  { name: 'Edward Enejoh',            role: 'Project Manager / Business Analyst',   img: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/IMG_1918-Edward-Enejoh-scaled.jpeg' },
  { name: 'Bukola Osuntuyi',          role: 'Tech Business Operations',             img: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/photo-output-Olubukola-Osuntuyi.jpeg' },
  { name: 'Nafisat Ogijo',            role: 'Data Analyst',                         img: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/IMG_8373-Nafisat-olamide-Ogijo.jpg' },
  { name: 'Dr Elizabeth Wuyep',       role: 'Sales and Operations Coordinator',     img: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/IMG_3097-Elizabeth-scaled.jpeg' },
  { name: 'Oluwasegun Odumoye',       role: 'Risk Management',                      img: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/Segun-Landscape-2-Oluwasegun-Odumoye-scaled.jpg' },
  { name: 'Uchenna Chukwu',           role: 'Product Manager',                      img: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/DSCF2795-Prince-Uchenna-Chukwu-scaled.jpg' },
  { name: 'Chibuikem Ugwu',           role: 'Support Worker',                       img: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/Snapchat-123564268-ugwu-chibuikem.jpg' },
  { name: 'Lawrence Dada',            role: 'Process and Flow Assurance Engineer',  img: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/IMG_8144-Lawrence-DADA-e1761234517311.jpeg' },
  { name: 'Olamide Kosile',           role: 'Cloud Infrastructure Engineer',        img: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/Generated-Image-September-25-2025-6_28PM-Olamide-Kosile.png' },
  { name: 'Augustine Ologun',         role: 'Cloud Infrastructure Engineer',        img: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/IMG_2487-Augustine-Ologun.jpeg' },
  { name: 'Michael David',            role: 'Tech Support',                         img: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/Michael-David-Headshot-David-Michael.png' },
  { name: 'Ifeanyi Moses A',          role: 'HR – People Engagement Administrator', img: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/IMG_6336-Moses-Ifeanyi.jpeg' },
  { name: 'Kelechi Elizabeth Origa',  role: 'Product / Project Manager',            img: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/IMG_0388-Kelechi-Origa.jpeg' },
  { name: 'Dr. Ikenna Light Nkwocha', role: 'Volunteer Research Associate',         img: 'https://blackprofessionals.uk/wp-content/uploads/2025/10/IMG-20250106-WA0002-Ikenna-Nkwocha-e1761239259922.jpg' },
];

export default function HomePageClient() {
  const [posts, setPosts] = useState<WpPost[]>([]);
  /* name → resolved WP username (populated async after mount) */
  const [resolvedUsernames, setResolvedUsernames] = useState<Record<string, string>>({});
  const [menuOpen, setMenuOpen] = useState(false);
  const [navUp, setNavUp] = useState(false);
  const [counting, setCounting] = useState(false);
  const [cityIndex, setCityIndex] = useState(0);
  const [cityVisible, setCityVisible] = useState(true);
  const lastY = useRef(0);
  const marqueeRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    fetch('https://blackprofessionals.uk/wp-json/wp/v2/posts?per_page=3&_embed=1')
      .then(r => r.ok ? r.json() : [])
      .then(setPosts)
      .catch(() => {});
  }, []);

  /* Resolve each hardcoded member to a WP username via name search */
  useEffect(() => {
    const results: Record<string, string> = {};
    Promise.allSettled(
      STATIC_MEMBERS.map(({ name }) =>
        fetch(`${WP_URL}/wp-json/bpu/v1/members?search=${encodeURIComponent(name)}&per_page=5`)
          .then(r => r.ok ? r.json() : [])
          .then((candidates: Array<{ username: string; display_name: string }>) => {
            /* Pick candidate with highest name token overlap, require ≥2 matching tokens */
            const best = candidates
              .map(c => ({ username: c.username, score: nameMatchScore(c.display_name, name) }))
              .filter(c => c.score >= 2)
              .sort((a, b) => b.score - a.score)[0];
            if (best) results[name] = best.username;
          })
          .catch(() => {}),
      ),
    ).then(() => {
      if (Object.keys(results).length > 0) setResolvedUsernames(results);
    });
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, []);


  useEffect(() => {
    const id = setInterval(() => {
      setCityVisible(false);
      setTimeout(() => {
        setCityIndex(i => (i + 1) % CITIES.length);
        setCityVisible(true);
      }, 280);
    }, 2200);
    return () => clearInterval(id);
  }, []);

  useEffect(() => {
    const onScroll = () => {
      const y = window.scrollY;
      setNavUp(y > lastY.current && y > 100);
      lastY.current = y;
    };
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  useEffect(() => {
    const el = marqueeRef.current;
    if (!el) return;
    const obs = new IntersectionObserver(
      ([e]) => { if (e.isIntersecting) { setCounting(true); obs.disconnect(); } },
      { threshold: 0.1 },
    );
    obs.observe(el);
    return () => obs.disconnect();
  }, []);

  const members    = useCounter(7000, counting);
  const partners   = useCounter(30,   counting);
  const placements = useCounter(1000, counting);
  const vacancies  = useCounter(200,  counting);

  return (
    <div className="bg-hp-bg text-hp-dark antialiased overflow-x-hidden flex-1">

      {/* ── Navigation ── */}
      <nav
        className={`fixed w-full top-0 z-50 bg-hp-bg border-brutal-b transition-transform duration-300 ${
          navUp ? '-translate-y-full' : 'translate-y-0'
        }`}
      >
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex justify-between h-20 items-center">

            {/* Logo */}
            <div className="flex-shrink-0">
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src="/images/bpu-logo.png" alt="Black Professionals UK" className="h-12 w-auto" />
            </div>

            {/* Desktop nav */}
            <div className="hidden lg:flex space-x-6 items-center font-bold text-sm uppercase tracking-wide">
              <a href="/membership"  className="hover:text-hp-accent transition-colors">Memberships</a>
              <a href="/partnerships" className="hover:text-hp-accent transition-colors">Partnerships</a>
              <a href="/jobs"        className="hover:text-hp-accent transition-colors">Job Board</a>
              <a href="/events"      className="hover:text-hp-accent transition-colors">Events</a>
              <a href="#report"      className="hover:text-hp-accent transition-colors">Report</a>
              <a href="/safe-space"  className="hover:text-hp-accent transition-colors">Safe Space</a>
              <a
                href="/register"
                className="bg-hp-dark text-white px-6 py-3 rounded-xl border-brutal shadow-brutal interactive-card uppercase font-black text-sm"
              >
                Become a Member
              </a>
            </div>

            {/* Mobile hamburger */}
            <div className="lg:hidden flex items-center">
              <button
                onClick={() => setMenuOpen(o => !o)}
                className="p-2 border-brutal rounded-xl shadow-brutal-sm bg-white"
                aria-label="Toggle menu"
              >
                <List size={24} />
              </button>
            </div>
          </div>
        </div>

        {/* Mobile dropdown */}
        {menuOpen && (
          <div className="lg:hidden bg-hp-bg border-brutal-b font-bold uppercase text-center flex flex-col">
            <a href="/membership"   className="block py-4 border-b-2 border-hp-dark">Memberships</a>
            <a href="/partnerships" className="block py-4 border-b-2 border-hp-dark">Partnerships</a>
            <a href="/jobs"         className="block py-4 border-b-2 border-hp-dark">Job Board</a>
            <a href="/events"       className="block py-4 border-b-2 border-hp-dark">Events</a>
            <a href="/safe-space"   className="block py-4 border-b-2 border-hp-dark">Safe Space</a>
            <div className="p-4">
              <a
                href="/register"
                className="block w-full bg-hp-dark text-white px-6 py-4 rounded-xl border-brutal shadow-brutal uppercase font-black text-sm text-center"
              >
                Become a Member
              </a>
            </div>
          </div>
        )}
      </nav>

      {/* ── Hero ── */}
      <header className="pt-32 pb-0 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

          {/* Text */}
          <div className="space-y-8 z-10 pb-16">
            <div className="inline-flex items-center gap-2 border-brutal rounded-full px-4 py-2 bg-white shadow-brutal-sm">
              <div className="w-3 h-3 bg-hp-accent rounded-full animate-pulse" />
              <span className="font-bold text-sm uppercase tracking-wider flex items-center gap-1">
                <span style={{
                  display: 'inline-block',
                  transition: 'opacity 0.28s ease, transform 0.28s ease',
                  opacity: cityVisible ? 1 : 0,
                  transform: cityVisible ? 'translateY(0)' : 'translateY(-6px)',
                }}>
                  {CITIES[cityIndex]}
                </span>
                <span>• UK Wide</span>
              </span>
            </div>

            <h1 className="text-6xl sm:text-7xl lg:text-8xl font-black leading-[0.9] tracking-tighter">
              INCLUDE.<br />
              <span className="block text-4xl sm:text-5xl lg:text-6xl leading-[0.9]">COLLABORATE.</span>
              <span className="text-hp-accent">GROW.</span>
            </h1>

            <p className="text-xl sm:text-2xl font-medium max-w-lg leading-snug border-l-4 border-hp-dark pl-4">
              A free platform where UK-based Black professionals are equipped with skills and
              information to be the best they can within their chosen careers.
            </p>

            <div className="flex flex-wrap gap-4 pt-4">
              <a
                href="/register"
                className="bg-hp-accent text-white px-8 py-4 rounded-xl border-brutal shadow-brutal interactive-card uppercase font-black text-lg"
              >
                Join the Hub
              </a>
              <a
                href="/partnerships"
                className="bg-white text-hp-dark px-8 py-4 rounded-xl border-brutal shadow-brutal interactive-card uppercase font-black text-lg flex items-center gap-2"
              >
                Partner With Us <ArrowRight weight="bold" />
              </a>
            </div>
          </div>

          {/* Portrait carousel – fills viewport height so images meet the stats bar */}
          <div className="relative grid grid-cols-2 gap-4 h-[calc(100vh-8rem)] min-h-[500px]">
            {/* Left column */}
            <div className="overflow-hidden photo-carousel-col">
              <div className="photo-col-inner">
                {[...heroLeft, ...heroLeft].map((p, i) => (
                  <div
                    key={i}
                    className={`flex-shrink-0 ${p.tall ? 'aspect-[2/3]' : 'aspect-[3/2]'} bg-gray-200 rounded-2xl border-brutal shadow-brutal overflow-hidden group`}
                  >
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img src={p.src} alt={p.alt} className="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" />
                  </div>
                ))}
              </div>
            </div>
            {/* Right column – offset + slightly slower */}
            <div className="overflow-hidden photo-carousel-col pt-12">
              <div className="photo-col-inner photo-col-inner-slow">
                {[...heroRight, ...heroRight].map((p, i) => (
                  <div
                    key={i}
                    className={`flex-shrink-0 ${p.tall ? 'aspect-[2/3]' : 'aspect-[3/2]'} bg-gray-200 rounded-2xl border-brutal shadow-brutal overflow-hidden group`}
                  >
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img src={p.src} alt={p.alt} className="w-full h-full object-cover grayscale group-hover:grayscale-0 transition-all duration-500" />
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </header>

      {/* ── Metrics bar – static single row ── */}
      <section ref={marqueeRef} className="bg-hp-dark text-white border-brutal-b py-8">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-wrap lg:flex-nowrap items-center justify-between gap-y-4 gap-x-4 font-black text-xl sm:text-2xl lg:text-2xl uppercase tracking-tighter">
            <div className="flex items-center gap-2">
              <UsersThree weight="fill" className="text-hp-accent flex-shrink-0" size={24} />
              <span>{members.toLocaleString()}+ Members</span>
            </div>
            <div className="flex items-center gap-2">
              <Buildings weight="fill" className="text-hp-accent flex-shrink-0" size={24} />
              <span>{partners.toLocaleString()}+ Corporate Partners</span>
            </div>
            <div className="flex items-center gap-2">
              <Briefcase weight="fill" className="text-hp-accent flex-shrink-0" size={24} />
              <span>{placements.toLocaleString()}+ Work Placements</span>
            </div>
            <div className="flex items-center gap-2">
              <MagnifyingGlass weight="fill" className="text-hp-accent flex-shrink-0" size={24} />
              <span>{vacancies.toLocaleString()}+ Vacancies</span>
            </div>
          </div>
        </div>
      </section>

      {/* ── Featured Members ── */}
      <section className="py-20 border-brutal-b">
        <div className="px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
          <div>
            <h2 className="text-4xl lg:text-5xl font-black uppercase tracking-tight">The Network</h2>
            <p className="text-xl mt-2 font-medium">Highlighting the talent driving the UK forward.</p>
          </div>
          <div className="flex items-center gap-3">
            <button
              onClick={() => {
                document.getElementById('members-carousel')?.scrollBy({ left: -320, behavior: 'smooth' });
              }}
              className="w-12 h-12 rounded-xl border-brutal shadow-brutal-sm bg-white flex items-center justify-center hover:bg-hp-dark hover:text-white transition-colors font-black text-xl interactive-card"
              aria-label="Previous members"
            >
              ←
            </button>
            <button
              onClick={() => {
                document.getElementById('members-carousel')?.scrollBy({ left: 320, behavior: 'smooth' });
              }}
              className="w-12 h-12 rounded-xl border-brutal shadow-brutal-sm bg-white flex items-center justify-center hover:bg-hp-dark hover:text-white transition-colors font-black text-xl interactive-card"
              aria-label="Next members"
            >
              →
            </button>
            <a
              href="/members"
              className="bg-white text-hp-dark px-6 py-3 rounded-xl border-brutal shadow-brutal-sm hover:shadow-brutal transition-all font-bold uppercase text-sm interactive-card"
            >
              View All Members
            </a>
          </div>
        </div>

        <div
          id="members-carousel"
          className="flex gap-6 overflow-x-auto px-4 sm:px-6 lg:px-8 pb-4 members-carousel max-w-7xl mx-auto"
        >
          {STATIC_MEMBERS.map(({ name, role, img }) => {
            const username = resolvedUsernames[name];
            const href = username
              ? `/members/${encodeURIComponent(username)}`
              : `/members?search=${encodeURIComponent(name)}`;
            return (
            <a
              key={name}
              href={href}
              className="flex-shrink-0 w-72 bg-white rounded-2xl border-brutal shadow-brutal p-4 flex flex-col interactive-card scroll-snap-align-start"
              style={{ textDecoration: 'none', color: 'inherit' }}
            >
              <div className="aspect-square w-full bg-hp-dark rounded-xl mb-4 overflow-hidden border-2 border-hp-dark">
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img src={img} alt={name} className="w-full h-full object-cover" />
              </div>
              <h3 className="font-black text-xl leading-tight flex-grow">{name}</h3>
              <p className="text-sm font-bold text-gray-500 uppercase mt-1 mb-3">{role}</p>
              <span className="inline-block bg-hp-dark text-white text-xs font-black uppercase tracking-wider px-3 py-1.5 rounded-lg border-2 border-hp-dark w-max">
                View Profile →
              </span>
            </a>
            );
          })}
          {/* end padding so last card doesn't sit against the edge */}
          <div className="flex-shrink-0 w-4" />
        </div>
      </section>

      {/* ── Support Initiatives ── */}
      <section className="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <h2 className="text-4xl lg:text-5xl font-black uppercase tracking-tight mb-12 max-w-2xl">
          Providing a number of support initiatives for you
        </h2>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

          {/* Work Placements */}
          <div className="bg-[#EAEAEA] rounded-2xl border-brutal shadow-brutal p-8 flex flex-col interactive-card col-span-1 md:col-span-2 lg:col-span-1">
            <div className="bg-white w-12 h-12 rounded-full border-brutal flex items-center justify-center mb-6">
              <Briefcase weight="fill" size={20} />
            </div>
            <h3 className="font-black text-2xl uppercase mb-3">Work Placements</h3>
            <p className="font-medium mb-8 flex-grow">Designed to be the catalyst for your career growth in the UK.</p>
            <a href="/placements" className="inline-block border-b-4 border-hp-dark font-black uppercase text-sm w-max hover:text-hp-accent hover:border-hp-accent transition-colors">
              Explore Opportunities
            </a>
          </div>

          {/* Job Board */}
          <div className="bg-hp-dark text-white rounded-2xl border-brutal shadow-brutal p-8 flex flex-col interactive-card">
            <div className="bg-hp-accent w-12 h-12 rounded-full border-2 border-white flex items-center justify-center mb-6">
              <ListMagnifyingGlass weight="fill" size={20} />
            </div>
            <h3 className="font-black text-2xl uppercase mb-3">Job Board</h3>
            <p className="font-medium mb-8 text-gray-300 flex-grow">
              Find exclusive job opportunities from organisations that prioritise DE&amp;I.
            </p>
            <a href="/jobs" className="inline-block border-b-4 border-white font-black uppercase text-sm w-max hover:text-hp-accent hover:border-hp-accent transition-colors">
              Browse Jobs
            </a>
          </div>

          {/* Interview Prep */}
          <div className="bg-white rounded-2xl border-brutal shadow-brutal p-8 flex flex-col interactive-card">
            <div className="bg-hp-bg w-12 h-12 rounded-full border-brutal flex items-center justify-center mb-6">
              <MicrophoneStage weight="fill" size={20} />
            </div>
            <h3 className="font-black text-2xl uppercase mb-3">Interview Prep</h3>
            <p className="font-medium mb-8 flex-grow">
              A personalised approach to cater to your specific needs and career aspirations.
            </p>
            <a href="/interview-prep" className="inline-block border-b-4 border-hp-dark font-black uppercase text-sm w-max hover:text-hp-accent hover:border-hp-accent transition-colors">
              Start Preparing
            </a>
          </div>

          {/* CV Clinic — spans 2 */}
          <div className="bg-hp-accent text-white rounded-2xl border-brutal shadow-brutal p-8 flex flex-col md:flex-row items-start md:items-center gap-8 interactive-card md:col-span-2">
            <div className="flex-grow">
              <div className="bg-white text-hp-dark w-12 h-12 rounded-full border-2 border-white flex items-center justify-center mb-6">
                <FileText weight="fill" size={20} />
              </div>
              <h3 className="font-black text-2xl uppercase mb-3">CV Clinic</h3>
              <p className="font-medium mb-6 text-white max-w-md">
                Get personalised feedback and guidance from our professionals to create a standout
                resume highlighting your skills.
              </p>
            </div>
            <a
              href="/dashboard/cv-clinic"
              className="bg-white text-hp-dark px-8 py-4 rounded-xl border-2 border-white shadow-brutal-sm uppercase font-black text-sm whitespace-nowrap hover:bg-hp-bg transition-colors"
            >
              Request Feedback
            </a>
          </div>

          {/* Mentorship */}
          <div className="bg-white rounded-2xl border-brutal shadow-brutal p-8 flex flex-col interactive-card">
            <div className="bg-hp-bg w-12 h-12 rounded-full border-brutal flex items-center justify-center mb-6">
              <Users weight="fill" size={20} />
            </div>
            <h3 className="font-black text-2xl uppercase mb-3">Mentorship</h3>
            <p className="font-medium mb-8 flex-grow">Connect with mentors who actively support your growth and development.</p>
            <a href="https://pairedbybpu.uk" className="inline-block border-b-4 border-hp-dark font-black uppercase text-sm w-max hover:text-hp-accent hover:border-hp-accent transition-colors">
              Join Programme
            </a>
          </div>

          {/* Courses */}
          <div className="bg-white rounded-2xl border-brutal shadow-brutal p-8 flex flex-col interactive-card">
            <div className="bg-hp-bg w-12 h-12 rounded-full border-brutal flex items-center justify-center mb-6">
              <GraduationCap weight="fill" size={20} />
            </div>
            <h3 className="font-black text-2xl uppercase mb-3">UK Courses</h3>
            <p className="font-medium mb-8 flex-grow">Propel your career to new heights with these UK accredited courses.</p>
            <a href="/courses" className="inline-block border-b-4 border-hp-dark font-black uppercase text-sm w-max hover:text-hp-accent hover:border-hp-accent transition-colors">
              Start Learning
            </a>
          </div>

          {/* Events */}
          <div className="bg-white rounded-2xl border-brutal shadow-brutal p-8 flex flex-col interactive-card">
            <div className="bg-hp-bg w-12 h-12 rounded-full border-brutal flex items-center justify-center mb-6">
              <CalendarStar weight="fill" size={20} />
            </div>
            <h3 className="font-black text-2xl uppercase mb-3">Events</h3>
            <p className="font-medium mb-8 flex-grow">Attend our engaging workshops, seminars, and networking events.</p>
            <a href="/events" className="inline-block border-b-4 border-hp-dark font-black uppercase text-sm w-max hover:text-hp-accent hover:border-hp-accent transition-colors">
              See Upcoming
            </a>
          </div>

          {/* Support */}
          <div className="bg-[#EAEAEA] rounded-2xl border-brutal shadow-brutal p-8 flex flex-col interactive-card">
            <div className="bg-white w-12 h-12 rounded-full border-brutal flex items-center justify-center mb-6">
              <Lifebuoy weight="fill" size={20} />
            </div>
            <h3 className="font-black text-2xl uppercase mb-3">Support</h3>
            <p className="font-medium mb-8 flex-grow">Get expert advice on your membership, career, and employability.</p>
            <a href="/support" className="inline-block border-b-4 border-hp-dark font-black uppercase text-sm w-max hover:text-hp-accent hover:border-hp-accent transition-colors">
              Access Support
            </a>
          </div>

        </div>
      </section>

      {/* ── Report ── */}
      <section id="report" className="bg-hp-dark text-white py-24 border-brutal-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
            <div>
              <div className="inline-block bg-hp-accent text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-6 border-2 border-white">
                Exclusive Publication
              </div>
              <h2 className="text-5xl sm:text-6xl font-black uppercase tracking-tight leading-none mb-6">
                Harnessing<br />Talent Report<br />2026
              </h2>
              <p className="text-xl font-medium text-gray-300 mb-8 max-w-lg border-l-4 border-hp-accent pl-4">
                This report presents findings from the Black Professionals UK Big Survey, offering a
                snapshot of the lived realities of Black professionals in Scotland. It builds on
                BPU&apos;s mission to dismantle systemic barriers and empower Scotland to unlock its
                competitive edge.
              </p>
              <a
                href="https://blackprofessionals.uk/bpu-harnessing-talent-report-2026/"
                target="_blank"
                rel="noopener noreferrer"
                className="inline-block bg-white text-hp-dark px-8 py-4 rounded-xl border-2 border-white shadow-[6px_6px_0px_0px_rgba(217,56,30,1)] hover:shadow-[8px_8px_0px_0px_rgba(217,56,30,1)] transition-all uppercase font-black text-lg"
              >
                View the Report
              </a>
            </div>

            {/* Report cover photo */}
            <div className="relative w-full aspect-[3/2] rounded-2xl border-2 border-gray-700 overflow-hidden">
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img
                src="https://blackprofessionals.uk/wp-content/uploads/2026/04/TBCC2026-103-scaled.jpg"
                alt="Harnessing Talent Report 2026 – BPU event"
                className="w-full h-full object-cover"
              />
            </div>
          </div>
        </div>
      </section>

      {/* ── Recent Engagements ── */}
      <section className="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto border-brutal-b">
        <div className="flex justify-between items-end mb-12">
          <div>
            <h2 className="text-4xl lg:text-5xl font-black uppercase tracking-tight">Recent Engagements</h2>
            <p className="text-xl mt-2 font-medium">Catch up on our most recent updates and conversations.</p>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
          {posts.map((post) => {
            const title = post.title.rendered.replace(/&amp;/g, '&').replace(/&#8217;/g, "'").replace(/&#8211;/g, '–').replace(/<[^>]+>/g, '');
            const excerpt = post.excerpt.rendered.replace(/<[^>]+>/g, '').replace(/\[&hellip;\]|\[…\]/g, '…').trim();
            const rawCategory = post._embedded?.['wp:term']?.[0]?.[0]?.name ?? 'Article';
            const category = rawCategory.replace(/&amp;/g, '&').replace(/&#8217;/g, "'").replace(/&#8211;/g, '–').replace(/&[a-z]+;/gi, '');
            const image = post._embedded?.['wp:featuredmedia']?.[0]?.source_url;
            return (
              <article key={post.id} className="flex flex-col group cursor-pointer">
                <a href={post.link} target="_blank" rel="noopener noreferrer" className="block w-full aspect-[4/3] bg-hp-dark rounded-2xl border-brutal shadow-brutal mb-6 overflow-hidden relative">
                  <div className="absolute top-4 left-4 bg-white text-black text-xs font-black uppercase px-3 py-1 rounded-full border-2 border-black z-10">
                    {category}
                  </div>
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  {image ? (
                    <img
                      src={image}
                      alt={title}
                      className="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-500"
                    />
                  ) : (
                    <div className="w-full h-full flex items-end p-6">
                      <span className="text-white/40 font-black text-4xl leading-none uppercase">BPU</span>
                    </div>
                  )}
                  <div className="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
                    <div className="w-16 h-16 bg-white rounded-full flex items-center justify-center border-brutal pl-1">
                      <Play weight="fill" size={24} />
                    </div>
                  </div>
                </a>
                <h3 className="font-black text-xl leading-tight mb-2 group-hover:text-hp-accent transition-colors">
                  {title}
                </h3>
                {excerpt && <p className="text-sm text-gray-600 font-medium mb-4 line-clamp-2">{excerpt}</p>}
                <a href={post.link} target="_blank" rel="noopener noreferrer" className="font-bold uppercase text-sm border-b-2 border-black w-max mt-auto hover:text-hp-accent hover:border-hp-accent transition-colors">
                  Catch Up
                </a>
              </article>
            );
          })}
        </div>
      </section>

      {/* ── Partners & Press ── */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl font-black uppercase tracking-tight mb-4">
            Organisations Championing Diversity &amp; Inclusion
          </h2>
          <p className="text-lg font-medium text-gray-600 mb-12">
            Proudly partnering with industry leaders to create actionable change.
          </p>

          {/* Auto-scrolling partner logo marquee */}
          <div className="marquee-container mb-16">
            <div className="marquee-content">
              {[...PARTNERS, ...PARTNERS].map((p, i) => (
                <a
                  key={i}
                  href={p.href}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="inline-flex items-center justify-center px-8 whitespace-nowrap"
                >
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img
                    src={p.logo}
                    alt={p.name}
                    className="h-10 w-auto object-contain grayscale hover:grayscale-0 transition-all opacity-60 hover:opacity-100"
                  />
                </a>
              ))}
            </div>
          </div>

          <a
            href="/partnerships"
            className="inline-block bg-hp-bg text-black px-6 py-3 rounded-xl border-brutal shadow-brutal-sm hover:shadow-brutal interactive-card uppercase font-black text-sm mb-24"
          >
            Learn More About Partnering
          </a>

          <div className="border-t-4 border-black pt-12">
            <h3 className="text-xl font-black uppercase tracking-tight mb-8">Press Mentions</h3>
            <div className="flex justify-center items-center gap-10 flex-wrap">
              {[
                { name: 'Insider',               href: 'https://www.insider.co.uk/news/diversity-advocate-calls-out-subtle-23722129',                                                                                        logo: 'https://blackprofessionals.uk/wp-content/uploads/2024/01/Insider.png' },
                { name: 'Accounting & Business', href: 'https://abmagazine.accaglobal.com/global/articles/2021/jan/interviews/diversity-is-everyone-s-business.html',                                                         logo: 'https://blackprofessionals.uk/wp-content/uploads/2024/01/Accounting-Business.png' },
                { name: 'Scottish Business News', href: 'https://scottishbusiness.news/black-professionals-uk-strengthens-scotlands-workforce-diversity-with-2025-black-interns-and-black-talents-programmes/',               logo: 'https://blackprofessionals.uk/wp-content/uploads/2024/01/Scottish-Business-News.png' },
                { name: 'The Scotsman',           href: 'https://www.scotsman.com/business/call-for-scots-financial-firms-to-step-up-support-of-black-workers-3164616',                                                        logo: 'https://blackprofessionals.uk/wp-content/uploads/2024/01/scotsman-logo-1.png' },
              ].map(({ name, href, logo }) => (
                <a
                  key={name}
                  href={href}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="inline-flex items-center justify-center"
                >
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img
                    src={logo}
                    alt={name}
                    className="h-8 w-auto object-contain grayscale hover:grayscale-0 transition-all opacity-60 hover:opacity-100"
                  />
                </a>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* ── Footer ── */}
      <footer className="bg-hp-dark text-white pt-20 pb-10 border-t-[8px] border-hp-accent">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 mb-16">

            {/* Brand */}
            <div className="lg:col-span-1">
              <div className="mb-6">
                <div className="inline-block bg-white rounded-xl px-3 py-2">
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img src="/images/bpu-logo.png" alt="Black Professionals UK" className="h-10 w-auto" />
                </div>
              </div>
              <p className="text-gray-400 font-medium text-sm mb-6">
                Equipping professionals. Meeting inclusion ambitions. Dismantling barriers.
              </p>
              <div className="flex gap-4">
                <a href="https://linkedin.com/company/blackprofessionalsuk" className="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-hp-accent transition-colors">
                  <LinkedinLogo weight="fill" size={20} />
                </a>
                <a href="https://twitter.com/blackprosuk" className="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-hp-accent transition-colors">
                  <TwitterLogo weight="fill" size={20} />
                </a>
                <a href="https://instagram.com/blackprofessionalsuk" className="w-10 h-10 bg-gray-800 rounded-full flex items-center justify-center hover:bg-hp-accent transition-colors">
                  <InstagramLogo weight="fill" size={20} />
                </a>
              </div>
            </div>

            {/* Platform links */}
            <div>
              <h4 className="font-black uppercase tracking-wider mb-6 text-hp-accent">Platform</h4>
              <ul className="space-y-3 font-bold text-sm text-gray-300">
                {[
                  ['Work Placements', '/placements'],
                  ['Job Board', '/jobs'],
                  ['CV Clinic & Interview Prep', '/dashboard/cv-clinic'],
                  ['Free Courses', '/courses'],
                  ['Mentorship Programme', 'https://pairedbybpu.uk'],
                  ['Membership Support', '/support'],
                ].map(([label, href]) => (
                  <li key={label}><a href={href} className="hover:text-white transition-colors">{label}</a></li>
                ))}
              </ul>
            </div>

            {/* Network links */}
            <div>
              <h4 className="font-black uppercase tracking-wider mb-6 text-hp-accent">Network</h4>
              <ul className="space-y-3 font-bold text-sm text-gray-300">
                {[
                  ['Our Partners', '/partnerships'],
                  ['Become a Partner', '/partnerships#join'],
                  ['Post a Job', '/employer/jobs'],
                  ['Community & Networking', '/community'],
                  ['Stories & Articles', '/stories'],
                ].map(([label, href]) => (
                  <li key={label}><a href={href} className="hover:text-white transition-colors">{label}</a></li>
                ))}
              </ul>
            </div>

            {/* Company links */}
            <div>
              <h4 className="font-black uppercase tracking-wider mb-6 text-hp-accent">Company</h4>
              <ul className="space-y-3 font-bold text-sm text-gray-300">
                {[
                  ['About Us', '/about'],
                  ['Contact Us', '/contact'],
                  ['In The Press', '/press'],
                  ['Resources', '/resources'],
                ].map(([label, href]) => (
                  <li key={label}><a href={href} className="hover:text-white transition-colors">{label}</a></li>
                ))}
              </ul>
            </div>
          </div>

          {/* Bottom bar */}
          <div className="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-xs font-bold text-gray-500 uppercase tracking-wide">
            <p>&copy; {new Date().getFullYear()} Black Professionals United Kingdom. All rights reserved.</p>
            <div className="flex gap-6">
              <a href="/terms"   className="hover:text-white transition-colors">Usage Terms</a>
              <a href="/cookies" className="hover:text-white transition-colors">Cookie Policy</a>
              <a href="/privacy" className="hover:text-white transition-colors">Privacy Policy</a>
            </div>
          </div>
        </div>
      </footer>

    </div>
  );
}

