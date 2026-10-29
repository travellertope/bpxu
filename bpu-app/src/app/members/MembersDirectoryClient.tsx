'use client';

import { useEffect, useRef, useState, useCallback } from 'react';
import { useSearchParams } from 'next/navigation';
import { PublicMemberProfile } from './[username]/types';
import { BPUUser } from '@/lib/auth';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';
const PER_PAGE = 12;

const SECTORS = [
  'All Sectors',
  'Technology',
  'Engineering',
  'Finance & Banking',
  'Healthcare',
  'Legal',
  'Leadership',
  'Marketing',
  'Education',
  'Early Career',
];

function gravatarUrl(username: string) {
  return `https://www.gravatar.com/avatar/${encodeURIComponent(username)}?s=128&d=mp`;
}

function passportNo(id: number) {
  return `BPU-${String(id).padStart(5, '0')}`;
}

function deriveStatus(m: PublicMemberProfile) {
  if (m.roles?.includes('bpu_mentor')) return 'ALUMNI MENTOR';
  if (m.is_pro) return 'PRO MEMBER';
  return 'VERIFIED MEMBER';
}

function topSkills(m: PublicMemberProfile, max = 3): string[] {
  if (!m.skills_separate) return [];
  return m.skills_separate
    .split(',')
    .map(s => s.trim())
    .filter(Boolean)
    .slice(0, max);
}

interface Props {
  viewer: BPUUser | null;
  isAuthenticated: boolean;
}

export default function MembersDirectoryClient({ viewer, isAuthenticated }: Props) {
  const searchParams = useSearchParams();
  const [members, setMembers] = useState<PublicMemberProfile[]>([]);
  const [loading, setLoading] = useState(true);
  const [loadingMore, setLoadingMore] = useState(false);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(true);
  const [search, setSearch] = useState(searchParams?.get('search') ?? '');
  const [sector, setSector] = useState('All Sectors');
  const [totalCount, setTotalCount] = useState<number | null>(null);
  const searchTimeout = useRef<ReturnType<typeof setTimeout> | null>(null);

  const fetchMembers = useCallback(async (
    pageNum: number,
    searchVal: string,
    sectorVal: string,
    replace: boolean,
  ) => {
    const params = new URLSearchParams({
      page: String(pageNum),
      per_page: String(PER_PAGE),
    });
    if (searchVal) params.set('search', searchVal);
    if (sectorVal && sectorVal !== 'All Sectors') params.set('sector', sectorVal);

    try {
      const res = await fetch(
        `${WP_URL}/wp-json/bpu/v1/members?${params.toString()}`,
        { cache: 'no-store' },
      );
      if (!res.ok) return;
      const data: PublicMemberProfile[] = await res.json();
      const total = parseInt(res.headers.get('X-WP-Total') ?? '0', 10);
      if (total) setTotalCount(total);
      setHasMore(data.length === PER_PAGE);
      setMembers(prev => replace ? data : [...prev, ...data]);
    } catch {
      // silently ignore
    }
  }, []);

  // Initial load + search/sector changes
  useEffect(() => {
    setLoading(true);
    setPage(1);
    fetchMembers(1, search, sector, true).finally(() => setLoading(false));
  // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [search, sector]);

  const handleSearch = (val: string) => {
    if (searchTimeout.current) clearTimeout(searchTimeout.current);
    searchTimeout.current = setTimeout(() => setSearch(val), 300);
  };

  const loadMore = async () => {
    const next = page + 1;
    setLoadingMore(true);
    await fetchMembers(next, search, sector, false);
    setPage(next);
    setLoadingMore(false);
  };

  return (
    <div style={{ minHeight: '100vh', background: '#F3ECE0', fontFamily: "'Plus Jakarta Sans', sans-serif" }}>
      {/* Page header */}
      <div style={{
        background: '#111317',
        color: '#F3ECE0',
        borderBottom: '4px solid #1E2229',
        padding: '60px 24px 48px',
      }}>
        <div style={{ maxWidth: 1200, margin: '0 auto' }}>
          <p style={{ fontFamily: 'JetBrains Mono, monospace', fontSize: 11, letterSpacing: '0.2em', color: '#C5491F', fontWeight: 700, marginBottom: 12, textTransform: 'uppercase' }}>
            BPU · VERIFIED NETWORK
          </p>
          <h1 style={{ fontSize: 'clamp(2rem, 5vw, 3.5rem)', fontWeight: 900, textTransform: 'uppercase', letterSpacing: '-0.02em', lineHeight: 1, margin: 0 }}>
            The Network
          </h1>
          <p style={{ marginTop: 12, fontSize: 16, fontWeight: 600, color: '#9CA3AF' }}>
            Highlighting the talent driving the UK forward
            {totalCount ? ` · ${totalCount.toLocaleString()} members` : ''}
          </p>
        </div>
      </div>

      {/* Controls bar */}
      <div style={{
        background: '#fff',
        borderBottom: '3px solid #1E2229',
        padding: '16px 24px',
        position: 'sticky',
        top: 0,
        zIndex: 40,
        boxShadow: '0 4px 0 #1E2229',
      }}>
        <div style={{ maxWidth: 1200, margin: '0 auto', display: 'flex', flexWrap: 'wrap', gap: 12, alignItems: 'center', justifyContent: 'space-between' }}>
          {/* Sector pills */}
          <div style={{ display: 'flex', flexWrap: 'wrap', gap: 8 }}>
            {SECTORS.map(s => (
              <button
                key={s}
                onClick={() => setSector(s)}
                style={{
                  padding: '6px 14px',
                  border: '2px solid #1E2229',
                  borderRadius: 6,
                  fontWeight: 700,
                  fontSize: 11,
                  textTransform: 'uppercase',
                  letterSpacing: '0.05em',
                  cursor: 'pointer',
                  background: sector === s ? '#111317' : '#fff',
                  color: sector === s ? '#F3ECE0' : '#111317',
                  boxShadow: sector === s ? '3px 3px 0 #1E2229' : 'none',
                  transition: 'all 0.15s',
                  fontFamily: 'JetBrains Mono, monospace',
                }}
              >
                {s}
              </button>
            ))}
          </div>

          {/* Search input */}
          <div style={{ position: 'relative', width: 260 }}>
            <svg style={{ position: 'absolute', left: 10, top: '50%', transform: 'translateY(-50%)', color: '#6B7280' }} width={14} height={14} fill="none" stroke="currentColor" strokeWidth={2} viewBox="0 0 24 24">
              <circle cx={11} cy={11} r={8} /><path d="m21 21-4.35-4.35" />
            </svg>
            <input
              type="text"
              defaultValue={search}
              placeholder="Search name, role, or skill..."
              onChange={e => handleSearch(e.target.value)}
              style={{
                width: '100%',
                paddingLeft: 32,
                paddingRight: 12,
                paddingTop: 8,
                paddingBottom: 8,
                border: '2px solid #1E2229',
                borderRadius: 6,
                fontSize: 12,
                fontWeight: 600,
                fontFamily: 'inherit',
                background: '#F9F6F0',
                outline: 'none',
                boxSizing: 'border-box',
              }}
            />
          </div>
        </div>
      </div>

      {/* Grid */}
      <div style={{ maxWidth: 1200, margin: '0 auto', padding: '40px 24px' }}>
        {loading ? (
          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(320px, 1fr))', gap: 24 }}>
            {Array.from({ length: 6 }).map((_, i) => (
              <div key={i} style={{
                background: '#fff',
                border: '3px solid #1E2229',
                borderRadius: 12,
                padding: 24,
                boxShadow: '5px 5px 0 #1E2229',
                height: 220,
                opacity: 0.5,
                animation: 'pulse 1.5s ease-in-out infinite',
              }} />
            ))}
          </div>
        ) : members.length === 0 ? (
          <div style={{
            textAlign: 'center',
            padding: '80px 24px',
            background: '#fff',
            border: '3px solid #1E2229',
            borderRadius: 12,
            boxShadow: '5px 5px 0 #1E2229',
          }}>
            <p style={{ fontSize: 40, marginBottom: 12 }}>🔍</p>
            <h3 style={{ fontWeight: 900, textTransform: 'uppercase', fontSize: 20, margin: 0 }}>No Members Found</h3>
            <p style={{ color: '#6B7280', marginTop: 8, fontSize: 14 }}>Try resetting your search or sector filter.</p>
          </div>
        ) : (
          <>
            <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fill, minmax(320px, 1fr))', gap: 24 }}>
              {members.map(m => (
                <MemberCard key={m.id} member={m} />
              ))}
            </div>

            {hasMore && (
              <div style={{ textAlign: 'center', marginTop: 48 }}>
                <button
                  onClick={loadMore}
                  disabled={loadingMore}
                  style={{
                    padding: '14px 40px',
                    background: loadingMore ? '#E5E7EB' : '#111317',
                    color: loadingMore ? '#9CA3AF' : '#F3ECE0',
                    border: '3px solid #1E2229',
                    borderRadius: 8,
                    fontWeight: 900,
                    fontSize: 13,
                    textTransform: 'uppercase',
                    letterSpacing: '0.08em',
                    cursor: loadingMore ? 'not-allowed' : 'pointer',
                    boxShadow: loadingMore ? 'none' : '4px 4px 0 #1E2229',
                    fontFamily: 'inherit',
                  }}
                >
                  {loadingMore ? 'Loading...' : 'Load More Members'}
                </button>
              </div>
            )}
          </>
        )}

        {/* CTA for non-authenticated */}
        {!isAuthenticated && (
          <div style={{
            marginTop: 64,
            background: '#001478',
            color: '#fff',
            border: '3px solid #1E2229',
            borderRadius: 12,
            padding: '40px 32px',
            boxShadow: '6px 6px 0 #1E2229',
            display: 'flex',
            flexWrap: 'wrap',
            gap: 24,
            alignItems: 'center',
            justifyContent: 'space-between',
          }}>
            <div>
              <h3 style={{ fontWeight: 900, fontSize: 24, textTransform: 'uppercase', margin: 0 }}>Join The Network</h3>
              <p style={{ marginTop: 8, color: '#93C5FD', fontSize: 14 }}>Create your BPU Digital Passport and get discovered by top employers.</p>
            </div>
            <a
              href="/register"
              style={{
                padding: '14px 32px',
                background: '#C8102E',
                color: '#fff',
                border: '3px solid #fff',
                borderRadius: 8,
                fontWeight: 900,
                fontSize: 13,
                textTransform: 'uppercase',
                textDecoration: 'none',
                whiteSpace: 'nowrap',
                boxShadow: '4px 4px 0 rgba(0,0,0,0.3)',
              }}
            >
              Get Your Passport →
            </a>
          </div>
        )}
      </div>

      <style>{`
        @keyframes pulse { 0%,100%{opacity:.5} 50%{opacity:.25} }
      `}</style>
    </div>
  );
}

function MemberCard({ member: m }: { member: PublicMemberProfile }) {
  const photo = m.photo_url || gravatarUrl(m.username);
  const skills = topSkills(m, 3);
  const status = deriveStatus(m);
  const location = m.location_city || m.where_in_the_uk || m.country_location || '';
  const title = m.job_title
    ? m.current_employer ? `${m.job_title} @ ${m.current_employer}` : m.job_title
    : m.industry || '';

  return (
    <a
      href={`/members/${encodeURIComponent(m.username)}`}
      style={{ textDecoration: 'none', color: 'inherit', display: 'block' }}
    >
      <div style={{
        background: '#fff',
        border: '3px solid #1E2229',
        borderRadius: 12,
        padding: 24,
        boxShadow: '5px 5px 0 #1E2229',
        display: 'flex',
        flexDirection: 'column',
        height: '100%',
        transition: 'transform 0.15s, box-shadow 0.15s',
        cursor: 'pointer',
      }}
        onMouseEnter={e => {
          (e.currentTarget as HTMLElement).style.transform = 'translate(-3px,-3px)';
          (e.currentTarget as HTMLElement).style.boxShadow = '8px 8px 0 #1E2229';
        }}
        onMouseLeave={e => {
          (e.currentTarget as HTMLElement).style.transform = 'none';
          (e.currentTarget as HTMLElement).style.boxShadow = '5px 5px 0 #1E2229';
        }}
      >
        {/* Card header: passport no + status */}
        <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center', borderBottom: '2px solid #E5E7EB', paddingBottom: 10, marginBottom: 16 }}>
          <span style={{ fontFamily: 'JetBrains Mono, monospace', fontSize: 10, fontWeight: 700, color: '#C5491F', textTransform: 'uppercase', letterSpacing: '0.1em' }}>
            {passportNo(m.id)}
          </span>
          <span style={{
            fontFamily: 'JetBrains Mono, monospace',
            fontSize: 9,
            fontWeight: 700,
            padding: '3px 8px',
            border: '1px solid #1E2229',
            borderRadius: 4,
            background: status === 'PRO MEMBER' ? '#001478' : status === 'ALUMNI MENTOR' ? '#C5491F' : '#F3ECE0',
            color: status === 'VERIFIED MEMBER' ? '#111317' : '#fff',
            textTransform: 'uppercase',
            letterSpacing: '0.08em',
          }}>
            {status}
          </span>
        </div>

        {/* Profile row */}
        <div style={{ display: 'flex', gap: 14, marginBottom: 16, alignItems: 'flex-start' }}>
          <div style={{ width: 60, height: 60, borderRadius: 8, border: '2px solid #1E2229', overflow: 'hidden', flexShrink: 0, background: '#111317' }}>
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img src={photo} alt={m.display_name} style={{ width: '100%', height: '100%', objectFit: 'cover' }} />
          </div>
          <div style={{ minWidth: 0 }}>
            <h3 style={{ fontWeight: 900, fontSize: 17, lineHeight: 1.2, margin: 0, color: '#111317' }}>
              {m.display_name}
            </h3>
            {title && (
              <p style={{ fontSize: 11, fontWeight: 700, color: '#6B7280', margin: '4px 0 0', lineHeight: 1.4 }}>
                {title}
              </p>
            )}
            {location && (
              <p style={{ fontFamily: 'JetBrains Mono, monospace', fontSize: 10, color: '#9CA3AF', margin: '3px 0 0' }}>
                📍 {location}
              </p>
            )}
          </div>
        </div>

        {/* Skill badges */}
        {skills.length > 0 && (
          <div style={{ display: 'flex', flexWrap: 'wrap', gap: 6, marginBottom: 16, flexGrow: 1 }}>
            {skills.map(sk => (
              <span key={sk} style={{
                fontFamily: 'JetBrains Mono, monospace',
                fontSize: 10,
                fontWeight: 700,
                padding: '3px 8px',
                border: '1px solid #D1D5DB',
                borderRadius: 4,
                background: '#F9F6F0',
                color: '#374151',
                textTransform: 'uppercase',
                letterSpacing: '0.05em',
              }}>
                {sk}
              </span>
            ))}
          </div>
        )}

        {/* Footer: sector + CTA */}
        <div style={{ borderTop: '2px solid #E5E7EB', paddingTop: 14, display: 'flex', justifyContent: 'space-between', alignItems: 'center', marginTop: 'auto' }}>
          <span style={{ fontFamily: 'JetBrains Mono, monospace', fontSize: 9, fontWeight: 700, color: '#9CA3AF', textTransform: 'uppercase', letterSpacing: '0.08em' }}>
            {m.industry || 'Professional'}
          </span>
          <span style={{
            padding: '8px 16px',
            background: '#111317',
            color: '#F3ECE0',
            border: '2px solid #1E2229',
            borderRadius: 6,
            fontWeight: 900,
            fontSize: 11,
            textTransform: 'uppercase',
            letterSpacing: '0.06em',
            boxShadow: '3px 3px 0 #1E2229',
          }}>
            View Profile ↗
          </span>
        </div>
      </div>
    </a>
  );
}
