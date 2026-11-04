'use client';

import { useState, useEffect } from 'react';

const NAV_ITEMS = [
    { id: 'overview',    label: 'Overview' },
    { id: 'context',     label: 'Context' },
    { id: 'findings',    label: 'Findings' },
    { id: 'insights',    label: 'Insights' },
    { id: 'action-plan', label: 'Action Plan' },
    { id: 'download',    label: 'Download' },
];

export default function ReportNav() {
    const [active, setActive] = useState('overview');
    const [visible, setVisible] = useState(false);

    useEffect(() => {
        const sectionEls = NAV_ITEMS.map(item => document.getElementById(item.id)).filter(Boolean) as HTMLElement[];

        const observer = new IntersectionObserver(
            entries => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        setActive(entry.target.id);
                    }
                });
            },
            { rootMargin: '-20% 0px -70% 0px', threshold: 0 }
        );

        sectionEls.forEach(el => observer.observe(el));

        // Show nav once user scrolls past the hero
        const heroEl = document.getElementById('overview');
        const scrollObserver = new IntersectionObserver(
            ([entry]) => setVisible(!entry.isIntersecting),
            { threshold: 0.1 }
        );
        if (heroEl) scrollObserver.observe(heroEl);

        return () => {
            observer.disconnect();
            scrollObserver.disconnect();
        };
    }, []);

    const scrollTo = (id: string) => {
        const el = document.getElementById(id);
        if (el) {
            const offset = 80; // below sticky header + this nav
            const top = el.getBoundingClientRect().top + window.scrollY - offset;
            window.scrollTo({ top, behavior: 'smooth' });
        }
    };

    return (
        <nav
            className="sticky top-16 z-30 border-b border-border transition-all duration-300"
            style={{
                background: 'rgba(255,255,255,0.95)',
                backdropFilter: 'blur(12px)',
                WebkitBackdropFilter: 'blur(12px)',
                transform: visible ? 'translateY(0)' : 'translateY(-100%)',
                opacity: visible ? 1 : 0,
                pointerEvents: visible ? 'auto' : 'none',
                position: 'sticky',
            }}
            aria-label="Report sections"
        >
            <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex items-center gap-1 overflow-x-auto scrollbar-hide py-0.5">
                    {NAV_ITEMS.map(item => (
                        <button
                            key={item.id}
                            onClick={() => scrollTo(item.id)}
                            className="shrink-0 px-4 py-3 text-xs font-black uppercase tracking-wider transition-colors whitespace-nowrap border-b-2"
                            style={{
                                color: active === item.id ? 'var(--hp-accent, #d9381e)' : 'var(--text-2)',
                                borderBottomColor: active === item.id ? 'var(--hp-accent, #d9381e)' : 'transparent',
                            }}
                        >
                            {item.label}
                        </button>
                    ))}
                </div>
            </div>
        </nav>
    );
}
