'use client';

import { useState, useEffect, useRef } from 'react';
import { List, CaretDown, User } from '@phosphor-icons/react';

interface AuthUser {
  id: number;
  display_name: string;
  photo_url?: string;
  is_pro?: boolean;
}

export default function SiteHeader() {
  const [menuOpen, setMenuOpen]       = useState(false);
  const [navUp, setNavUp]             = useState(false);
  const [authUser, setAuthUser]       = useState<AuthUser | null | undefined>(undefined); // undefined = loading
  const [dropOpen, setDropOpen]       = useState(false);
  const dropRef                       = useRef<HTMLDivElement>(null);
  const lastY                         = useRef(0);

  useEffect(() => {
    function onScroll() {
      const y = window.scrollY;
      setNavUp(y > lastY.current && y > 100);
      lastY.current = y;
    }
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  useEffect(() => {
    fetch('/api/auth/me', { credentials: 'same-origin' })
      .then(r => r.ok ? r.json() : null)
      .then(data => setAuthUser(data?.authenticated ? data.user : null))
      .catch(() => setAuthUser(null));
  }, []);

  // Close dropdown on outside click
  useEffect(() => {
    function onClickOutside(e: MouseEvent) {
      if (dropRef.current && !dropRef.current.contains(e.target as Node)) {
        setDropOpen(false);
      }
    }
    document.addEventListener('mousedown', onClickOutside);
    return () => document.removeEventListener('mousedown', onClickOutside);
  }, []);

  const initials = authUser
    ? authUser.display_name.split(' ').slice(0, 2).map(w => w[0]).join('').toUpperCase()
    : '';

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
          <div className="hidden lg:flex items-center gap-5 font-bold text-sm uppercase tracking-wide">
            <a href="/membership"                className="hover:text-hp-accent transition-colors">Membership</a>
            <a href="/partnership"               className="hover:text-hp-accent transition-colors">Partnership</a>
            <a href="/membership/work-placement" className="hover:text-hp-accent transition-colors">Work Placement</a>
            <a href="/jobs"                      className="hover:text-hp-accent transition-colors">Jobs</a>
            <a href="/events"                    className="hover:text-hp-accent transition-colors">Events</a>

            {/* Divider */}
            <span className="w-px h-5 bg-hp-dark/30 flex-shrink-0" aria-hidden="true" />

            {/* Featured projects */}
            <a
              href="/reports/harnessing-talent-2026"
              className="px-3 py-1.5 rounded-lg border-2 border-hp-dark text-hp-dark hover:bg-hp-dark hover:text-white transition-colors text-xs font-black tracking-wider"
            >
              Report
            </a>
            <a
              href="/safe-space"
              className="px-3 py-1.5 rounded-lg border-2 border-hp-accent text-hp-accent hover:bg-hp-accent hover:text-white transition-colors text-xs font-black tracking-wider"
            >
              Safe Space
            </a>

            {/* Auth CTA */}
            {authUser === undefined ? (
              /* loading — invisible placeholder keeps layout stable */
              <span className="w-36 h-10" aria-hidden="true" />
            ) : authUser ? (
              /* Logged-in: avatar + dropdown */
              <div ref={dropRef} className="relative">
                <button
                  onClick={() => setDropOpen(o => !o)}
                  className="flex items-center gap-2 border-brutal rounded-xl px-3 py-2 shadow-brutal-sm bg-white hover:shadow-brutal transition-shadow"
                  aria-label="Account menu"
                >
                  {authUser.photo_url ? (
                    /* eslint-disable-next-line @next/next/no-img-element */
                    <img src={authUser.photo_url} alt={authUser.display_name} className="w-7 h-7 rounded-full object-cover border border-hp-dark/20" />
                  ) : (
                    <span className="w-7 h-7 rounded-full bg-hp-dark text-white text-xs font-black flex items-center justify-center">
                      {initials || <User size={14} weight="bold" />}
                    </span>
                  )}
                  <span className="font-bold text-sm max-w-[110px] truncate">{authUser.display_name.split(' ')[0]}</span>
                  <CaretDown size={12} weight="bold" className={`transition-transform ${dropOpen ? 'rotate-180' : ''}`} />
                </button>

                {dropOpen && (
                  <div className="absolute right-0 top-full mt-2 w-48 bg-hp-bg border-brutal rounded-xl shadow-brutal overflow-hidden z-50">
                    <a href="/paired/dashboard" className="block px-4 py-3 font-bold text-sm hover:bg-hp-dark hover:text-white transition-colors">
                      Dashboard
                    </a>
                    <a href="/paired/profile" className="block px-4 py-3 font-bold text-sm hover:bg-hp-dark hover:text-white transition-colors border-t-2 border-hp-dark/10">
                      My Profile
                    </a>
                    <a href="/api/auth/logout" className="block px-4 py-3 font-bold text-sm text-red-600 hover:bg-red-600 hover:text-white transition-colors border-t-2 border-hp-dark/10">
                      Log Out
                    </a>
                  </div>
                )}
              </div>
            ) : (
              /* Logged-out */
              <a
                href="/register"
                className="bg-hp-dark text-white px-6 py-3 rounded-xl border-brutal shadow-brutal interactive-card uppercase font-black text-sm"
              >
                Become a Member
              </a>
            )}
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
          <a href="/membership"                className="block py-4 border-b-2 border-hp-dark">Membership</a>
          <a href="/partnership"               className="block py-4 border-b-2 border-hp-dark">Partnership</a>
          <a href="/membership/work-placement" className="block py-4 border-b-2 border-hp-dark">Work Placement</a>
          <a href="/jobs"                      className="block py-4 border-b-2 border-hp-dark">Jobs</a>
          <a href="/events"                    className="block py-4 border-b-2 border-hp-dark">Events</a>
          <div className="flex gap-3 justify-center px-4 py-4 border-b-2 border-hp-dark bg-hp-bg">
            <a
              href="/reports/harnessing-talent-2026"
              className="flex-1 py-3 rounded-lg border-2 border-hp-dark text-hp-dark text-xs font-black tracking-wider text-center"
            >
              Report
            </a>
            <a
              href="/safe-space"
              className="flex-1 py-3 rounded-lg border-2 border-hp-accent text-hp-accent text-xs font-black tracking-wider text-center"
            >
              Safe Space
            </a>
          </div>

          {/* Mobile auth CTA */}
          {authUser ? (
            <div className="flex flex-col">
              <a href="/paired/dashboard" className="block py-4 border-b-2 border-hp-dark">Dashboard</a>
              <a href="/paired/profile"   className="block py-4 border-b-2 border-hp-dark">My Profile</a>
              <div className="p-4">
                <a
                  href="/api/auth/logout"
                  className="block w-full bg-red-600 text-white px-6 py-4 rounded-xl border-brutal shadow-brutal uppercase font-black text-sm text-center"
                >
                  Log Out
                </a>
              </div>
            </div>
          ) : (
            <div className="p-4">
              <a
                href="/register"
                className="block w-full bg-hp-dark text-white px-6 py-4 rounded-xl border-brutal shadow-brutal uppercase font-black text-sm text-center"
              >
                Become a Member
              </a>
            </div>
          )}
        </div>
      )}
    </nav>
  );
}
