'use client';

import React, { useState } from 'react';
import Link from 'next/link';
import { PublicMemberProfile, PublicTestimonial } from './types';
import { BPUUser } from '@/lib/auth';

/* ── colour constants (match PassportCard) ── */
const OCHRE  = '#C5491F';
const SLATE  = '#111317';
const PAPER  = '#F3ECE0';
const BORDER = '#1E2229';
const BPU_RED = '#C8102E';

interface Props {
  member: PublicMemberProfile | null;
  username: string;
  isOwner: boolean;
  viewer: BPUUser | null;
}

function deriveStatus(m: PublicMemberProfile): string {
  if (m.roles?.includes('bpu_mentor') || m.roles?.includes('mentor')) return 'ALUMNI MENTOR';
  if (m.is_pro) return 'PRO MEMBER';
  return 'VERIFIED MEMBER';
}

function passportNo(id: number) {
  return `PASSPORT NO. 2026-${String(id).padStart(4, '0')}`;
}

function avatarUrl(member: PublicMemberProfile | null, username: string) {
  return member?.photo_url || `https://www.gravatar.com/avatar/${username}?s=400&d=mp`;
}

/* ── Modal wrapper ── */
function Modal({ id, open, onClose, children }: {
  id: string; open: boolean; onClose: () => void; children: React.ReactNode;
}) {
  if (!open) return null;
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div
        className="absolute inset-0"
        style={{ background: 'rgba(17,19,23,0.85)', backdropFilter: 'blur(4px)' }}
        onClick={onClose}
      />
      <div
        id={id}
        className="relative z-10 bg-white rounded-2xl max-w-lg w-full overflow-hidden"
        style={{ border: `4px solid ${BORDER}`, boxShadow: `12px 12px 0 ${BORDER}` }}
      >
        {children}
      </div>
    </div>
  );
}

function ModalHeader({ title, onClose }: { title: string; onClose: () => void }) {
  return (
    <div className="flex justify-between items-center p-5 border-b-2" style={{ background: SLATE, borderColor: BORDER }}>
      <span className="font-black text-xs uppercase tracking-wider" style={{ color: OCHRE, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
        {title}
      </span>
      <button
        onClick={onClose}
        className="w-8 h-8 flex items-center justify-center rounded text-white border"
        style={{ background: OCHRE, borderColor: BORDER }}
      >
        ✕
      </button>
    </div>
  );
}

/* ── Toast ── */
function Toast({ msg, onClose }: { msg: string; onClose: () => void }) {
  React.useEffect(() => {
    const t = setTimeout(onClose, 3500);
    return () => clearTimeout(t);
  }, [onClose]);

  return (
    <div
      className="fixed bottom-6 right-6 z-50 flex items-center gap-3 p-4 rounded-lg max-w-sm"
      style={{ background: SLATE, border: `3px solid ${BORDER}`, boxShadow: `8px 8px 0 ${BORDER}`, color: PAPER }}
    >
      <span style={{ color: OCHRE, fontSize: '1.5rem' }}>✓</span>
      <div>
        <span className="font-black text-xs uppercase block" style={{ color: OCHRE, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Notification</span>
        <p className="text-xs font-bold" style={{ color: '#d1d5db' }}>{msg}</p>
      </div>
    </div>
  );
}

/* ── Skill bar ── */
function SkillBar({ name, votes, maxVotes, endorsed, onEndorse }: {
  name: string; votes: number; maxVotes: number; endorsed?: boolean; onEndorse?: () => void;
}) {
  const pct = maxVotes > 0 ? Math.round((votes / maxVotes) * 100) : 0;
  return (
    <div className="space-y-1">
      <div className="flex justify-between items-center gap-2">
        <span className="font-black text-xs uppercase flex-1" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>{name}</span>
        <span className="font-mono font-bold text-xs shrink-0" style={{ color: OCHRE }}>{votes} vote{votes !== 1 ? 's' : ''}</span>
        {onEndorse && (
          <button
            onClick={onEndorse}
            title={endorsed ? 'Remove endorsement' : 'Endorse this skill'}
            className="shrink-0 font-mono font-bold text-[10px] uppercase px-2 py-0.5 rounded transition-colors"
            style={{
              border: `2px solid ${endorsed ? OCHRE : BORDER}`,
              color: endorsed ? '#fff' : BORDER,
              background: endorsed ? OCHRE : 'transparent',
            }}
          >
            {endorsed ? '✓ Endorsed' : '+ Endorse'}
          </button>
        )}
      </div>
      <div className="w-full h-3 rounded-full overflow-hidden" style={{ background: PAPER, border: `2px solid ${BORDER}` }}>
        <div className="h-full rounded-full transition-all duration-500" style={{ width: `${pct}%`, background: SLATE }} />
      </div>
    </div>
  );
}

/* ── Download passport as PNG via Canvas ── */
function downloadPassport(m: PublicMemberProfile) {
  const canvas = document.createElement('canvas');
  canvas.width = 1200;
  canvas.height = 630;
  const ctx = canvas.getContext('2d')!;

  /* bg */
  ctx.fillStyle = SLATE;
  ctx.fillRect(0, 0, 1200, 630);

  /* card */
  ctx.fillStyle = PAPER;
  ctx.strokeStyle = BORDER;
  ctx.lineWidth = 8;
  ctx.fillRect(40, 40, 1120, 550);
  ctx.strokeRect(40, 40, 1120, 550);

  /* header */
  ctx.fillStyle = SLATE;
  ctx.font = 'bold 30px "Plus Jakarta Sans", sans-serif';
  ctx.fillText('BLACK PROFESSIONALS UNITED KINGDOM', 80, 105);

  ctx.fillStyle = BPU_RED;
  ctx.font = 'bold 18px monospace';
  ctx.fillText('VERIFIED DIGITAL PASSPORT • ' + passportNo(m.id), 80, 138);

  ctx.strokeStyle = BORDER;
  ctx.lineWidth = 3;
  ctx.beginPath(); ctx.moveTo(80, 158); ctx.lineTo(1120, 158); ctx.stroke();

  /* name */
  const name = m.display_name || m.username;
  ctx.fillStyle = SLATE;
  ctx.font = '900 52px "Plus Jakarta Sans", sans-serif';
  ctx.fillText(name.toUpperCase(), 80, 230);

  /* title + employer */
  const titleLine = [m.job_title, m.current_employer ? `at ${m.current_employer}` : ''].filter(Boolean).join(' · ');
  ctx.fillStyle = OCHRE;
  ctx.font = 'bold 26px "DM Sans", sans-serif';
  ctx.fillText(titleLine || 'BPU Verified Professional', 80, 272);

  /* location + sector */
  const loc = [m.location_city, m.where_in_the_uk].filter(Boolean).join(', ');
  ctx.fillStyle = '#555';
  ctx.font = 'bold 20px monospace';
  ctx.fillText(`LOC: ${loc || 'UK'}    SEC: ${m.industry || 'Professional'}`, 80, 315);

  /* skills */
  ctx.fillStyle = SLATE;
  ctx.font = 'bold 18px monospace';
  ctx.fillText('SKILLS:', 80, 370);
  const skills = (m.skills_separate || '').split(',').map(s => s.trim()).filter(Boolean).slice(0, 5);
  ctx.font = '500 20px "DM Sans", sans-serif';
  ctx.fillText(skills.join('  •  ') || '—', 80, 400);

  /* status footer */
  ctx.fillStyle = SLATE;
  ctx.fillRect(80, 480, 600, 64);
  ctx.fillStyle = PAPER;
  ctx.font = 'bold 18px monospace';
  ctx.fillText(`STATUS: ${deriveStatus(m)}  //  web.blackprofessionals.uk/members/${m.username}`, 100, 518);

  /* open to opps */
  if (m.open_to_opportunities === 'Yes' || m.open_to_opportunities === 'Passive') {
    ctx.fillStyle = '#16a34a';
    ctx.fillRect(700, 480, 440, 64);
    ctx.fillStyle = '#fff';
    ctx.font = 'bold 18px monospace';
    ctx.fillText('✓ OPEN TO OPPORTUNITIES', 720, 518);
  }

  const link = document.createElement('a');
  link.download = `BPU-Passport-${name.replace(/\s+/g, '-')}.png`;
  link.href = canvas.toDataURL('image/png');
  link.click();
}

/* ================================================================ */
export default function MemberProfileClient({ member, username, isOwner, viewer }: Props) {
  const [toast, setToast] = useState<string | null>(null);
  const [showMessage, setShowMessage] = useState(false);
  const [showMentorship, setShowMentorship] = useState(false);
  const [showPassport, setShowPassport] = useState(false);
  const [showShare, setShowShare] = useState(false);
  const [showRecommendation, setShowRecommendation] = useState(false);
  const [msgText, setMsgText] = useState('');
  const [msgSubject, setMsgSubject] = useState('');
  const [msgSending, setMsgSending] = useState(false);
  const [msgError, setMsgError] = useState<string | null>(null);
  const [recText, setRecText] = useState('');
  const [recAuthor, setRecAuthor] = useState(viewer?.display_name ?? '');
  const [recRole, setRecRole] = useState('');
  const [recSubmitting, setRecSubmitting] = useState(false);
  const [recError, setRecError] = useState<string | null>(null);
  const [testimonials, setTestimonials] = useState<PublicTestimonial[]>(member?.testimonials ?? []);
  const [endorsements, setEndorsements] = useState<{ skill: string; count: number }[]>(member?.endorsements ?? []);
  const [endorsedSkills, setEndorsedSkills] = useState<Set<string>>(new Set<string>());
  const [connectionState, setConnectionState] = useState<'not_connected' | 'pending' | 'connected'>('not_connected');
  const [showConnect, setShowConnect] = useState(false);
  const [connectContext, setConnectContext] = useState('Peer Professional');
  const [connectNote, setConnectNote] = useState('');
  const [connectSubmitting, setConnectSubmitting] = useState(false);

  const openToast = (msg: string) => setToast(msg);

  const handleEndorse = async (skill: string) => {
    try {
      const res = await fetch('/api/member/endorse', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ to_username: username, skill }),
      });
      const data = await res.json();
      if (!res.ok) throw new Error(data.error || 'Could not save endorsement.');
      const action: 'added' | 'removed' = data.action;
      setEndorsedSkills(prev => {
        const next = new Set(prev);
        if (action === 'added') next.add(skill); else next.delete(skill);
        return next;
      });
      setEndorsements(prev => {
        const hit = prev.find(e => e.skill === skill);
        if (hit) return prev.map(e => e.skill === skill ? { ...e, count: data.count } : e);
        return [...prev, { skill, count: data.count }];
      });
    } catch (err: unknown) {
      openToast(err instanceof Error ? err.message : 'Could not save endorsement.');
    }
  };
  const profileUrl = `https://web.blackprofessionals.uk/members/${username}`;

  /* ── Not found ── */
  if (!member) {
    return (
      <div
        className="min-h-screen flex flex-col items-center justify-center gap-6 p-8 text-center"
        style={{ background: PAPER }}
      >
        <div
          className="w-20 h-20 flex items-center justify-center font-black text-3xl text-white rounded"
          style={{ background: SLATE, border: `4px solid ${BORDER}` }}
        >
          ?
        </div>
        <h1 className="font-black text-3xl uppercase" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
          Member Not Found
        </h1>
        <p className="text-sm font-medium text-gray-600 max-w-sm">
          The profile for <strong>@{username}</strong> does not exist or has not been made public yet.
        </p>
        <Link
          href="/"
          className="px-6 py-3 font-black text-xs uppercase rounded text-white"
          style={{ background: SLATE, border: `3px solid ${BORDER}`, boxShadow: `4px 4px 0 ${BORDER}` }}
        >
          ← Back to BPU
        </Link>
      </div>
    );
  }

  const m = member;
  const name = m.display_name || m.username;
  const status = deriveStatus(m);
  const pNo = passportNo(m.id);
  const location = [m.location_city, m.where_in_the_uk, m.country_location !== 'United Kingdom' ? m.country_location : undefined].filter(Boolean).join(', ');
  const skills = (m.skills_separate || '').split(',').map(s => s.trim()).filter(Boolean);
  const topSkills = skills.slice(0, 5);
  const headline = m.profile_headline || [m.job_title, m.current_employer ? `at ${m.current_employer}` : ''].filter(Boolean).join(' · ') || `${m.industryfield_of_expertise || m.industry || 'Professional'} • BPU Verified Member`;

  return (
    <div className="min-h-screen flex flex-col" style={{ background: PAPER, fontFamily: '"DM Sans", sans-serif', color: SLATE }}>

      {/* ── Sticky nav ── */}
      <header
        className="sticky top-0 z-40"
        style={{ background: PAPER, borderBottom: `4px solid ${BORDER}` }}
      >
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-20 gap-4">
          <Link href="/" className="flex items-center gap-3">
            <div
              className="w-10 h-10 flex items-center justify-center font-black text-xl text-white"
              style={{ background: SLATE, border: `3px solid ${BORDER}`, boxShadow: `3px 3px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}
            >B</div>
            <div className="hidden sm:block">
              <span className="font-black text-sm uppercase tracking-tight block leading-none" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>BLACK PROFESSIONALS UK</span>
              <span className="text-[11px] font-bold uppercase tracking-widest block" style={{ color: OCHRE, fontFamily: 'monospace' }}>VERIFIED MEMBER PROFILE</span>
            </div>
          </Link>

          <div className="flex items-center gap-2">
            <button
              onClick={() => setShowShare(true)}
              className="px-4 py-2 text-xs font-black uppercase rounded"
              style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `3px 3px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}
            >
              ↗ Share
            </button>
            {isOwner && (
              <Link
                href="/profile"
                className="px-4 py-2 text-xs font-black uppercase rounded text-white"
                style={{ background: SLATE, border: `3px solid ${BORDER}`, boxShadow: `3px 3px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}
              >
                Edit Profile
              </Link>
            )}
          </div>
        </div>
      </header>

      <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full space-y-8">

        {/* ── Header profile card ── */}
        <section
          className="rounded-2xl overflow-hidden"
          style={{ background: '#fff', border: `4px solid ${BORDER}`, boxShadow: `12px 12px 0 ${BORDER}` }}
        >
          {/* Cover banner */}
          <div
            className="h-48 sm:h-64 relative flex items-center justify-between p-6 sm:p-10 border-b-4"
            style={{ background: SLATE, borderColor: BORDER }}
          >
            <div
              className="absolute inset-0 opacity-20"
              style={{ backgroundImage: 'radial-gradient(#F3ECE0 1px, transparent 1px)', backgroundSize: '16px 16px' }}
            />
            <div className="relative z-10 hidden sm:block space-y-2">
              <span className="inline-block text-white font-bold text-[10px] px-2.5 py-1 uppercase rounded" style={{ background: OCHRE, border: '1px solid #fff', fontFamily: 'monospace' }}>
                OFFICIAL BPU UK PROFILE
              </span>
              <h2 className="font-black text-2xl text-white uppercase tracking-tight leading-none" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                {status.replace('_', ' ')}
              </h2>
            </div>

            {/* Passport stamp */}
            <button
              onClick={() => setShowPassport(true)}
              className="relative z-10 p-3 rounded-xl flex items-center gap-3 hover:scale-105 transition-transform cursor-pointer"
              style={{ background: PAPER, border: `3px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}
            >
              <div className="w-10 h-10 flex items-center justify-center text-white rounded" style={{ background: OCHRE, border: '1px solid #000', fontSize: '1.2rem' }}>
                ⬛
              </div>
              <div className="text-left hidden sm:block">
                <span className="font-mono font-extrabold text-[11px] block leading-none" style={{ color: SLATE }}>{pNo}</span>
                <span className="font-mono text-[9px] font-bold uppercase block mt-1" style={{ color: OCHRE }}>CLICK TO SCAN & VERIFY ↗</span>
              </div>
            </button>
          </div>

          {/* Profile content */}
          <div className="px-6 sm:px-10 pb-8 pt-0 relative">

            {/* Avatar + actions row */}
            <div className="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 -mt-16 sm:-mt-20 mb-6">
              {/* Avatar */}
              <div className="relative">
                <div
                  className="w-32 h-32 sm:w-40 sm:h-40 rounded-2xl overflow-hidden bg-white"
                  style={{ border: `4px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}
                >
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img src={avatarUrl(member, username)} alt={name} className="w-full h-full object-cover" />
                </div>
                <span
                  className="absolute -bottom-2 -right-2 p-2 rounded-lg text-white"
                  style={{ background: OCHRE, border: `2px solid ${SLATE}`, boxShadow: `3px 3px 0 ${BORDER}` }}
                  title="Verified BPU Member"
                >
                  ✓
                </span>
              </div>

              {/* Action buttons */}
              <div className="flex flex-wrap items-center gap-3 w-full md:w-auto">
                {viewer && !isOwner && (
                  <>
                    {connectionState === 'not_connected' && (
                      <button
                        onClick={() => setShowConnect(true)}
                        className="flex-1 md:flex-initial px-6 py-3 text-xs font-black uppercase rounded-xl text-white flex items-center justify-center gap-2"
                        style={{ background: SLATE, border: `3px solid ${BORDER}`, boxShadow: `4px 4px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}
                      >
                        + Connect on BPU
                      </button>
                    )}
                    {connectionState === 'pending' && (
                      <button
                        onClick={() => { setConnectionState('not_connected'); openToast('Connection request withdrawn.'); }}
                        className="flex-1 md:flex-initial px-6 py-3 text-xs font-black uppercase rounded-xl flex items-center justify-center gap-2"
                        style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `4px 4px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif', color: SLATE }}
                      >
                        Request Sent ⏳
                      </button>
                    )}
                    {connectionState === 'connected' && (
                      <button
                        disabled
                        className="flex-1 md:flex-initial px-6 py-3 text-xs font-black uppercase rounded-xl flex items-center justify-center gap-2"
                        style={{ background: '#166534', border: `3px solid ${BORDER}`, boxShadow: `4px 4px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif', color: '#fff' }}
                      >
                        Connected ✓
                      </button>
                    )}
                    <button
                      onClick={() => setShowMessage(true)}
                      className="flex-1 md:flex-initial px-6 py-3 text-xs font-black uppercase rounded-xl text-white flex items-center justify-center gap-2"
                      style={{ background: OCHRE, border: `3px solid ${BORDER}`, boxShadow: `4px 4px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}
                    >
                      ✉ Message
                    </button>
                    <button
                      onClick={() => setShowMentorship(true)}
                      className="px-5 py-3 text-xs font-bold uppercase rounded-xl flex items-center gap-2"
                      style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `4px 4px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif', color: OCHRE }}
                    >
                      🤝 Mentorship
                    </button>
                  </>
                )}
                {isOwner && (
                  <>
                    <button
                      onClick={() => downloadPassport(m)}
                      className="px-6 py-3 text-xs font-black uppercase rounded-xl text-white flex items-center gap-2"
                      style={{ background: OCHRE, border: `3px solid ${BORDER}`, boxShadow: `4px 4px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}
                    >
                      ↓ Download Passport
                    </button>
                    <Link
                      href="/profile"
                      className="px-5 py-3 text-xs font-bold uppercase rounded-xl flex items-center gap-2"
                      style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `4px 4px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}
                    >
                      ✏ Edit Profile
                    </Link>
                  </>
                )}
                <button
                  onClick={() => setShowPassport(true)}
                  className="p-3 rounded-xl"
                  style={{ background: PAPER, border: `3px solid ${BORDER}`, boxShadow: `4px 4px 0 ${BORDER}` }}
                  title="View Official Passport"
                >
                  🪪
                </button>
              </div>
            </div>

            {/* Member info grid */}
            <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
              <div className="lg:col-span-8 space-y-2">
                <div className="flex flex-wrap items-center gap-2">
                  <h1 className="font-black text-3xl sm:text-4xl uppercase tracking-tight" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                    {name}
                  </h1>
                  <span
                    className="font-mono font-extrabold text-[10px] px-2.5 py-1 rounded uppercase"
                    style={{ border: `1px solid ${SLATE}`, background: PAPER }}
                  >
                    {status}
                  </span>
                </div>
                <p className="font-bold text-base sm:text-lg text-gray-800 leading-snug">{headline}</p>
                <div className="flex flex-wrap items-center gap-x-4 gap-y-1 font-mono text-xs text-gray-600 pt-1">
                  {location && <span>📍 <strong>{location}</strong></span>}
                  {m.current_employer && <><span>•</span><span>🏢 <strong>{m.current_employer}</strong></span></>}
                  {m.open_to_opportunities && m.open_to_opportunities !== 'No' && (
                    <span className="font-extrabold" style={{ color: '#16a34a' }}>
                      ✓ Open to Opportunities
                    </span>
                  )}
                </div>
              </div>

              {/* Right stats box */}
              <div
                className="lg:col-span-4 p-4 rounded-xl space-y-3"
                style={{ background: `${PAPER}80`, border: `2px solid ${BORDER}` }}
              >
                {m.placement_scheme && (
                  <div className="flex items-center justify-between text-xs font-black uppercase" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                    <span className="text-gray-600">Placement Scheme:</span>
                    <span style={{ color: OCHRE }}>{m.placement_scheme}</span>
                  </div>
                )}
                {m.mentorship_status && (
                  <div className="flex items-center justify-between text-xs font-black uppercase border-t pt-2" style={{ borderColor: `${BORDER}30`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                    <span className="text-gray-600">Mentorship:</span>
                    <span
                      className="px-2 py-0.5 rounded text-[10px]"
                      style={{
                        background: m.mentorship_status === 'Accepting Mentees' ? '#dcfce7' : m.mentorship_status === 'Active Mentor' ? '#dbeafe' : `${PAPER}`,
                        color: m.mentorship_status === 'Accepting Mentees' ? '#166534' : m.mentorship_status === 'Active Mentor' ? '#1e40af' : SLATE,
                        border: `1px solid ${m.mentorship_status === 'Accepting Mentees' ? '#86efac' : m.mentorship_status === 'Active Mentor' ? '#93c5fd' : BORDER}`,
                      }}
                    >
                      {m.mentorship_status}
                    </span>
                  </div>
                )}
                <div className={`flex items-center justify-between text-xs font-black uppercase${m.placement_scheme || m.mentorship_status ? ' border-t pt-2' : ''}`} style={{ borderColor: `${BORDER}30`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                  <span className="text-gray-600">Endorsements:</span>
                  <span className="font-mono" style={{ color: SLATE }}>{(m.total_endorsements ?? endorsements.reduce((s, e) => s + e.count, 0))} Peer Votes</span>
                </div>
                {m.years_of_experience && (
                  <div className="flex items-center justify-between text-xs font-black uppercase border-t pt-2" style={{ borderColor: `${BORDER}30`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                    <span className="text-gray-600">Experience:</span>
                    <span style={{ color: OCHRE }}>{m.years_of_experience} Yr{Number(m.years_of_experience) !== 1 ? 's' : ''}</span>
                  </div>
                )}
                {m.member_since && (
                  <div className="flex items-center justify-between text-xs font-black uppercase border-t pt-2" style={{ borderColor: `${BORDER}30`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                    <span className="text-gray-600">Member Since:</span>
                    <span style={{ color: SLATE }}>{m.member_since}</span>
                  </div>
                )}
                <div className="flex items-center justify-between text-xs font-black uppercase border-t pt-2" style={{ borderColor: `${BORDER}30`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                  <span className="text-gray-600">Passport:</span>
                  <span className="font-mono" style={{ color: OCHRE }}>{pNo.replace('PASSPORT NO. ', '')}</span>
                </div>
              </div>
            </div>
          </div>
        </section>


        {/* ── Two-column body ── */}
        <div className="grid grid-cols-1 lg:grid-cols-12 gap-8">

          {/* LEFT: main content */}
          <div className="lg:col-span-8 space-y-8">

            {/* About */}
            {m.user_bio && (
              <section
                className="rounded-2xl p-6 sm:p-8 space-y-4"
                style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}
              >
                <div className="flex justify-between items-center pb-3 border-b-2" style={{ borderColor: BORDER }}>
                  <h2 className="font-black text-xl uppercase flex items-center gap-2" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                    <span style={{ color: OCHRE }}>◆</span> About & Executive Summary
                  </h2>
                  <span className="font-mono text-[10px] font-bold text-gray-500 uppercase">VERIFIED BIO</span>
                </div>
                <div className="text-sm font-medium text-gray-800 leading-relaxed whitespace-pre-wrap">{m.user_bio}</div>

                {/* Stats strip */}
                <div className="grid grid-cols-3 gap-3 pt-4 border-t-2" style={{ borderColor: `${BORDER}30` }}>
                  <div className="p-3 text-center rounded-lg" style={{ background: `${PAPER}60`, border: `1px solid ${BORDER}` }}>
                    <span className="font-black text-xl block" style={{ color: OCHRE, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                      {m.years_of_experience ? `${m.years_of_experience}yr` : '—'}
                    </span>
                    <span className="font-mono text-[10px] font-bold text-gray-600 uppercase">Experience</span>
                  </div>
                  <div className="p-3 text-center rounded-lg" style={{ background: `${PAPER}60`, border: `1px solid ${BORDER}` }}>
                    <span className="font-black text-xl block" style={{ color: SLATE, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                      {m.certifications?.length ?? 0}
                    </span>
                    <span className="font-mono text-[10px] font-bold text-gray-600 uppercase">Certifications</span>
                  </div>
                  <div className="p-3 text-center rounded-lg" style={{ background: `${PAPER}60`, border: `1px solid ${BORDER}` }}>
                    <span className="font-black text-xl block" style={{ color: '#166534', fontFamily: 'Plus Jakarta Sans, sans-serif' }}>100%</span>
                    <span className="font-mono text-[10px] font-bold text-gray-600 uppercase">BPU Verified</span>
                  </div>
                </div>
              </section>
            )}

            {/* Skills & Badges */}
            {topSkills.length > 0 && (
              <section
                className="rounded-2xl p-6 sm:p-8 space-y-5"
                style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}
              >
                <div className="flex justify-between items-center pb-3 border-b-2" style={{ borderColor: BORDER }}>
                  <h2 className="font-black text-xl uppercase flex items-center gap-2" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                    <span style={{ color: OCHRE }}>◆</span> Verified Badges & Skills
                  </h2>
                </div>
                <div className="flex flex-wrap gap-2">
                  {skills.map(s => (
                    <span
                      key={s}
                      className="font-mono text-xs font-bold px-3 py-1.5 rounded"
                      style={{ border: `3px solid ${BORDER}`, background: PAPER, boxShadow: `3px 3px 0 ${BORDER}` }}
                    >
                      {s}
                    </span>
                  ))}
                </div>
                <div className="pt-4 border-t-2 space-y-3" style={{ borderColor: `${BORDER}30` }}>
                  <span className="font-mono text-[11px] font-bold uppercase text-gray-500 block">TOP ENDORSED COMPETENCIES:</span>
                  {(() => {
                    const maxVotes = Math.max(1, ...endorsements.map(e => e.count));
                    return topSkills.map(s => {
                      const endorsement = endorsements.find(e => e.skill.toLowerCase() === s.toLowerCase());
                      const votes = endorsement?.count ?? 0;
                      return (
                        <SkillBar
                          key={s}
                          name={s}
                          votes={votes}
                          maxVotes={maxVotes}
                          endorsed={endorsedSkills.has(s)}
                          onEndorse={viewer && !isOwner ? () => handleEndorse(s) : undefined}
                        />
                      );
                    });
                  })()}
                </div>
              </section>
            )}

            {/* Work Experience */}
            {(m.experiences?.length ?? 0) > 0 && (
              <section
                className="rounded-2xl p-6 sm:p-8 space-y-6"
                style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}
              >
                <div className="flex justify-between items-center pb-3 border-b-2" style={{ borderColor: BORDER }}>
                  <h2 className="font-black text-xl uppercase flex items-center gap-2" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                    <span style={{ color: OCHRE }}>◆</span> Work Experience
                  </h2>
                  <span className="font-mono text-[10px] font-bold text-gray-500 uppercase">VERIFIED TIMELINE</span>
                </div>
                <div className="space-y-8 relative" style={{ paddingLeft: '2rem' }}>
                  <div className="absolute left-3.5 top-0 bottom-0 w-1" style={{ background: BORDER }} />
                  {m.experiences!.map((exp, i) => (
                    <div key={i} className="relative space-y-1">
                      <div
                        className="absolute -left-6 top-1.5 w-5 h-5 rounded-full flex items-center justify-center text-white font-bold text-[9px]"
                        style={{ background: OCHRE, border: `2px solid ${BORDER}` }}
                      >
                        {i + 1}
                      </div>
                      <span className="font-mono text-[10px] font-bold uppercase block" style={{ color: OCHRE }}>
                        {exp.start_date || ''}{exp.start_date && (exp.end_date || exp.is_current) ? ' — ' : ''}{exp.is_current ? 'Present' : exp.end_date || ''}
                      </span>
                      <h3 className="font-black text-lg uppercase" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>{exp.title}</h3>
                      <p className="font-bold text-xs text-gray-600">{exp.company}</p>
                      {exp.description && <p className="text-xs font-medium text-gray-700 leading-relaxed pt-1">{exp.description}</p>}
                    </div>
                  ))}
                </div>
              </section>
            )}

            {/* Education */}
            {(m.educations?.length ?? 0) > 0 && (
              <section
                className="rounded-2xl p-6 sm:p-8 space-y-6"
                style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}
              >
                <div className="pb-3 border-b-2" style={{ borderColor: BORDER }}>
                  <h2 className="font-black text-xl uppercase flex items-center gap-2" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                    <span style={{ color: OCHRE }}>◆</span> Education & BPU Accredited Training
                  </h2>
                </div>
                <div className="space-y-4">
                  {m.educations!.map((edu, i) => (
                    <div key={i} className="p-4 rounded-xl space-y-1" style={{ background: `${PAPER}50`, border: `3px solid ${BORDER}` }}>
                      <span className="font-mono text-[10px] font-bold uppercase block" style={{ color: OCHRE }}>
                        {edu.start_year || ''}{edu.start_year && edu.end_year ? ' – ' : ''}{edu.end_year || ''}
                      </span>
                      <h3 className="font-black text-base uppercase" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                        {[edu.degree, edu.field_of_study].filter(Boolean).join(', ')}
                      </h3>
                      <p className="font-bold text-xs text-gray-600">{edu.institution}</p>
                    </div>
                  ))}
                  {(m.certifications?.length ?? 0) > 0 && (
                    <div className="pt-2 space-y-2">
                      <span className="font-mono text-[10px] font-bold text-gray-500 uppercase block">CERTIFICATIONS:</span>
                      {m.certifications!.map((cert, i) => (
                        <div key={i} className="flex items-start gap-2 text-sm">
                          <span style={{ color: OCHRE }} className="mt-0.5">✓</span>
                          <span>
                            <span className="font-bold">{cert.name}</span>
                            {cert.issuer && <span className="text-gray-500"> — {cert.issuer}</span>}
                            {cert.year && <span className="text-gray-400"> ({cert.year})</span>}
                          </span>
                        </div>
                      ))}
                    </div>
                  )}
                </div>
              </section>
            )}

            {/* Recommendations & Testimonials */}
            <section
              className="rounded-2xl p-6 sm:p-8 space-y-6"
              style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}
            >
              <div className="flex justify-between items-center pb-3 border-b-2" style={{ borderColor: BORDER }}>
                <h2 className="font-black text-xl uppercase flex items-center gap-2" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                  <span style={{ color: OCHRE, fontSize: '1.25rem', lineHeight: 1 }}>&ldquo;&rdquo;</span> Recommendations &amp; Testimonials
                </h2>
                {viewer && !isOwner && (
                  <button
                    onClick={() => setShowRecommendation(true)}
                    className="font-mono font-extrabold text-[10px] uppercase px-3 py-1.5 rounded"
                    style={{ border: `2px solid ${OCHRE}`, color: OCHRE, background: 'transparent' }}
                  >
                    + Write Recommendation
                  </button>
                )}
              </div>
              {testimonials.length === 0 ? (
                <div className="py-8 text-center space-y-2">
                  <p className="font-mono text-xs text-gray-500 uppercase">No recommendations yet.</p>
                  {viewer && !isOwner && (
                    <button
                      onClick={() => setShowRecommendation(true)}
                      className="font-black text-xs uppercase px-4 py-2 rounded text-white mt-2"
                      style={{ background: OCHRE, border: `3px solid ${BORDER}`, boxShadow: `3px 3px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}
                    >
                      Be the first to recommend {name.split(' ')[0]} ↗
                    </button>
                  )}
                </div>
              ) : (
                <div className="space-y-4">
                  {testimonials.map((t, i) => (
                    <div key={i} className="p-5 rounded-xl relative space-y-2" style={{ background: PAPER, border: `3px solid ${BORDER}` }}>
                      <span className="absolute top-3 right-4 font-black text-3xl opacity-30" style={{ color: OCHRE }}>&ldquo;</span>
                      <p className="text-sm font-medium text-gray-800 italic leading-relaxed">&ldquo;{t.quote}&rdquo;</p>
                      <div className="flex items-center gap-2 pt-2 border-t" style={{ borderColor: `${BORDER}20` }}>
                        <div className="w-7 h-7 flex items-center justify-center font-black text-xs text-white rounded" style={{ background: SLATE, border: `1px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                          {t.author.charAt(0)}
                        </div>
                        <div>
                          <span className="font-black text-xs uppercase block" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>{t.author}</span>
                          <span className="font-mono text-[9px] font-bold text-gray-500 uppercase block">{t.role}</span>
                        </div>
                      </div>
                    </div>
                  ))}
                </div>
              )}
            </section>

          </div>


          {/* RIGHT: sidebar */}
          <div className="lg:col-span-4 space-y-8">

            {/* Passport QR widget */}
            <div
              className="rounded-2xl p-6 space-y-5 text-white relative overflow-hidden"
              style={{ background: SLATE, border: `3px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}
            >
              <div className="flex justify-between items-start pb-3 border-b-2" style={{ borderColor: 'rgba(255,255,255,0.2)' }}>
                <div>
                  <span className="font-black text-xs uppercase block" style={{ color: OCHRE, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>BPU IDENTITY SYSTEM</span>
                  <h3 className="font-black text-base uppercase" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Digital Passport Pass</h3>
                </div>
                <span style={{ color: OCHRE, fontSize: '1.5rem' }}>🛡</span>
              </div>

              <div className="p-4 rounded-xl border space-y-4 text-center" style={{ background: PAPER, color: SLATE, border: `3px solid ${BORDER}` }}>
                <div className="w-28 h-28 bg-white border-2 border-black mx-auto p-2 flex items-center justify-center">
                  <svg viewBox="0 0 24 24" className="w-full h-full" fill={SLATE}>
                    <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm9-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm11-2h2v2h-2v-2zm4 0h2v4h-2v-4zm-4 4h2v4h-2v-4zm4 2h2v2h-2v-2zm-6-2h2v2h-2v-2z"/>
                  </svg>
                </div>
                <div className="space-y-1">
                  <span className="font-mono font-extrabold text-xs block" style={{ color: OCHRE }}>{pNo}</span>
                  <span className="font-mono text-[9px] font-bold text-gray-600 block uppercase">SCAN WITH MOBILE CAMERA TO VERIFY</span>
                </div>
              </div>

              <button
                onClick={() => setShowPassport(true)}
                className="w-full py-3 rounded-xl font-black text-xs uppercase tracking-wider text-white"
                style={{ background: OCHRE, border: `3px solid ${BORDER}`, boxShadow: `4px 4px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}
              >
                Expand Official Passport Card ↗
              </button>

              {isOwner && (
                <button
                  onClick={() => downloadPassport(m)}
                  className="w-full py-3 rounded-xl font-black text-xs uppercase tracking-wider"
                  style={{ background: PAPER, color: SLATE, border: `3px solid ${BORDER}`, boxShadow: `4px 4px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}
                >
                  ↓ Download Passport PNG
                </button>
              )}
            </div>

            {/* LinkedIn link if available */}
            {m.linkedin_url && (
              <div
                className="rounded-2xl p-6 space-y-3"
                style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}
              >
                <h3 className="font-black text-sm uppercase" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Connect Elsewhere</h3>
                <a
                  href={m.linkedin_url}
                  target="_blank"
                  rel="noopener noreferrer"
                  className="flex items-center gap-3 p-3 rounded-xl font-bold text-sm"
                  style={{ background: PAPER, border: `2px solid ${BORDER}` }}
                >
                  <span className="w-8 h-8 flex items-center justify-center rounded text-white font-black text-xs" style={{ background: '#0077B5' }}>in</span>
                  LinkedIn Profile ↗
                </a>
              </div>
            )}

          </div>
        </div>
      </main>


      {/* ── Footer ── */}
      <footer className="pt-12 pb-8 border-t-4 mt-auto" style={{ background: SLATE, borderColor: OCHRE, color: PAPER }}>
        <div className="max-w-7xl mx-auto px-4 text-center space-y-3">
          <div className="flex justify-center items-center gap-2">
            <div className="w-7 h-7 flex items-center justify-center font-black text-sm" style={{ background: '#fff', color: SLATE, border: `3px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>B</div>
            <span className="font-black text-sm uppercase tracking-tight" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Black Professionals UK</span>
          </div>
          <p className="font-mono text-[10px] text-gray-500 uppercase">© 2026 Black Professionals United Kingdom. All rights reserved.</p>
        </div>
      </footer>


      {/* ── Modals ── */}

      {/* Message modal */}
      <Modal id="modal-message" open={showMessage} onClose={() => { setShowMessage(false); setMsgError(null); }}>
        <ModalHeader title="Direct BPU Network Message" onClose={() => { setShowMessage(false); setMsgError(null); }} />
        <form
          onSubmit={async e => {
            e.preventDefault();
            setMsgSending(true);
            setMsgError(null);
            try {
              const res = await fetch('/api/member/message', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ to_username: username, subject: msgSubject, message: msgText }),
              });
              const data = await res.json();
              if (!res.ok) throw new Error(data.error || 'Message could not be sent.');
              setShowMessage(false);
              setMsgText('');
              setMsgSubject('');
              openToast(`Message sent to ${name}!`);
            } catch (err: unknown) {
              setMsgError(err instanceof Error ? err.message : 'Could not send message.');
            } finally {
              setMsgSending(false);
            }
          }}
          className="p-6 space-y-4"
        >
          <div>
            <label className="block font-black text-xs uppercase mb-1" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Recipient</label>
            <input readOnly value={name} className="w-full p-2.5 rounded font-bold text-xs" style={{ border: `3px solid ${BORDER}`, background: `${PAPER}80` }} />
          </div>
          <div>
            <label className="block font-black text-xs uppercase mb-1" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Subject <span style={{ color: OCHRE }}>*</span></label>
            <input required value={msgSubject} onChange={e => setMsgSubject(e.target.value)} placeholder="e.g. Collaboration Opportunity" className="w-full p-2.5 rounded font-medium text-xs focus:outline-none" style={{ border: `3px solid ${BORDER}` }} />
          </div>
          <div>
            <label className="block font-black text-xs uppercase mb-1" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Message <span style={{ color: OCHRE }}>*</span></label>
            <textarea required rows={4} value={msgText} onChange={e => setMsgText(e.target.value)} placeholder="Type your message…" className="w-full p-2.5 rounded font-medium text-xs focus:outline-none" style={{ border: `3px solid ${BORDER}` }} />
          </div>
          {msgError && <p className="text-xs font-bold text-red-600">{msgError}</p>}
          <div className="flex justify-end gap-2 pt-2">
            <button type="button" onClick={() => { setShowMessage(false); setMsgError(null); }} className="px-4 py-2 rounded font-black text-xs uppercase" style={{ border: `3px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Cancel</button>
            <button type="submit" disabled={msgSending} className="px-6 py-2 rounded font-black text-xs uppercase text-white" style={{ background: OCHRE, border: `3px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif', opacity: msgSending ? 0.7 : 1 }}>
              {msgSending ? 'Sending…' : 'Send ↗'}
            </button>
          </div>
        </form>
      </Modal>

      {/* Mentorship modal */}
      <Modal id="modal-mentorship" open={showMentorship} onClose={() => setShowMentorship(false)}>
        <ModalHeader title="BPU Mentorship Programme" onClose={() => setShowMentorship(false)} />
        <form
          onSubmit={e => { e.preventDefault(); setShowMentorship(false); openToast('Mentorship request sent! BPU Intro Committee will respond via email.'); }}
          className="p-6 space-y-4"
        >
          <div className="p-3 rounded text-xs font-medium" style={{ background: `${PAPER}80`, border: `1px solid ${BORDER}` }}>
            You are requesting mentorship from <strong>{name}</strong>. Our committee will coordinate the intro.
          </div>
          <div>
            <label className="block font-black text-xs uppercase mb-1" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Your Career Stage <span style={{ color: OCHRE }}>*</span></label>
            <select required className="w-full p-2.5 rounded font-medium text-xs bg-white focus:outline-none" style={{ border: `3px solid ${BORDER}` }}>
              <option value="">— Choose Stage —</option>
              <option>Student / Recent Graduate</option>
              <option>Early Career (1–3 Yrs)</option>
              <option>Mid-Level (4–7 Yrs)</option>
              <option>Career Transitioning into STEM</option>
            </select>
          </div>
          <div>
            <label className="block font-black text-xs uppercase mb-1" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Mentorship Goal <span style={{ color: OCHRE }}>*</span></label>
            <textarea required rows={3} placeholder="Briefly outline what guidance you are seeking…" className="w-full p-2.5 rounded font-medium text-xs focus:outline-none" style={{ border: `3px solid ${BORDER}` }} />
          </div>
          <div className="flex justify-end gap-2 pt-2">
            <button type="button" onClick={() => setShowMentorship(false)} className="px-4 py-2 rounded font-black text-xs uppercase" style={{ border: `3px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Cancel</button>
            <button type="submit" className="px-6 py-2 rounded font-black text-xs uppercase text-white" style={{ background: OCHRE, border: `3px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Request Intro ↗</button>
          </div>
        </form>
      </Modal>

      {/* Passport verify modal */}
      <Modal id="modal-passport" open={showPassport} onClose={() => setShowPassport(false)}>
        <ModalHeader title="Verified Credential Scanner" onClose={() => setShowPassport(false)} />
        <div className="p-6 space-y-5 text-center">
          <div className="p-6 rounded-xl space-y-4" style={{ background: PAPER, border: `3px solid ${BORDER}` }}>
            <div className="w-36 h-36 bg-white border-2 border-black mx-auto p-2 flex items-center justify-center">
              <svg viewBox="0 0 24 24" className="w-full h-full" fill={SLATE}>
                <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm9-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm11-2h2v2h-2v-2zm4 0h2v4h-2v-4zm-4 4h2v4h-2v-4zm4 2h2v2h-2v-2zm-6-2h2v2h-2v-2z"/>
              </svg>
            </div>
            <div className="space-y-1">
              <h3 className="font-black text-lg uppercase" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>{name}</h3>
              <p className="font-bold text-xs text-gray-600">{m.job_title || m.industryfield_of_expertise || 'Professional'}</p>
              <span className="font-mono font-extrabold text-xs block pt-1" style={{ color: OCHRE }}>{pNo}</span>
            </div>
            <div className="flex items-center justify-center gap-1.5 font-mono text-[10px] font-bold" style={{ color: '#166534' }}>
              ✓ AUTHENTICATED BPU UNITED KINGDOM RECORD
            </div>
          </div>
          <div className="flex gap-2">
            <button
              onClick={() => { navigator.clipboard?.writeText(profileUrl); openToast('Profile link copied!'); setShowPassport(false); }}
              className="flex-1 py-2.5 rounded font-black text-xs uppercase"
              style={{ background: '#fff', border: `3px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}
            >
              Copy Profile Link
            </button>
            <button
              onClick={() => setShowPassport(false)}
              className="flex-1 py-2.5 rounded font-black text-xs uppercase text-white"
              style={{ background: SLATE, border: `3px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}
            >
              Close
            </button>
          </div>
        </div>
      </Modal>

      {/* Share modal */}
      <Modal id="modal-share" open={showShare} onClose={() => setShowShare(false)}>
        <ModalHeader title="Share Member Profile" onClose={() => setShowShare(false)} />
        <div className="p-6 space-y-4">
          <p className="text-xs font-medium text-gray-600">Share this verified BPU member profile across professional networks.</p>
          <div className="p-3 rounded flex justify-between items-center" style={{ background: PAPER, border: `3px solid ${BORDER}`, fontFamily: 'monospace', fontSize: '12px' }}>
            <span className="truncate pr-2">{profileUrl}</span>
            <button
              onClick={() => { navigator.clipboard?.writeText(profileUrl); openToast('Copied!'); setShowShare(false); }}
              className="px-3 py-1 rounded text-[10px] uppercase font-bold text-white flex-shrink-0"
              style={{ background: SLATE }}
            >Copy</button>
          </div>
          <div className="flex justify-end">
            <button onClick={() => setShowShare(false)} className="px-4 py-2 rounded font-black text-xs uppercase" style={{ border: `3px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Close</button>
          </div>
        </div>
      </Modal>

      {/* Connect on BPU modal */}
      <Modal id="modal-connect" open={showConnect} onClose={() => setShowConnect(false)}>
        <ModalHeader title="BPU Network Connection Request" onClose={() => setShowConnect(false)} />
        <form
          onSubmit={async e => {
            e.preventDefault();
            setConnectSubmitting(true);
            await new Promise(r => setTimeout(r, 600)); // optimistic delay
            setConnectSubmitting(false);
            setShowConnect(false);
            setConnectContext('Peer Professional');
            setConnectNote('');
            setConnectionState('pending');
            openToast(`Connection request sent to ${name}!`);
          }}
          className="p-6 space-y-4"
        >
          <div className="p-3 rounded flex items-center gap-3 text-xs font-medium" style={{ background: `${PAPER}80`, border: `1px solid ${BORDER}` }}>
            <div className="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0" style={{ border: `2px solid ${BORDER}` }}>
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src={avatarUrl(member, username)} alt={name} className="w-full h-full object-cover" />
            </div>
            <div>
              <span className="font-black text-xs uppercase block" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>{name}</span>
              <span className="text-gray-600 block leading-tight">{m.job_title || m.industryfield_of_expertise || 'BPU Member'}</span>
            </div>
          </div>
          <div>
            <label className="block font-black text-xs uppercase mb-1" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>How do you know each other? <span style={{ color: OCHRE }}>*</span></label>
            <select
              required
              value={connectContext}
              onChange={e => setConnectContext(e.target.value)}
              className="w-full p-2.5 rounded font-medium text-xs bg-white focus:outline-none"
              style={{ border: `3px solid ${BORDER}` }}
            >
              <option value="Peer Professional">Peer / Industry Professional</option>
              <option value="Seeking Mentorship">Seeking Career Guidance / Mentorship</option>
              <option value="Recruiter / Hiring Lead">Recruiter / Employer Representative</option>
              <option value="Colleague / Placement Alumni">Colleague / Placement Alumni</option>
            </select>
          </div>
          <div>
            <label className="block font-black text-xs uppercase mb-1" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Personal Note <span className="text-gray-400 normal-case font-medium">(optional but recommended)</span></label>
            <textarea
              rows={3}
              value={connectNote}
              onChange={e => setConnectNote(e.target.value)}
              placeholder={`Hi ${name.split(' ')[0]}, I'd love to connect with you on BPU…`}
              className="w-full p-2.5 rounded font-medium text-xs focus:outline-none"
              style={{ border: `3px solid ${BORDER}` }}
            />
          </div>
          <div className="flex justify-end gap-2 pt-2">
            <button type="button" onClick={() => setShowConnect(false)} className="px-4 py-2 rounded font-black text-xs uppercase" style={{ border: `3px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Cancel</button>
            <button type="submit" disabled={connectSubmitting} className="px-6 py-2 rounded font-black text-xs uppercase text-white" style={{ background: SLATE, border: `3px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif', opacity: connectSubmitting ? 0.7 : 1 }}>
              {connectSubmitting ? 'Sending…' : 'Send Invitation ↗'}
            </button>
          </div>
        </form>
      </Modal>

      {/* Write Recommendation modal */}
      <Modal id="modal-recommendation" open={showRecommendation} onClose={() => { setShowRecommendation(false); setRecError(null); }}>
        <ModalHeader title="Write a Recommendation" onClose={() => { setShowRecommendation(false); setRecError(null); }} />
        <form
          onSubmit={async e => {
            e.preventDefault();
            setRecSubmitting(true);
            setRecError(null);
            try {
              const res = await fetch('/api/member/testimonial', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ to_username: username, quote: recText, author_name: recAuthor, role: recRole }),
              });
              const data = await res.json();
              if (!res.ok) throw new Error(data.error || 'Could not save recommendation.');
              // Optimistically append to local testimonials list
              setTestimonials(prev => [...prev, { quote: recText, author: recAuthor, role: recRole }]);
              setShowRecommendation(false);
              setRecText('');
              setRecRole('');
              openToast('Your recommendation has been published!');
            } catch (err: unknown) {
              setRecError(err instanceof Error ? err.message : 'Could not submit recommendation.');
            } finally {
              setRecSubmitting(false);
            }
          }}
          className="p-6 space-y-4"
        >
          <div className="p-3 rounded text-xs font-medium" style={{ background: `${PAPER}80`, border: `1px solid ${BORDER}` }}>
            You are writing a recommendation for <strong>{name}</strong>. It will appear on their public profile immediately.
          </div>
          <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label className="block font-black text-xs uppercase mb-1" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Your name <span style={{ color: OCHRE }}>*</span></label>
              <input required value={recAuthor} onChange={e => setRecAuthor(e.target.value)} placeholder="Your full name" className="w-full p-2.5 rounded font-medium text-xs focus:outline-none" style={{ border: `3px solid ${BORDER}` }} />
            </div>
            <div>
              <label className="block font-black text-xs uppercase mb-1" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Your title &amp; org</label>
              <input value={recRole} onChange={e => setRecRole(e.target.value)} placeholder="e.g. Senior Engineer at HSBC" className="w-full p-2.5 rounded font-medium text-xs focus:outline-none" style={{ border: `3px solid ${BORDER}` }} />
            </div>
          </div>
          <div>
            <label className="block font-black text-xs uppercase mb-1" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Recommendation <span style={{ color: OCHRE }}>*</span></label>
            <textarea
              required
              rows={5}
              value={recText}
              onChange={e => setRecText(e.target.value)}
              placeholder={`Share how you know ${name.split(' ')[0]} and what makes them stand out professionally…`}
              className="w-full p-2.5 rounded font-medium text-xs focus:outline-none"
              style={{ border: `3px solid ${BORDER}` }}
            />
            <p className="text-[10px] text-gray-500 mt-1 font-mono uppercase">Minimum 50 characters. Be specific — great recommendations mention impact.</p>
          </div>
          {recError && <p className="text-xs font-bold text-red-600">{recError}</p>}
          <div className="flex justify-end gap-2 pt-2">
            <button type="button" onClick={() => { setShowRecommendation(false); setRecError(null); }} className="px-4 py-2 rounded font-black text-xs uppercase" style={{ border: `3px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Cancel</button>
            <button type="submit" disabled={recSubmitting || recText.length < 50} className="px-6 py-2 rounded font-black text-xs uppercase text-white" style={{ background: OCHRE, border: `3px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif', opacity: (recSubmitting || recText.length < 50) ? 0.6 : 1 }}>
              {recSubmitting ? 'Publishing…' : 'Publish Recommendation ↗'}
            </button>
          </div>
        </form>
      </Modal>

      {/* Toast */}
      {toast && <Toast msg={toast} onClose={() => setToast(null)} />}
    </div>
  );
}
