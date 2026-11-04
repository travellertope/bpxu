'use client';

import { useState, useEffect, useRef, useCallback } from 'react';
import { ArrowLeft, ArrowRight } from '@phosphor-icons/react';

const MILESTONES = [
  {
    year: 2016,
    era: 'Foundation',
    title: 'Edinburgh Black Professionals',
    desc: 'EBP launches with its maiden event in Edinburgh, bringing together Black professionals to build community and open doors in Scotland\'s capital.',
    dark: false,
  },
  {
    year: 2017,
    era: 'Community',
    title: 'Mentorship Takes Shape',
    desc: 'The Mentorship Programme launches — creating structured one-to-one connections between experienced professionals and those starting out.',
    dark: false,
  },
  {
    year: 2018,
    era: 'Digital',
    title: 'Going Online',
    desc: 'The website launches, extending EBP\'s reach beyond in-person events and laying the groundwork for a national community.',
    dark: false,
  },
  {
    year: 2019,
    era: 'Official',
    title: 'EBP Incorporated',
    desc: 'Edinburgh Black Professionals formally incorporates — transforming from a grassroots network into a recognised organisation with legal standing.',
    dark: false,
  },
  {
    year: 2020,
    era: 'Team & Identity',
    title: 'First Hire. New Name.',
    desc: 'The first employee joins the team. EBP rebrands to Black Professionals Scotland (BPS), reflecting an ambition that now spans the whole country.',
    dark: false,
  },
  {
    year: 2021,
    era: 'Partnerships',
    title: 'Breaking Through',
    desc: 'BPS secures its first corporate partner, launches #BlackScotInterns to open internship pipelines, and membership crosses the 1,000 mark.',
    dark: false,
    stat: '1,000',
    statLabel: 'members',
  },
  {
    year: 2022,
    era: 'Recognition',
    title: 'The Black Scottish Awards',
    desc: 'The Black Scottish Awards launch — the first ceremony of its kind in Scotland, celebrating exceptional Black professionals and the organisations championing them.',
    dark: false,
  },
  {
    year: 2023,
    era: 'Next Generation',
    title: 'Young Black Future',
    desc: 'Young Black Future launches to support the next generation entering the workforce — equipping students and early-career professionals with skills and connections.',
    dark: false,
  },
  {
    year: 2024,
    era: 'National',
    title: 'Conference & New Identity',
    desc: 'The Big Careers Conference debuts, bringing hundreds together. BPS rebrands to Black Professionals United Kingdom (BPU), cementing a truly UK-wide mission.',
    dark: false,
  },
  {
    year: 2025,
    era: 'Scale',
    title: '7,000 Strong',
    desc: 'Membership hits 7,000 — a community of Black professionals spanning every major UK city and sector, from finance and tech to law and healthcare.',
    dark: false,
    stat: '7,000',
    statLabel: 'members',
  },
  {
    year: 2026,
    era: '10th Anniversary',
    title: 'A Decade of Impact',
    desc: 'Our Founder is awarded an Honorary Doctorate — a fitting crown on 10 years of transformative work for Black professionals across the UK.',
    dark: true,
  },
];

function useCountUp(target: number, active: boolean) {
  const [val, setVal] = useState(0);
  useEffect(() => {
    if (!active) return;
    let n = 0;
    const inc = target / 60;
    const id = setInterval(() => {
      n += inc;
      if (n >= target) { setVal(target); clearInterval(id); }
      else setVal(Math.ceil(n));
    }, 16);
    return () => clearInterval(id);
  }, [target, active]);
  return val;
}

function StatBadge({ stat, label }: { stat: string; label: string }) {
  const ref = useRef<HTMLDivElement>(null);
  const [active, setActive] = useState(false);
  const num = parseInt(stat.replace(/,/g, ''), 10);
  const count = useCountUp(num, active);

  useEffect(() => {
    const el = ref.current;
    if (!el) return;
    const obs = new IntersectionObserver(([e]) => { if (e.isIntersecting) { setActive(true); obs.disconnect(); } }, { threshold: 0.5 });
    obs.observe(el);
    return () => obs.disconnect();
  }, []);

  return (
    <div ref={ref} className="inline-flex items-baseline gap-1">
      <span className="text-4xl font-black text-hp-accent leading-none tabular-nums">
        {count.toLocaleString()}+
      </span>
      <span className="text-xs font-black uppercase tracking-wider text-hp-dark/50 ml-1">{label}</span>
    </div>
  );
}

export default function MilestonesSection() {
  const [active, setActive] = useState(0);
  const [paused, setPaused] = useState(false);
  const trackRef = useRef<HTMLDivElement>(null);
  const cardRefs = useRef<(HTMLDivElement | null)[]>([]);
  const autoRef  = useRef<ReturnType<typeof setInterval> | null>(null);

  const scrollTo = useCallback((idx: number) => {
    const card = cardRefs.current[idx];
    if (card) card.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
    setActive(idx);
  }, []);

  // Auto-advance
  useEffect(() => {
    if (paused) return;
    autoRef.current = setInterval(() => {
      setActive(i => {
        const next = (i + 1) % MILESTONES.length;
        const card = cardRefs.current[next];
        if (card) card.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
        return next;
      });
    }, 4000);
    return () => { if (autoRef.current) clearInterval(autoRef.current); };
  }, [paused]);

  // Keyboard nav
  useEffect(() => {
    function onKey(e: KeyboardEvent) {
      if (e.key === 'ArrowLeft')  scrollTo(Math.max(0, active - 1));
      if (e.key === 'ArrowRight') scrollTo(Math.min(MILESTONES.length - 1, active + 1));
    }
    window.addEventListener('keydown', onKey);
    return () => window.removeEventListener('keydown', onKey);
  }, [active, scrollTo]);

  // Track scroll position to sync dots
  useEffect(() => {
    const track = trackRef.current;
    if (!track) return;
    function onScroll() {
      const cards = cardRefs.current;
      const trackRect = track!.getBoundingClientRect();
      const center = trackRect.left + trackRect.width / 2;
      let closest = 0;
      let closestDist = Infinity;
      cards.forEach((card, i) => {
        if (!card) return;
        const r = card.getBoundingClientRect();
        const dist = Math.abs(r.left + r.width / 2 - center);
        if (dist < closestDist) { closestDist = dist; closest = i; }
      });
      setActive(closest);
    }
    track.addEventListener('scroll', onScroll, { passive: true });
    return () => track.removeEventListener('scroll', onScroll);
  }, []);

  return (
    <section
      className="py-20 bg-hp-bg border-b-2 border-hp-dark overflow-hidden"
      onMouseEnter={() => setPaused(true)}
      onMouseLeave={() => setPaused(false)}
      onFocus={() => setPaused(true)}
      onBlur={() => setPaused(false)}
    >
      {/* Header */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-10">
        <div className="flex items-end justify-between gap-4 flex-wrap">
          <div>
            <span className="inline-flex items-center gap-2 border-brutal rounded-full px-4 py-1.5 bg-white shadow-brutal-sm mb-4">
              <span className="w-2 h-2 rounded-full bg-hp-accent" />
              <span className="text-xs font-black uppercase tracking-widest">10-Year Journey (2016 – 2026)</span>
            </span>
            <h2 className="text-4xl sm:text-5xl font-black uppercase tracking-tight leading-none">
              A Decade of<br className="sm:hidden" /> Milestones
            </h2>
            <p className="mt-3 text-base text-text-2 max-w-lg leading-snug">
              From a grassroots network in Edinburgh to a UK-wide force for change.
            </p>
          </div>
          {/* Arrow controls */}
          <div className="flex items-center gap-3">
            <button
              onClick={() => scrollTo(Math.max(0, active - 1))}
              disabled={active === 0}
              className="w-10 h-10 rounded-xl border-brutal shadow-brutal-sm bg-white flex items-center justify-center disabled:opacity-30 hover:bg-hp-dark hover:text-white transition-colors"
              aria-label="Previous"
            >
              <ArrowLeft size={18} weight="bold" />
            </button>
            <button
              onClick={() => scrollTo(Math.min(MILESTONES.length - 1, active + 1))}
              disabled={active === MILESTONES.length - 1}
              className="w-10 h-10 rounded-xl border-brutal shadow-brutal-sm bg-white flex items-center justify-center disabled:opacity-30 hover:bg-hp-dark hover:text-white transition-colors"
              aria-label="Next"
            >
              <ArrowRight size={18} weight="bold" />
            </button>
          </div>
        </div>

        {/* Year dot scrubber */}
        <div className="mt-6 flex items-center gap-0">
          {MILESTONES.map((m, i) => (
            <button
              key={m.year}
              onClick={() => scrollTo(i)}
              className="flex flex-col items-center group flex-1"
              aria-label={String(m.year)}
            >
              {/* connector line */}
              <div className={`w-full h-0.5 transition-colors duration-300 ${i <= active ? 'bg-hp-accent' : 'bg-hp-dark/15'}`} />
              {/* dot */}
              <div className={`w-3 h-3 rounded-full border-2 transition-all duration-300 mt-[-6px] ${
                i === active
                  ? 'bg-hp-accent border-hp-accent scale-125'
                  : i < active
                  ? 'bg-hp-accent/40 border-hp-accent/40'
                  : 'bg-white border-hp-dark/30 group-hover:border-hp-accent'
              }`} />
              {/* year label - only show on wider screens for every other */}
              <span className={`text-[10px] font-black uppercase mt-1 transition-colors duration-300 hidden sm:block ${
                i === active ? 'text-hp-accent' : 'text-hp-dark/30'
              }`}>
                {m.year}
              </span>
            </button>
          ))}
        </div>
      </div>

      {/* Card track */}
      <div
        ref={trackRef}
        className="flex gap-5 overflow-x-auto px-4 sm:px-6 lg:px-8 pb-4 scroll-smooth"
        style={{ scrollSnapType: 'x mandatory', scrollbarWidth: 'none', msOverflowStyle: 'none' }}
      >
        {/* Left spacer to allow centering first card */}
        <div className="flex-shrink-0 w-[calc((100vw-min(100vw,56rem))/2)]" aria-hidden="true" />

        {MILESTONES.map((m, i) => (
          <div
            key={m.year}
            ref={el => { cardRefs.current[i] = el; }}
            onClick={() => scrollTo(i)}
            style={{ scrollSnapAlign: 'center', flexShrink: 0, width: 'min(340px, 80vw)' }}
            className={`rounded-2xl border-brutal shadow-brutal p-7 flex flex-col gap-4 cursor-pointer transition-all duration-300 ${
              i === active ? 'scale-100 opacity-100' : 'scale-95 opacity-60 hover:opacity-80'
            } ${m.dark ? 'bg-hp-dark text-white' : 'bg-white text-hp-dark'}`}
          >
            {/* Year + Era */}
            <div className="flex items-center justify-between gap-3">
              <span className="text-5xl font-black text-hp-accent leading-none">{m.year}</span>
              <span className={`text-[10px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full border ${
                m.dark ? 'border-white/20 text-white/60' : 'border-hp-dark/20 text-hp-dark/60'
              }`}>
                {m.era}
              </span>
            </div>

            <div className={`w-10 h-0.5 ${m.dark ? 'bg-white/20' : 'bg-hp-dark/20'}`} />

            <div className="flex-1 space-y-2">
              <p className={`font-black uppercase tracking-tight text-base leading-tight ${m.dark ? 'text-white' : 'text-hp-dark'}`}>
                {m.title}
              </p>
              <p className={`text-sm leading-relaxed ${m.dark ? 'text-white/60' : 'text-text-2'}`}>
                {m.desc}
              </p>
            </div>

            {/* Animated stat */}
            {m.stat && i === active && (
              <StatBadge stat={m.stat} label={m.statLabel!} />
            )}
          </div>
        ))}

        {/* Right spacer */}
        <div className="flex-shrink-0 w-[calc((100vw-min(100vw,56rem))/2)]" aria-hidden="true" />
      </div>

      {/* Mobile year indicator */}
      <div className="sm:hidden text-center mt-4 text-xs font-black uppercase tracking-widest text-hp-dark/40">
        {MILESTONES[active].year} · {active + 1} of {MILESTONES.length}
      </div>
    </section>
  );
}
