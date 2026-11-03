'use client';

import { useState, useEffect, useRef } from 'react';
import { List } from '@phosphor-icons/react';

export default function SiteHeader() {
  const [menuOpen, setMenuOpen] = useState(false);
  const [navUp, setNavUp]       = useState(false);
  const lastY                   = useRef(0);

  useEffect(() => {
    function onScroll() {
      const y = window.scrollY;
      setNavUp(y > lastY.current && y > 100);
      lastY.current = y;
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  return (
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
            <a href="/">
              <img src="/images/bpu-logo.png" alt="Black Professionals UK" className="h-12 w-auto" />
            </a>
          </div>

          {/* Desktop nav */}
          <div className="hidden lg:flex space-x-6 items-center font-bold text-sm uppercase tracking-wide">
            <a href="/membership"             className="hover:text-hp-accent transition-colors">Memberships</a>
            <a href="/partnership"            className="hover:text-hp-accent transition-colors">Partnerships</a>
            <a href="/membership/work-placement" className="hover:text-hp-accent transition-colors">Work Placement</a>
            <a href="/jobs"                   className="hover:text-hp-accent transition-colors">Job Board</a>
            <a href="/events"                 className="hover:text-hp-accent transition-colors">Events</a>
            <a href="/members"               className="hover:text-hp-accent transition-colors">Network</a>
            <a href="/reports/harnessing-talent-2026" className="hover:text-hp-accent transition-colors">Report</a>
            <a href="/safe-space"                    className="hover:text-hp-accent transition-colors">Safe Space</a>
            <
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
          <a href="/membership"                className="block py-4 border-b-2 border-hp-dark">Memberships</a>
          <a href="/partnership"             className="block py-4 border-b-2 border-hp-dark">Partnerships</a>
          <a href="/membership/work-placement" className="block py-4 border-b-2 border-hp-dark">Work Placement</a>
          <a href="/jobs"                    className="block py-4 border-b-2 border-hp-dark">Job Board</a>
          <a href="/events"                  className="block py-4 border-b-2 border-hp-dark">Events</a>
          <a href="/members"                 className="block py-4 border-b-2 border-hp-dark">Network</a>
          <a href="/reports/harnessing-talent-2026" className="block py-4 border-b-2 border-hp-dark">Report</a>
          <a href="/safe-space"                     className="block py-4 border-b-2 border-hp-dark">Safe Space</a>
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
  );
}
