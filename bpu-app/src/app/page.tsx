'use client';

import { useState, useEffect, useRef } from 'react';
import {
  List, ArrowRight,
  UsersThree, Buildings, Briefcase, MagnifyingGlass,
  ListMagnifyingGlass, MicrophoneStage, FileText, Users,
  GraduationCap, CalendarStar, Lifebuoy, ChartBar, Play,
  LinkedinLogo, TwitterLogo, InstagramLogo,
} from '@phosphor-icons/react';

/* ─── hero photo data ─── */
const HERO_PHOTOS = [
  { src: 'https://placehold.co/400x600/0F0F0F/FFFFFF?text=Portrait+1',  alt: 'Member portrait 1',  tall: true  },
  { src: 'https://placehold.co/400x300/D9381E/FFFFFF?text=Event+1',     alt: 'BPU event 1',        tall: false },
  { src: 'https://placehold.co/400x600/1A1A1A/FFFFFF?text=Portrait+2',  alt: 'Member portrait 2',  tall: true  },
  { src: 'https://placehold.co/400x300/F4F4F0/0F0F0F?text=Workshop+1',  alt: 'Workshop 1',         tall: false },
  { src: 'https://placehold.co/400x600/0F0F0F/FFFFFF?text=Portrait+3',  alt: 'Member portrait 3',  tall: true  },
  { src: 'https://placehold.co/400x300/D9381E/FFFFFF?text=Event+2',     alt: 'BPU event 2',        tall: false },
  { src: 'https://placehold.co/400x600/2D2D2D/FFFFFF?text=Portrait+4',  alt: 'Member portrait 4',  tall: true  },
  { src: 'https://placehold.co/400x300/F4F4F0/0F0F0F?text=Networking',  alt: 'Networking',         tall: false },
  { src: 'https://placehold.co/400x600/0F0F0F/FFFFFF?text=Portrait+5',  alt: 'Member portrait 5',  tall: true  },
  { src: 'https://placehold.co/400x300/D9381E/FFFFFF?text=Conference',  alt: 'Conference',         tall: false },
  { src: 'https://placehold.co/400x600/1A1A1A/FFFFFF?text=Portrait+6',  alt: 'Member portrait 6',  tall: true  },
  { src: 'https://placehold.co/400x300/F4F4F0/0F0F0F?text=Awards',      alt: 'Award night',        tall: false },
  { src: 'https://placehold.co/400x600/0F0F0F/FFFFFF?text=Portrait+7',  alt: 'Member portrait 7',  tall: true  },
  { src: 'https://placehold.co/400x300/D9381E/FFFFFF?text=Event+3',     alt: 'BPU event 3',        tall: false },
  { src: 'https://placehold.co/400x600/2D2D2D/FFFFFF?text=Portrait+8',  alt: 'Member portrait 8',  tall: true  },
  { src: 'https://placehold.co/400x300/F4F4F0/0F0F0F?text=Workshop+2',  alt: 'Workshop 2',         tall: false },
  { src: 'https://placehold.co/400x600/0F0F0F/FFFFFF?text=Portrait+9',  alt: 'Member portrait 9',  tall: true  },
  { src: 'https://placehold.co/400x300/D9381E/FFFFFF?text=Panel',       alt: 'Panel discussion',   tall: false },
  { src: 'https://placehold.co/400x600/1A1A1A/FFFFFF?text=Portrait+10', alt: 'Member portrait 10', tall: true  },
  { src: 'https://placehold.co/400x300/F4F4F0/0F0F0F?text=Mentoring',   alt: 'Mentoring session',  tall: false },
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

export default function HomePage() {
  const [menuOpen, setMenuOpen] = useState(false);
  const [navUp, setNavUp] = useState(false);
  const [counting, setCounting] = useState(false);
  const lastY = useRef(0);
  const marqueeRef = useRef<HTMLDivElement>(null);

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
          <div className="space-y-8 z-10">
            <div className="inline-flex items-center gap-2 border-brutal rounded-full px-4 py-2 bg-white shadow-brutal-sm">
              <div className="w-3 h-3 bg-hp-accent rounded-full animate-pulse" />
              <span className="font-bold text-sm uppercase tracking-wider">Glasgow HQ • UK Wide</span>
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
          <div className="flex flex-wrap items-center justify-between gap-y-6 gap-x-6 font-black text-2xl sm:text-3xl lg:text-4xl uppercase tracking-tighter">
            <div className="flex items-center gap-3">
              <UsersThree weight="fill" className="text-hp-accent flex-shrink-0" size={32} />
              <span>{members.toLocaleString()}+ Members</span>
            </div>
            <div className="w-2 h-2 bg-white rounded-full hidden lg:block flex-shrink-0" />
            <div className="flex items-center gap-3">
              <Buildings weight="fill" className="text-hp-accent flex-shrink-0" size={32} />
              <span>{partners.toLocaleString()}+ Corporate Partners</span>
            </div>
            <div className="w-2 h-2 bg-white rounded-full hidden lg:block flex-shrink-0" />
            <div className="flex items-center gap-3">
              <Briefcase weight="fill" className="text-hp-accent flex-shrink-0" size={32} />
              <span>{placements.toLocaleString()}+ Work Placements</span>
            </div>
            <div className="w-2 h-2 bg-white rounded-full hidden lg:block flex-shrink-0" />
            <div className="flex items-center gap-3">
              <MagnifyingGlass weight="fill" className="text-hp-accent flex-shrink-0" size={32} />
              <span>{vacancies.toLocaleString()}+ Vacancies</span>
            </div>
          </div>
        </div>
      </section>

      {/* ── Featured Members ── */}
      <section className="py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto border-brutal-b">
        <div className="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
          <div>
            <h2 className="text-4xl lg:text-5xl font-black uppercase tracking-tight">The Network</h2>
            <p className="text-xl mt-2 font-medium">Highlighting the talent driving the UK forward.</p>
          </div>
          <a
            href="/members"
            className="bg-white text-hp-dark px-6 py-3 rounded-xl border-brutal shadow-brutal-sm hover:shadow-brutal transition-all font-bold uppercase text-sm interactive-card"
          >
            View All Members
          </a>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          {[
            { name: 'Lawrence Dada',    role: 'Process and FA Engineer' },
            { name: 'Olamide Kosile',   role: 'Cloud Infrastructure Engineer' },
            { name: 'Augustine Ologun', role: 'Cloud Infrastructure Engineer' },
            { name: 'Michael David',    role: 'Tech Support' },
          ].map(({ name, role }) => (
            <div key={name} className="bg-white rounded-2xl border-brutal shadow-brutal p-4 flex flex-col interactive-card">
              <div className="aspect-square w-full bg-gray-200 rounded-xl mb-4 overflow-hidden border-2 border-black">
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img
                  src={`https://placehold.co/400x400/0F0F0F/FFFFFF?text=${encodeURIComponent(name)}`}
                  alt={name}
                  className="w-full h-full object-cover"
                />
              </div>
              <h3 className="font-black text-xl leading-tight">{name}</h3>
              <p className="text-sm font-bold text-gray-600 uppercase mt-1">{role}</p>
            </div>
          ))}
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
                href="/report"
                className="inline-block bg-white text-hp-dark px-8 py-4 rounded-xl border-2 border-white shadow-[6px_6px_0px_0px_rgba(217,56,30,1)] hover:shadow-[8px_8px_0px_0px_rgba(217,56,30,1)] transition-all uppercase font-black text-lg"
              >
                View Interactive Report
              </a>
            </div>

            {/* Data viz placeholder */}
            <div className="relative w-full aspect-square bg-[#1A1A1A] rounded-2xl border-2 border-gray-700 overflow-hidden flex items-center justify-center group cursor-pointer">
              <div className="absolute inset-0 bg-radial-[at_center] from-gray-800 to-transparent opacity-50 group-hover:opacity-100 transition-opacity duration-500" />
              <div className="text-center z-10 p-8 border-brutal bg-hp-dark shadow-brutal transform group-hover:scale-105 transition-transform duration-300">
                <ChartBar weight="light" size={64} className="text-hp-accent mb-4 mx-auto" />
                <h3 className="font-black uppercase text-xl">Unlock the Data</h3>
                <p className="text-sm text-gray-400 mt-2 font-medium">Explore the live dashboard</p>
              </div>
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
          {[
            { tag: 'Internship Week',  name: 'Daniel Olanrewaju', label: 'Intern Spotlight' },
            { tag: 'Alumni Mentor',    name: 'Vambo Maswiswi',    label: 'Alumni Mentor Spotlight' },
            { tag: 'Alumni Mentor',    name: 'Olabisi Oloyede',   label: 'Alumni Mentor Spotlight' },
          ].map(({ tag, name, label }) => (
            <article key={name} className="flex flex-col group cursor-pointer">
              <div className="w-full aspect-[4/3] bg-gray-200 rounded-2xl border-brutal shadow-brutal mb-6 overflow-hidden relative">
                <div className="absolute top-4 left-4 bg-white text-black text-xs font-black uppercase px-3 py-1 rounded-full border-2 border-black z-10">
                  {tag}
                </div>
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img
                  src={`https://placehold.co/600x450/0F0F0F/FFFFFF?text=${encodeURIComponent(name)}`}
                  alt={name}
                  className="w-full h-full object-cover grayscale group-hover:grayscale-0 group-hover:scale-105 transition-all duration-500"
                />
                <div className="absolute inset-0 flex items-center justify-center bg-black/20 opacity-0 group-hover:opacity-100 transition-opacity">
                  <div className="w-16 h-16 bg-white rounded-full flex items-center justify-center border-brutal pl-1">
                    <Play weight="fill" size={24} />
                  </div>
                </div>
              </div>
              <h3 className="font-black text-2xl leading-tight mb-3 group-hover:text-hp-accent transition-colors">
                {label}: A Conversation with {name}
              </h3>
              <a href="#" className="font-bold uppercase text-sm border-b-2 border-black w-max mt-auto">
                Catch Up
              </a>
            </article>
          ))}
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

          {/* Auto-scrolling single-row carousel */}
          <div className="marquee-container mb-16">
            <div className="marquee-content">
              {['BNY', 'FreeAgent', 'insider.co.uk', 'NatWest Group', 'Walter Scott', 'Addleshaw Goddard',
                'BNY', 'FreeAgent', 'insider.co.uk', 'NatWest Group', 'Walter Scott', 'Addleshaw Goddard'].map((p, i) => (
                <span key={i} className="text-2xl font-black text-gray-400 hover:text-black transition-colors cursor-pointer px-12 whitespace-nowrap">
                  {p}
                </span>
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
            <div className="flex justify-center items-center gap-12 flex-wrap">
              <div className="font-black text-xl md:text-2xl uppercase tracking-tighter">SCOTTISH BUSINESS NEWS</div>
              <div className="w-2 h-2 bg-black rounded-full" />
              <div className="font-black text-xl md:text-2xl uppercase tracking-tighter">THE SCOTSMAN</div>
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
