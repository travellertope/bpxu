'use client';

import React, { useRef } from 'react';
import { BPUUser } from '@/lib/auth';

interface Props {
  user: BPUUser;
}

type Theme = 'paper' | 'dark' | 'emerald' | 'ochre';

const THEMES: Record<Theme, { bg: string; text: string; border: string; sub: string }> = {
  paper:   { bg: '#F3ECE0', text: '#111317', border: '#1E2229', sub: 'rgba(17,19,23,0.55)' },
  dark:    { bg: '#111317', text: '#F3ECE0', border: '#F3ECE0', sub: 'rgba(243,236,224,0.55)' },
  emerald: { bg: '#0A3925', text: '#F3ECE0', border: '#F3ECE0', sub: 'rgba(243,236,224,0.55)' },
  ochre:   { bg: '#C5491F', text: '#FFFFFF', border: '#1E2229', sub: 'rgba(255,255,255,0.65)' },
};

const ACCENT = '#C8102E';   // BPU red

function derivePassportStatus(user: BPUUser): string {
  if (user.roles?.includes('bpu_mentor') || user.roles?.includes('mentor')) return 'ALUMNI MENTOR';
  if (user.is_pro) return 'PRO MEMBER';
  return 'VERIFIED MEMBER';
}

function completionScore(user: BPUUser): { score: number; missing: string[] } {
  const p = user.profile;
  const fields: [boolean, string][] = [
    [!!p.first_name && !!p.last_name,   'Full name'],
    [!!p.job_title,                      'Job title'],
    [!!p.current_employer,               'Current employer'],
    [!!p.location_city,                  'City'],
    [!!p.industry,                       'Industry'],
    [!!p.skills_separate,                'Skills'],
    [!!p.years_of_experience,            'Years of experience'],
    [!!p.linkedin_url,                   'LinkedIn URL'],
    [!!p.profile_headline,               'Headline'],
  ];
  const missing = fields.filter(([ok]) => !ok).map(([, label]) => label);
  const score = Math.round(((fields.length - missing.length) / fields.length) * 100);
  return { score, missing };
}

export default function PassportCard({ user }: Props) {
  const [theme, setTheme] = React.useState<Theme>('paper');
  const [copied, setCopied] = React.useState(false);
  const cardRef = useRef<HTMLDivElement>(null);

  const p = user.profile;
  const t = THEMES[theme];
  const status = derivePassportStatus(user);
  const { score, missing } = completionScore(user);

  const name = [p.first_name, p.last_name].filter(Boolean).join(' ') || user.display_name || 'Your Name';
  const title = p.job_title || 'Add your job title';
  const employer = p.current_employer ? `at ${p.current_employer}` : '';
  const location = [p.location_city, p.where_in_the_uk].filter(Boolean).join(', ') || 'Location';
  const sector = p.industry || 'Industry';
  const badges = (p.skills_separate || '').split(',').map(s => s.trim()).filter(Boolean).slice(0, 4);
  const topCert = user.certifications?.[0]?.name;
  if (topCert && badges.length < 4 && !badges.includes(topCert)) badges.push(topCert);

  const yoe = p.years_of_experience;
  const certCount = user.certifications?.length ?? 0;
  const impact = [
    yoe ? `${yoe} Yr${Number(yoe) !== 1 ? 's' : ''} Experience` : null,
    certCount > 0 ? `${certCount} Certification${certCount > 1 ? 's' : ''}` : null,
  ].filter(Boolean).join(' • ') || 'Add experience details';

  const passportNo = `PASSPORT NO. 2026-${String(user.id).padStart(4, '0')}`;
  const profileUrl = `https://web.blackprofessionals.uk/members/${user.username}`;
  const gravatarUrl = `https://www.gravatar.com/avatar/${user.email}?s=300&d=mp`;

  const copyLink = async () => {
    await navigator.clipboard.writeText(profileUrl).catch(() => {});
    setCopied(true);
    setTimeout(() => setCopied(false), 2000);
  };

  return (
    <div className="space-y-4">
      {/* Completion bar */}
      <div className="card card-p space-y-3">
        <div className="flex items-center justify-between gap-3">
          <div>
            <p className="text-xs font-bold uppercase tracking-wide text-text-3">Passport Completion</p>
            <p className="text-sm font-semibold mt-0.5">{score}% complete</p>
          </div>
          <span className={`text-xs font-bold px-2 py-1 rounded ${score === 100 ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700'}`}>
            {score === 100 ? 'Ready to share' : `${missing.length} field${missing.length !== 1 ? 's' : ''} missing`}
          </span>
        </div>
        <div className="h-2 w-full bg-gray-200 rounded-full overflow-hidden">
          <div
            className="h-full rounded-full transition-all duration-500"
            style={{ width: `${score}%`, background: score === 100 ? '#16a34a' : ACCENT }}
          />
        </div>
        {missing.length > 0 && (
          <p className="text-xs text-text-3">
            Missing: <span className="font-semibold text-text-2">{missing.slice(0, 3).join(', ')}{missing.length > 3 ? ` +${missing.length - 3} more` : ''}</span>
            <span className="ml-1">— fill in the form below.</span>
          </p>
        )}
      </div>

      {/* Passport card render */}
      <div
        ref={cardRef}
        className="rounded-xl overflow-hidden"
        style={{
          background: t.bg,
          color: t.text,
          border: `4px solid ${t.border}`,
          boxShadow: `6px 6px 0px ${t.border}`,
          fontFamily: '"Plus Jakarta Sans", "Inter", sans-serif',
        }}
      >
        {/* Card header */}
        <div
          className="flex justify-between items-start px-6 py-4"
          style={{ borderBottom: `2px solid ${t.border}` }}
        >
          <div className="flex items-center gap-2.5">
            <div className="w-7 h-7 flex items-center justify-center font-black text-sm text-white" style={{ background: ACCENT, border: `2px solid ${t.border}` }}>B</div>
            <div>
              <span className="font-black text-xs uppercase tracking-tight block leading-none">BLACK PROFESSIONALS UK</span>
              <span className="text-[10px] font-bold block mt-0.5" style={{ color: t.sub, fontFamily: 'monospace', letterSpacing: '0.1em' }}>VERIFIED DIGITAL PASSPORT</span>
            </div>
          </div>
          <span className="font-mono font-extrabold text-[11px] px-2.5 py-1 rounded text-white" style={{ background: ACCENT, border: `1px solid ${t.border}` }}>
            {passportNo}
          </span>
        </div>

        {/* Card body */}
        <div className="p-6 grid grid-cols-12 gap-5 items-center">
          <div className="col-span-4">
            <div className="aspect-square rounded overflow-hidden" style={{ border: `2px solid ${t.border}` }}>
              {/* eslint-disable-next-line @next/next/no-img-element */}
              <img src={gravatarUrl} alt={name} className="w-full h-full object-cover" />
            </div>
          </div>
          <div className="col-span-8 space-y-1.5">
            <span className="inline-block font-mono font-extrabold text-[10px] uppercase px-2 py-0.5 rounded text-white" style={{ background: ACCENT, border: `1px solid ${t.border}` }}>
              {status}
            </span>
            <h2 className="font-black text-2xl uppercase tracking-tight leading-none" style={{ color: t.text }}>{name}</h2>
            <p className="font-bold text-sm leading-snug" style={{ color: t.sub }}>
              {title}{employer ? <span> <span style={{ color: ACCENT }}>·</span> {employer}</span> : null}
            </p>
            {p.profile_headline && (
              <p className="text-xs italic leading-snug" style={{ color: t.sub }}>&ldquo;{p.profile_headline}&rdquo;</p>
            )}
            <div className="pt-1.5 text-[11px] space-y-0.5" style={{ borderTop: `1px solid ${t.border}40`, fontFamily: 'monospace' }}>
              <div><span style={{ color: t.sub }}>LOC: </span><strong>{location}</strong></div>
              <div><span style={{ color: t.sub }}>SEC: </span><strong>{sector}</strong></div>
            </div>
          </div>
        </div>

        {/* Badges */}
        {badges.length > 0 && (
          <div className="px-6 pb-4 space-y-1.5">
            <span className="font-mono text-[9px] font-bold uppercase block" style={{ color: t.sub }}>VERIFIED SKILLS & ROLES:</span>
            <div className="flex flex-wrap gap-1.5">
              {badges.map(b => (
                <span key={b} className="font-mono text-[10px] font-bold px-2 py-0.5 rounded" style={{ border: `1px solid ${t.border}`, background: 'rgba(255,255,255,0.12)' }}>
                  {b}
                </span>
              ))}
            </div>
          </div>
        )}

        {/* Footer */}
        <div className="px-6 pt-4 pb-6 flex justify-between items-end gap-4" style={{ borderTop: `2px solid ${t.border}` }}>
          <div className="space-y-0.5">
            <span className="font-mono text-[9px] font-bold uppercase block" style={{ color: t.sub }}>VERIFIED IMPACT:</span>
            <p className="font-black text-xs leading-tight">{impact}</p>
            {p.open_to_opportunities === 'Yes' && (
              <span className="inline-block mt-1 font-mono text-[9px] font-bold uppercase px-2 py-0.5 rounded text-white" style={{ background: '#16a34a' }}>
                ✓ Open to Opportunities
              </span>
            )}
          </div>
          {/* QR placeholder — links to profile URL */}
          <a href={profileUrl} target="_blank" rel="noopener noreferrer" className="flex-shrink-0 text-center group">
            <div className="w-12 h-12 p-1 flex items-center justify-center" style={{ background: '#fff', border: `1px solid ${t.border}` }}>
              <svg viewBox="0 0 24 24" className="w-full h-full" fill={t.text}>
                <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm9-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm11-2h2v2h-2v-2zm4 0h2v4h-2v-4zm-4 4h2v4h-2v-4zm4 2h2v2h-2v-2zm-6-2h2v2h-2v-2z"/>
              </svg>
            </div>
            <span className="font-mono text-[8px] font-bold uppercase block mt-0.5" style={{ color: t.sub }}>VERIFY</span>
          </a>
        </div>
      </div>

      {/* Theme picker + actions */}
      <div className="flex items-center justify-between gap-3 flex-wrap">
        <div className="flex items-center gap-2">
          <span className="text-[11px] font-bold text-text-3 uppercase tracking-wide">Theme:</span>
          {(['paper', 'dark', 'emerald', 'ochre'] as Theme[]).map(th => (
            <button
              key={th}
              onClick={() => setTheme(th)}
              title={th}
              className="w-5 h-5 rounded-full border-2 transition-transform hover:scale-110"
              style={{
                background: THEMES[th].bg,
                borderColor: theme === th ? ACCENT : '#ccc',
                boxShadow: theme === th ? `0 0 0 2px ${ACCENT}` : 'none',
              }}
            />
          ))}
        </div>
        <div className="flex gap-2">
          <button
            onClick={copyLink}
            className="btn btn-outline btn-sm"
          >
            {copied ? '✓ Copied' : '🔗 Copy link'}
          </button>
          <a href={profileUrl} target="_blank" rel="noopener noreferrer" className="btn btn-amber btn-sm">
            View public profile ↗
          </a>
        </div>
      </div>
    </div>
  );
}
