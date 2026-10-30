'use client';

import { useState, useEffect, useRef, useCallback } from 'react';

interface PassportMember {
    id: number;
    username: string;
    display_name: string;
    photo_url?: string;
    job_title?: string;
    location_city?: string;
    where_in_the_uk?: string;
    industry?: string;
    skills_separate?: string;
    is_pro?: boolean;
    is_paired_mentor?: boolean;
    roles?: string[];
    member_since?: string;
    placement_scheme?: string;
    mentorship_status?: string;
}

const INDUSTRIES = [
    'Technology', 'Engineering', 'Finance & Banking', 'Healthcare',
    'Legal', 'Leadership', 'Marketing', 'Education', 'Early Career',
    'HR & People', 'Creative & Media', 'Science & Research',
    'Property & Construction', 'Retail & Consumer', 'Public Sector',
    'Charity & NGO', 'Consulting', 'Supply Chain & Logistics',
];

const UK_CITIES = [
    'London', 'Birmingham', 'Manchester', 'Leeds', 'Edinburgh',
    'Glasgow', 'Bristol', 'Cardiff', 'Liverpool', 'Nottingham',
    'Sheffield', 'Leicester', 'Newcastle', 'Brighton', 'Oxford',
    'Cambridge', 'Southampton', 'Reading', 'Aberdeen', 'Dundee',
    'Coventry', 'Hull', 'Derby', 'Stoke-on-Trent', 'Wolverhampton',
];

function passportNumber(member: PassportMember): string {
    const year = member.member_since ? member.member_since.slice(0, 4) : '2026';
    const seq = String(member.id).padStart(4, '0');
    return `${year}-${seq}`;
}

function passportBadge(member: PassportMember): string {
    if (member.is_paired_mentor) return 'ALUMNI MENTOR';
    if (member.placement_scheme === 'active') return 'ON PLACEMENT';
    if (member.mentorship_status === 'mentor') return 'MENTOR';
    if (member.is_pro) return 'PRO MEMBER';
    return 'VERIFIED MEMBER';
}

function memberLocation(member: PassportMember): string {
    const parts = [member.location_city, member.where_in_the_uk].filter(Boolean);
    return parts.join(', ') || 'United Kingdom';
}

function memberSkills(member: PassportMember): string[] {
    if (!member.skills_separate) return [];
    return member.skills_separate.split(',').map(s => s.trim()).filter(Boolean).slice(0, 4);
}

const SELECT_STYLE: React.CSSProperties = {
    appearance: 'none',
    WebkitAppearance: 'none',
    border: '1.5px solid #2e2e2e',
    borderRadius: 7,
    padding: '8px 34px 8px 36px',
    fontSize: 12,
    fontWeight: 700,
    color: '#e0e0e0',
    background: '#1a1a1a',
    cursor: 'pointer',
    outline: 'none',
    letterSpacing: '0.02em',
    minWidth: 170,
};

function FilterSelect({
    icon, value, onChange, placeholder, options,
}: {
    icon: React.ReactNode;
    value: string;
    onChange: (v: string) => void;
    placeholder: string;
    options: string[];
}) {
    return (
        <div style={{ position: 'relative', display: 'inline-flex', alignItems: 'center' }}>
            {/* left icon */}
            <span style={{ position: 'absolute', left: 10, pointerEvents: 'none', color: value ? '#d9381e' : '#888', display: 'flex' }}>
                {icon}
            </span>
            <select
                value={value}
                onChange={e => onChange(e.target.value)}
                style={{
                    ...SELECT_STYLE,
                    borderColor: value ? '#d9381e' : '#2e2e2e',
                }}
                onFocus={e => (e.target.style.borderColor = '#fff')}
                onBlur={e => (e.target.style.borderColor = value ? '#d9381e' : '#2e2e2e')}
            >
                <option value="">{placeholder}</option>
                {options.map(o => <option key={o} value={o}>{o}</option>)}
            </select>
            {/* chevron */}
            <span style={{ position: 'absolute', right: 10, pointerEvents: 'none', color: '#888', display: 'flex' }}>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5">
                    <polyline points="6 9 12 15 18 9"/>
                </svg>
            </span>
        </div>
    );
}

function PassportCard({ member }: { member: PassportMember }) {
    const skills = memberSkills(member);
    const badge = passportBadge(member);
    const pno = passportNumber(member);
    const loc = memberLocation(member);
    const photo = member.photo_url || `https://www.gravatar.com/avatar/${encodeURIComponent(member.username)}?s=128&d=mp`;

    return (
        <a
            href={`/members/${encodeURIComponent(member.username)}`}
            style={{
                display: 'block',
                border: '1.5px solid #1a1a1a',
                borderRadius: 10,
                padding: '18px 18px 14px',
                background: '#fff',
                textDecoration: 'none',
                color: 'inherit',
                transition: 'box-shadow 0.15s, transform 0.15s',
            }}
            onMouseEnter={e => {
                (e.currentTarget as HTMLElement).style.boxShadow = '4px 4px 0 #1a1a1a';
                (e.currentTarget as HTMLElement).style.transform = 'translate(-2px,-2px)';
            }}
            onMouseLeave={e => {
                (e.currentTarget as HTMLElement).style.boxShadow = 'none';
                (e.currentTarget as HTMLElement).style.transform = 'none';
            }}
        >
            {/* Header row */}
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginBottom: 14 }}>
                <span style={{ fontSize: 10, fontWeight: 800, letterSpacing: '0.08em', color: '#d9381e', fontFamily: 'monospace' }}>
                    PASSPORT NO. {pno}
                </span>
                <span style={{
                    fontSize: 9,
                    fontWeight: 800,
                    letterSpacing: '0.1em',
                    border: '1.5px solid #1a1a1a',
                    borderRadius: 4,
                    padding: '2px 6px',
                    color: '#1a1a1a',
                }}>
                    {badge}
                </span>
            </div>

            {/* Profile row */}
            <div style={{ display: 'flex', gap: 12, alignItems: 'flex-start', marginBottom: 14 }}>
                <div style={{
                    width: 52, height: 52, flexShrink: 0,
                    border: '2px solid #1a1a1a',
                    borderRadius: 6,
                    overflow: 'hidden',
                    background: '#1a1a1a',
                }}>
                    {/* eslint-disable-next-line @next/next/no-img-element */}
                    <img src={photo} alt={member.display_name} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
                </div>
                <div style={{ minWidth: 0 }}>
                    <div style={{ fontWeight: 800, fontSize: 15, lineHeight: 1.2, marginBottom: 2, color: '#1a1a1a' }}>
                        {member.display_name}
                    </div>
                    {member.job_title && (
                        <div style={{ fontSize: 12, color: '#555', fontWeight: 600, marginBottom: 4, lineHeight: 1.3 }}>
                            {member.job_title}
                        </div>
                    )}
                    <div style={{ fontSize: 11, color: '#777', display: 'flex', alignItems: 'center', gap: 3 }}>
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        {loc}
                    </div>
                </div>
            </div>

            {/* Skills */}
            {skills.length > 0 && (
                <div style={{ display: 'flex', flexWrap: 'wrap', gap: 5, marginBottom: 12 }}>
                    {skills.map(s => (
                        <span key={s} style={{
                            fontSize: 10,
                            fontWeight: 700,
                            border: '1px solid #ccc',
                            borderRadius: 4,
                            padding: '2px 7px',
                            color: '#333',
                            background: '#f7f7f7',
                            letterSpacing: '0.04em',
                        }}>
                            {s}
                        </span>
                    ))}
                </div>
            )}

            <div style={{ borderTop: '1px solid #e5e5e5' }} />
        </a>
    );
}

export default function PassportHubClient() {
    const [members, setMembers] = useState<PassportMember[]>([]);
    const [loading, setLoading] = useState(true);
    const [sector, setSector] = useState('');
    const [city, setCity] = useState('');
    const [search, setSearch] = useState('');
    const [page, setPage] = useState(1);
    const [hasMore, setHasMore] = useState(false);
    const [menuOpen, setMenuOpen] = useState(false);
    const searchTimer = useRef<ReturnType<typeof setTimeout>>(null);

    const fetchMembers = useCallback(async (sec: string, ct: string, q: string, pg: number, append = false) => {
        setLoading(true);
        try {
            const params = new URLSearchParams({ per_page: '12', page: String(pg) });
            if (sec) params.set('sector', sec);
            if (ct) params.set('city', ct);
            if (q) params.set('search', q);
            const res = await fetch(`/api/members?${params}`);
            if (!res.ok) return;
            const data: PassportMember[] = await res.json();
            setMembers(prev => append ? [...prev, ...data] : data);
            setHasMore(data.length === 12);
        } catch { /* silent */ } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => {
        fetchMembers(sector, city, search, 1);
        setPage(1);
    }, [sector, city, fetchMembers]); // eslint-disable-line react-hooks/exhaustive-deps

    function handleSearch(val: string) {
        setSearch(val);
        if (searchTimer.current) clearTimeout(searchTimer.current);
        searchTimer.current = setTimeout(() => { fetchMembers(sector, city, val, 1); setPage(1); }, 400);
    }

    function loadMore() {
        const next = page + 1;
        setPage(next);
        fetchMembers(sector, city, search, next, true);
    }

    const activeFilters = [sector, city].filter(Boolean).length;

    return (
        <div style={{ fontFamily: "'Inter', sans-serif", background: '#0d0d0d', minHeight: '100vh' }}>

            {/* ── NAV ── */}
            <nav style={{
                background: '#0d0d0d',
                borderBottom: '1.5px solid #2a2a2a',
                position: 'sticky', top: 0, zIndex: 50,
                padding: '0 24px',
                height: 64,
                display: 'flex', alignItems: 'center', justifyContent: 'space-between',
            }}>
                <div style={{ display: 'flex', alignItems: 'center', gap: 12 }}>
                    <div style={{
                        width: 38, height: 38, background: '#fff', borderRadius: 6,
                        display: 'flex', alignItems: 'center', justifyContent: 'center',
                        fontWeight: 900, fontSize: 18, color: '#0d0d0d',
                    }}>B</div>
                    <div style={{ lineHeight: 1.15 }}>
                        <div style={{ fontWeight: 800, fontSize: 12, color: '#fff', letterSpacing: '0.05em', textTransform: 'uppercase' }}>Black Professionals UK</div>
                        <div style={{ fontWeight: 700, fontSize: 9, color: '#d9381e', letterSpacing: '0.12em', textTransform: 'uppercase' }}>Passport Identity System 2026</div>
                    </div>
                </div>

                <div style={{ display: 'flex', alignItems: 'center', gap: 8 }} className="passport-nav-desktop">
                    {[
                        { label: 'Member Directory', href: '/members', icon: '⊞' },
                        { label: 'Create My Passport', href: '/profile', icon: '✦' },
                        { label: 'System Concept', href: '/passport#concept', icon: '◈' },
                    ].map(({ label, href, icon }) => (
                        <a key={label} href={href} style={{
                            display: 'flex', alignItems: 'center', gap: 6,
                            border: '1.5px solid #3a3a3a', borderRadius: 6,
                            padding: '6px 12px', fontSize: 12, fontWeight: 700, color: '#e0e0e0',
                            textDecoration: 'none', letterSpacing: '0.02em',
                            transition: 'border-color 0.15s, color 0.15s',
                        }}
                            onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = '#fff'; (e.currentTarget as HTMLElement).style.color = '#fff'; }}
                            onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = '#3a3a3a'; (e.currentTarget as HTMLElement).style.color = '#e0e0e0'; }}
                        >
                            <span style={{ fontSize: 13, opacity: 0.7 }}>{icon}</span>{label}
                        </a>
                    ))}
                </div>

                <div style={{ display: 'flex', alignItems: 'center', gap: 8 }}>
                    <a href="/members" style={{
                        background: '#d9381e', color: '#fff',
                        border: 'none', borderRadius: 6, padding: '8px 16px',
                        fontSize: 12, fontWeight: 800, letterSpacing: '0.08em',
                        textTransform: 'uppercase', textDecoration: 'none',
                        display: 'flex', alignItems: 'center', gap: 6,
                    }}>
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><polyline points="13 2 13 9 20 9"/></svg>
                        Sample Passport
                    </a>
                    <button onClick={() => setMenuOpen(o => !o)}
                        style={{ display: 'none', background: 'none', border: '1.5px solid #3a3a3a', borderRadius: 6, padding: '6px 8px', cursor: 'pointer', color: '#fff' }}
                        className="passport-nav-burger" aria-label="Toggle menu">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
                    </button>
                </div>
            </nav>

            {menuOpen && (
                <div style={{ background: '#1a1a1a', padding: '12px 24px', borderBottom: '1px solid #2a2a2a' }}>
                    {[
                        { label: 'Member Directory', href: '/members' },
                        { label: 'Create My Passport', href: '/profile' },
                        { label: 'My Dashboard', href: '/dashboard' },
                    ].map(({ label, href }) => (
                        <a key={label} href={href} style={{ display: 'block', padding: '10px 0', color: '#e0e0e0', textDecoration: 'none', fontWeight: 600, fontSize: 13, borderBottom: '1px solid #2a2a2a' }}>
                            {label}
                        </a>
                    ))}
                </div>
            )}

            {/* ── HERO ── */}
            <section style={{
                background: '#0d0d0d',
                padding: '64px 24px 56px',
                borderBottom: '1.5px solid #2a2a2a',
            }}>
                <div style={{ maxWidth: 1200, margin: '0 auto', display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: 48, flexWrap: 'wrap' }}>
                    <div style={{ maxWidth: 580, flex: '1 1 380px' }}>
                        <h1 style={{
                            fontSize: 'clamp(48px, 8vw, 84px)',
                            fontWeight: 900, color: '#ffffff',
                            lineHeight: 0.92, letterSpacing: '-0.03em',
                            margin: '0 0 28px', textTransform: 'uppercase',
                        }}>
                            Decade of<br />
                            <span style={{ color: '#d9381e' }}>Inclusion.</span><br />
                            Growth.
                        </h1>
                        <p style={{
                            fontSize: 15, color: '#b0b0b0', lineHeight: 1.7,
                            maxWidth: 460, fontWeight: 400, margin: '0 0 32px',
                            paddingLeft: 16, borderLeft: '3px solid #d9381e',
                        }}>
                            For 10 years, Black Professionals UK has empowered 7,000+ professionals across Scotland and the UK with skills, placements, and mentorship to thrive in their careers.
                        </p>
                        <div style={{ display: 'flex', alignItems: 'center', gap: 12, flexWrap: 'wrap' }}>
                            <a href="/profile" style={{
                                display: 'inline-flex', alignItems: 'center', gap: 8,
                                background: '#d9381e', color: '#fff',
                                border: '2px solid #d9381e', borderRadius: 8,
                                padding: '12px 22px', fontWeight: 800, fontSize: 13,
                                letterSpacing: '0.06em', textTransform: 'uppercase', textDecoration: 'none', whiteSpace: 'nowrap',
                            }}
                                onMouseEnter={e => { (e.currentTarget as HTMLElement).style.background = '#fff'; (e.currentTarget as HTMLElement).style.color = '#d9381e'; }}
                                onMouseLeave={e => { (e.currentTarget as HTMLElement).style.background = '#d9381e'; (e.currentTarget as HTMLElement).style.color = '#fff'; }}
                            >
                                Generate Yours Now
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg>
                            </a>
                            <a href="/members" style={{
                                display: 'inline-flex', alignItems: 'center', gap: 6,
                                border: '1.5px solid #3a3a3a', borderRadius: 8,
                                padding: '11px 18px', fontWeight: 700, fontSize: 13,
                                letterSpacing: '0.04em', textTransform: 'uppercase',
                                color: '#aaa', textDecoration: 'none', whiteSpace: 'nowrap',
                            }}
                                onMouseEnter={e => { (e.currentTarget as HTMLElement).style.borderColor = '#fff'; (e.currentTarget as HTMLElement).style.color = '#fff'; }}
                                onMouseLeave={e => { (e.currentTarget as HTMLElement).style.borderColor = '#3a3a3a'; (e.currentTarget as HTMLElement).style.color = '#aaa'; }}
                            >
                                Browse Directory
                            </a>
                        </div>
                    </div>

                    {/* Vertical scrolling card gallery */}
                    <div style={{ display: 'flex', gap: 10, flex: '0 0 auto', height: 420, overflow: 'hidden', position: 'relative' }}>
                        {/* top + bottom fade masks */}
                        <div style={{ position: 'absolute', top: 0, left: 0, right: 0, height: 60, background: 'linear-gradient(to bottom, #0d0d0d, transparent)', zIndex: 2, pointerEvents: 'none' }} />
                        <div style={{ position: 'absolute', bottom: 0, left: 0, right: 0, height: 60, background: 'linear-gradient(to top, #0d0d0d, transparent)', zIndex: 2, pointerEvents: 'none' }} />

                        {/* Column 1 — dark tall member cards, scroll up */}
                        <div className="hero-scroll-col1" style={{ display: 'flex', flexDirection: 'column', gap: 10, animation: 'heroScrollUp 22s linear infinite' }}>
                            {[
                                { name: 'Carol Anderson', role: '10-Yr Leader', anniversary: true },
                                { name: 'James Okafor', role: 'Alumni Mentor' },
                                { name: 'Priya Mensah', role: '10-Yr Member' },
                                { name: 'David Eze', role: 'Placement Lead' },
                                { name: 'Amara Diallo', role: 'Pro Member' },
                                /* duplicate for seamless loop */
                                { name: 'Carol Anderson', role: '10-Yr Leader', anniversary: true },
                                { name: 'James Okafor', role: 'Alumni Mentor' },
                                { name: 'Priya Mensah', role: '10-Yr Member' },
                                { name: 'David Eze', role: 'Placement Lead' },
                                { name: 'Amara Diallo', role: 'Pro Member' },
                            ].map((c, i) => (
                                <div key={i} style={{
                                    width: 160, flexShrink: 0,
                                    background: '#1a1a1a', borderRadius: 12,
                                    border: '1.5px solid #2a2a2a',
                                    padding: '14px 14px 18px',
                                    display: 'flex', flexDirection: 'column', justifyContent: 'space-between',
                                    height: 200,
                                    position: 'relative',
                                }}>
                                    {/* Anniversary badge overlay on first card */}
                                    {c.anniversary && (
                                        <div style={{
                                            position: 'absolute', top: 10, left: 10,
                                            background: '#1a1a1a', border: '1.5px solid #333',
                                            borderRadius: 8, padding: '8px 10px',
                                        }}>
                                            <div style={{ fontSize: 14, fontWeight: 900, color: '#d9381e', lineHeight: 1, letterSpacing: '-0.01em' }}>10th</div>
                                            <div style={{ fontSize: 7, fontWeight: 800, color: '#888', letterSpacing: '0.14em', textTransform: 'uppercase', marginTop: 1 }}>Anniversary</div>
                                            <div style={{ fontSize: 7, color: '#555', marginTop: 5, letterSpacing: '0.06em' }}>2016 — 2026</div>
                                        </div>
                                    )}
                                    <div />
                                    <div>
                                        <div style={{ fontSize: 13, fontWeight: 700, color: '#fff', lineHeight: 1.3 }}>{c.name}</div>
                                        <div style={{ fontSize: 10, color: '#888', marginTop: 3 }}>({c.role})</div>
                                    </div>
                                </div>
                            ))}
                        </div>

                        {/* Column 2 — mix of light label cards + dark member cards, scroll up slower */}
                        <div className="hero-scroll-col2" style={{ display: 'flex', flexDirection: 'column', gap: 10, marginTop: 40, animation: 'heroScrollUp 30s linear infinite' }}>
                            {[
                                { label: '10-Year Summit', light: true },
                                { name: 'Lawrence Dada', role: '10-Yr Alumni' },
                                { label: 'Alumni Network', light: true },
                                { name: 'Yemi Adeyemi', role: 'Mentor' },
                                { label: 'Scotland Hub', light: true },
                                { name: 'Tunde Williams', role: '10-Yr Leader' },
                                /* duplicate */
                                { label: '10-Year Summit', light: true },
                                { name: 'Lawrence Dada', role: '10-Yr Alumni' },
                                { label: 'Alumni Network', light: true },
                                { name: 'Yemi Adeyemi', role: 'Mentor' },
                                { label: 'Scotland Hub', light: true },
                                { name: 'Tunde Williams', role: '10-Yr Leader' },
                            ].map((c, i) => (
                                <div key={i} style={{
                                    width: 150, flexShrink: 0,
                                    background: (c as {light?: boolean}).light ? '#f0ece4' : '#1a1a1a',
                                    border: `1.5px solid ${ (c as {light?: boolean}).light ? '#ddd' : '#2a2a2a'}`,
                                    borderRadius: 12,
                                    padding: '16px 14px',
                                    display: 'flex', flexDirection: 'column', justifyContent: (c as {light?: boolean}).light ? 'center' : 'flex-end',
                                    height: (c as {light?: boolean}).light ? 120 : 160,
                                }}>
                                    {(c as {label?: string}).label ? (
                                        <div style={{ fontSize: 15, fontWeight: 800, color: '#1a1a1a', lineHeight: 1.2 }}>{(c as {label: string}).label}</div>
                                    ) : (
                                        <div>
                                            <div style={{ fontSize: 13, fontWeight: 700, color: '#fff', lineHeight: 1.3 }}>{(c as {name: string}).name}</div>
                                            <div style={{ fontSize: 10, color: '#888', marginTop: 3 }}>({(c as {role: string}).role})</div>
                                        </div>
                                    )}
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </section>

            {/* ── DIRECTORY ── */}
            <div style={{ background: '#ffffff', minHeight: '60vh' }}>
                <div style={{ maxWidth: 1200, margin: '0 auto', padding: '32px 24px' }}>

                    {/* Filter bar */}
                    <div style={{
                        background: '#fff',
                        border: '1.5px solid #1a1a1a',
                        borderRadius: 10,
                        padding: '14px 18px',
                        display: 'flex',
                        flexWrap: 'wrap',
                        alignItems: 'center',
                        gap: 10,
                        marginBottom: 28,
                    }}>
                        {/* Filter icon + label */}
                        <div style={{ display: 'flex', alignItems: 'center', gap: 6, marginRight: 4 }}>
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#1a1a1a" strokeWidth="2">
                                <polygon points="22 3 2 3 10 12.46 10 19 14 21 14 12.46 22 3"/>
                            </svg>
                            <span style={{ fontSize: 11, fontWeight: 800, letterSpacing: '0.08em', color: '#1a1a1a', textTransform: 'uppercase' }}>
                                Filter
                                {activeFilters > 0 && (
                                    <span style={{
                                        marginLeft: 5, background: '#d9381e', color: '#fff',
                                        borderRadius: '50%', width: 16, height: 16,
                                        display: 'inline-flex', alignItems: 'center', justifyContent: 'center',
                                        fontSize: 9, fontWeight: 900,
                                    }}>{activeFilters}</span>
                                )}
                            </span>
                        </div>

                        {/* Sector dropdown */}
                        <FilterSelect
                            icon={<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><rect x="2" y="3" width="6" height="6"/><rect x="9" y="3" width="6" height="6"/><rect x="16" y="3" width="6" height="6"/><rect x="2" y="10" width="6" height="6"/><rect x="9" y="10" width="6" height="6"/></svg>}
                            value={sector}
                            onChange={setSector}
                            placeholder="All Industries"
                            options={INDUSTRIES}
                        />

                        {/* City dropdown */}
                        <FilterSelect
                            icon={<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>}
                            value={city}
                            onChange={setCity}
                            placeholder="All Cities"
                            options={UK_CITIES}
                        />

                        {/* Clear filters */}
                        {activeFilters > 0 && (
                            <button
                                onClick={() => { setSector(''); setCity(''); }}
                                style={{
                                    border: '1px solid #ccc', borderRadius: 6, padding: '7px 12px',
                                    fontSize: 11, fontWeight: 700, cursor: 'pointer',
                                    background: 'transparent', color: '#666',
                                    display: 'flex', alignItems: 'center', gap: 4,
                                }}
                            >
                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                Clear
                            </button>
                        )}

                        {/* Search — pushed to right */}
                        <div style={{ marginLeft: 'auto', position: 'relative', display: 'flex', alignItems: 'center' }}>
                            <svg style={{ position: 'absolute', left: 10, color: '#aaa' }}
                                width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2">
                                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                            <input
                                type="text"
                                placeholder="Search name, role, or skill..."
                                value={search}
                                onChange={e => handleSearch(e.target.value)}
                                style={{
                                    paddingLeft: 32, paddingRight: 12, paddingTop: 8, paddingBottom: 8,
                                    border: '1.5px solid #ccc', borderRadius: 6, fontSize: 12,
                                    width: 230, outline: 'none', fontFamily: 'inherit', color: '#1a1a1a',
                                }}
                                onFocus={e => (e.target.style.borderColor = '#1a1a1a')}
                                onBlur={e => (e.target.style.borderColor = '#ccc')}
                            />
                        </div>
                    </div>

                    {/* Grid */}
                    {loading && members.length === 0 ? (
                        <div style={{ textAlign: 'center', padding: '60px 0', color: '#999', fontWeight: 600 }}>Loading passports…</div>
                    ) : members.length === 0 ? (
                        <div style={{ textAlign: 'center', padding: '60px 0', color: '#999', fontWeight: 600 }}>No members found.</div>
                    ) : (
                        <>
                            <div style={{
                                display: 'grid',
                                gridTemplateColumns: 'repeat(auto-fill, minmax(280px, 1fr))',
                                gap: 16,
                                marginBottom: 32,
                            }}>
                                {members.map(m => <PassportCard key={m.id} member={m} />)}
                            </div>
                            {hasMore && (
                                <div style={{ textAlign: 'center' }}>
                                    <button onClick={loadMore} disabled={loading} style={{
                                        border: '1.5px solid #1a1a1a', borderRadius: 6,
                                        padding: '10px 28px', fontWeight: 700, fontSize: 12,
                                        letterSpacing: '0.06em', cursor: 'pointer',
                                        background: loading ? '#f0f0f0' : '#1a1a1a',
                                        color: loading ? '#999' : '#fff',
                                        transition: 'all 0.12s',
                                    }}>
                                        {loading ? 'Loading…' : 'Load More Passports'}
                                    </button>
                                </div>
                            )}
                        </>
                    )}
                </div>
            </div>

            <style>{`
                @keyframes heroScrollUp {
                    from { transform: translateY(0); }
                    to { transform: translateY(-50%); }
                }
                .hero-scroll-col1:hover, .hero-scroll-col2:hover { animation-play-state: paused; }
                @media (max-width: 768px) {
                    .passport-nav-desktop { display: none !important; }
                    .passport-nav-burger { display: flex !important; }
                }
                select option { background: #1a1a1a; color: #e0e0e0; }
            `}</style>
        </div>
    );
}
