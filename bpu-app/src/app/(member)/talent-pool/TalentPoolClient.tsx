'use client';

import { useState, useEffect, useCallback } from 'react';
import type { BPUUser } from '@/lib/auth';

interface TalentMember {
    id: number;
    display_name: string;
    photo_url?: string;
    cv_url?: string;
    programme: string;
    city?: string;
    skills?: string;
    industry?: string;
    years_experience?: string;
    availability?: string;
    email?: string;
}

const PROGRAMME_LABEL: Record<string, string> = {
    'black-interns':      'Black Interns',
    'experienced-talent': 'Experienced Talent',
    'nextgen':            '#NextGen',
};

const PROGRAMME_BADGE: Record<string, string> = {
    'black-interns':      'badge-blue',
    'experienced-talent': 'badge-purple',
    'nextgen':            'badge-amber',
};

interface Props { user: BPUUser; }

export default function TalentPoolClient({ user }: Props) {
    const isEmployer = user.roles.includes('bpu_employer') || user.roles.includes('administrator') || user.roles.includes('bpu_editor');
    const [view, setView] = useState<'member' | 'browse'>(isEmployer ? 'browse' : 'member');

    return (
        <div className="max-w-5xl mx-auto px-4 py-8">
            <div className="mb-8 flex items-center justify-between flex-wrap gap-4">
                <div>
                    <h1 className="text-2xl font-bold">Talent Pool</h1>
                    <p className="text-text-2 mt-1 text-sm">
                        {isEmployer
                            ? 'Browse opted-in BPU members available for your programmes.'
                            : 'Opt in to make your profile visible to partner organisations.'}
                    </p>
                </div>
                {isEmployer && (
                    <div className="flex gap-2">
                        <button
                            onClick={() => setView('member')}
                            className={`btn btn-sm ${view === 'member' ? 'btn-primary' : 'btn-ghost'}`}
                        >
                            My Status
                        </button>
                        <button
                            onClick={() => setView('browse')}
                            className={`btn btn-sm ${view === 'browse' ? 'btn-primary' : 'btn-ghost'}`}
                        >
                            Browse Talent
                        </button>
                    </div>
                )}
            </div>

            {view === 'member' && <MemberOptIn user={user} />}
            {view === 'browse' && isEmployer && <TalentBrowser />}
        </div>
    );
}

/* ── Member opt-in panel ─────────────────────────────────────── */

function MemberOptIn({ user }: { user: BPUUser }) {
    const p = user.profile;
    const [optedIn,  setOptedIn]  = useState<boolean | null>(null);
    const [saving,   setSaving]   = useState(false);
    const [error,    setError]    = useState('');
    const [success,  setSuccess]  = useState('');

    useEffect(() => {
        fetch('/api/member/talent-pool')
            .then(r => r.json())
            .then(d => setOptedIn(d.opted_in ?? false))
            .catch(() => setOptedIn(false));
    }, []);

    async function toggle() {
        setSaving(true);
        setError('');
        setSuccess('');
        try {
            const res  = await fetch('/api/member/talent-pool', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ opt_in: !optedIn }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to update');
            setOptedIn(!optedIn);
            setSuccess(!optedIn ? 'You are now visible in the talent pool.' : 'You have been removed from the talent pool.');
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Something went wrong.');
        } finally {
            setSaving(false);
        }
    }

    const profileFields = [
        { label: 'Name',       value: [p.first_name, p.last_name].filter(Boolean).join(' ') },
        { label: 'City',       value: p.location_city || p.where_in_the_uk },
        { label: 'Industry',   value: p.industry || p.industryfield_of_expertise },
        { label: 'Skills',     value: p.skills_separate },
        { label: 'Experience', value: p.years_of_experience },
    ];
    const filled  = profileFields.filter(f => f.value).length;
    const pct     = Math.round((filled / profileFields.length) * 100);
    const complete = pct >= 80;

    return (
        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            {/* Status card */}
            <div className="card card-p">
                <h2 className="font-bold mb-4">Talent Pool Status</h2>

                {optedIn === null ? (
                    <p className="text-text-3 text-sm">Loading…</p>
                ) : (
                    <>
                        <div className={`flex items-center gap-3 p-4 rounded-xl mb-5 ${optedIn ? 'bg-green-50 border border-green-200' : 'bg-bg border border-border'}`}>
                            <div className={`w-10 h-10 rounded-full flex items-center justify-center text-lg flex-shrink-0 ${optedIn ? 'bg-green-100 text-green-600' : 'bg-bg border border-border text-text-3'}`}>
                                {optedIn ? '✓' : '○'}
                            </div>
                            <div>
                                <p className="font-semibold text-sm">{optedIn ? 'Opted In' : 'Not in Talent Pool'}</p>
                                <p className="text-xs text-text-3">
                                    {optedIn
                                        ? 'Your profile is visible to partner organisations.'
                                        : 'Your profile is private — not visible to employers.'}
                                </p>
                            </div>
                        </div>

                        {success && <p className="text-sm text-green-600 mb-3">{success}</p>}
                        {error   && <p className="text-sm text-red-500 mb-3">{error}</p>}

                        <button
                            onClick={toggle}
                            disabled={saving || (!complete && !optedIn)}
                            className={`btn w-full ${optedIn ? 'btn-ghost' : 'btn-primary'} disabled:opacity-50`}
                        >
                            {saving
                                ? 'Saving…'
                                : optedIn
                                    ? 'Remove Me from Talent Pool'
                                    : 'Join the Talent Pool'}
                        </button>

                        {!complete && !optedIn && (
                            <p className="text-xs text-amber-600 mt-3 text-center">
                                Complete your profile to at least 80% before joining the talent pool.
                            </p>
                        )}
                    </>
                )}
            </div>

            {/* Profile completeness card */}
            <div className="card card-p">
                <h2 className="font-bold mb-4">Profile Completeness</h2>
                <div className="flex items-center gap-3 mb-4">
                    <div className="flex-1 h-3 rounded-full bg-bg border border-border overflow-hidden">
                        <div
                            className={`h-full rounded-full transition-all ${pct >= 80 ? 'bg-green-500' : pct >= 50 ? 'bg-amber-400' : 'bg-red-400'}`}
                            style={{ width: `${pct}%` }}
                        />
                    </div>
                    <span className="text-sm font-bold w-10 text-right">{pct}%</span>
                </div>
                <ul className="space-y-2">
                    {profileFields.map(f => (
                        <li key={f.label} className="flex items-center gap-2 text-sm">
                            <span className={`w-4 h-4 rounded-full flex items-center justify-center text-[10px] font-bold ${f.value ? 'bg-green-100 text-green-600' : 'bg-red-50 text-red-400'}`}>
                                {f.value ? '✓' : '✗'}
                            </span>
                            <span className={f.value ? 'text-text' : 'text-text-3'}>{f.label}</span>
                            {!f.value && (
                                <a href="/profile" className="text-xs text-brand hover:underline ml-auto">Add →</a>
                            )}
                        </li>
                    ))}
                </ul>
                {!complete && (
                    <a href="/profile" className="btn btn-ghost btn-sm w-full mt-4">Complete Your Profile</a>
                )}
            </div>

            {/* What employers see */}
            <div className="card card-p md:col-span-2">
                <h2 className="font-bold mb-4">What Employers Will See</h2>
                <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 text-sm">
                    <div className="p-3 rounded-lg bg-bg border border-border">
                        <p className="font-semibold mb-1">Visible</p>
                        <ul className="text-text-2 space-y-1 text-xs">
                            <li>✓ First name &amp; last initial</li>
                            <li>✓ Programme accepted to</li>
                            <li>✓ City / region</li>
                            <li>✓ Industry &amp; skills</li>
                            <li>✓ Years of experience</li>
                            <li>✓ CV (if uploaded)</li>
                        </ul>
                    </div>
                    <div className="p-3 rounded-lg bg-bg border border-border">
                        <p className="font-semibold mb-1">Hidden</p>
                        <ul className="text-text-2 space-y-1 text-xs">
                            <li>✗ Full email address</li>
                            <li>✗ Phone number</li>
                            <li>✗ Date of birth</li>
                            <li>✗ Disability / health info</li>
                            <li>✗ Sexuality</li>
                        </ul>
                    </div>
                    <div className="p-3 rounded-lg bg-green-50 border border-green-200">
                        <p className="font-semibold mb-1 text-green-700">Your Control</p>
                        <ul className="text-green-700 space-y-1 text-xs">
                            <li>✓ Opt out at any time</li>
                            <li>✓ Employers request contact via BPU</li>
                            <li>✓ You approve introductions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    );
}

/* ── Employer talent browser ─────────────────────────────────── */

function TalentBrowser() {
    const [members,      setMembers]      = useState<TalentMember[]>([]);
    const [total,        setTotal]        = useState(0);
    const [loading,      setLoading]      = useState(true);
    const [error,        setError]        = useState('');
    const [programme,    setProgramme]    = useState('all');
    const [searchInput,  setSearchInput]  = useState('');
    const [search,       setSearch]       = useState('');
    const [page,         setPage]         = useState(1);
    const [selected,     setSelected]     = useState<TalentMember | null>(null);
    const perPage = 20;

    const load = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const p = new URLSearchParams({ page: String(page), per_page: String(perPage) });
            if (programme !== 'all') p.set('programme', programme);
            if (search.trim())       p.set('search', search.trim());
            const res  = await fetch(`/api/talent-pool?${p}`);
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load');
            setMembers(data.members || []);
            setTotal(data.total || 0);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load talent pool');
        } finally {
            setLoading(false);
        }
    }, [programme, search, page]);

    useEffect(() => { load(); }, [load]);

    const pages = Math.ceil(total / perPage);

    return (
        <div className="flex gap-6" style={{ alignItems: 'flex-start' }}>
            {/* List */}
            <div className="flex-1 min-w-0">
                {/* Filters */}
                <div className="flex flex-wrap gap-3 mb-5 items-center">
                    <select
                        className="input-sm"
                        value={programme}
                        onChange={e => { setProgramme(e.target.value); setPage(1); }}
                    >
                        <option value="all">All Programmes</option>
                        <option value="black-interns">Black Interns</option>
                        <option value="experienced-talent">Experienced Talent</option>
                        <option value="nextgen">#NextGen</option>
                    </select>

                    <form
                        className="flex gap-2 ml-auto"
                        onSubmit={e => { e.preventDefault(); setSearch(searchInput); setPage(1); }}
                    >
                        <input
                            className="input-sm"
                            placeholder="Search name or skill…"
                            value={searchInput}
                            onChange={e => setSearchInput(e.target.value)}
                            style={{ minWidth: 200 }}
                        />
                        <button type="submit" className="btn btn-sm btn-primary">Search</button>
                    </form>
                </div>

                {error && <p className="text-sm text-red-500 mb-4">{error}</p>}

                {loading ? (
                    <div className="text-text-3 text-sm py-12 text-center">Loading talent pool…</div>
                ) : members.length === 0 ? (
                    <div className="card card-p text-center text-text-3 text-sm py-12">No members found.</div>
                ) : (
                    <>
                        <p className="text-xs text-text-3 mb-3">{total} member{total !== 1 ? 's' : ''} in pool</p>
                        <div className="flex flex-col gap-2">
                            {members.map(m => (
                                <button
                                    key={m.id}
                                    onClick={() => setSelected(m)}
                                    className="card card-p text-left w-full hover:shadow-md transition-shadow"
                                    style={{ borderColor: selected?.id === m.id ? 'var(--brand)' : undefined }}
                                >
                                    <div className="flex items-center gap-3">
                                        {m.photo_url ? (
                                            <img src={m.photo_url} alt="" className="w-10 h-10 rounded-full object-cover flex-shrink-0" />
                                        ) : (
                                            <div className="w-10 h-10 rounded-full bg-brand/10 text-brand flex items-center justify-center font-bold text-sm flex-shrink-0">
                                                {m.display_name?.[0]?.toUpperCase() || '?'}
                                            </div>
                                        )}
                                        <div className="flex-1 min-w-0">
                                            <div className="flex items-center gap-2 flex-wrap mb-0.5">
                                                <span className="font-semibold text-sm">{m.display_name}</span>
                                                <span className={`badge ${PROGRAMME_BADGE[m.programme] || 'badge-gray'} text-[10px]`}>
                                                    {PROGRAMME_LABEL[m.programme] || m.programme}
                                                </span>
                                            </div>
                                            <p className="text-xs text-text-3">
                                                {[m.city, m.industry].filter(Boolean).join(' · ')}
                                            </p>
                                            {m.skills && (
                                                <p className="text-xs text-text-2 mt-0.5 truncate">{m.skills}</p>
                                            )}
                                        </div>
                                        {m.cv_url && (
                                            <span className="badge badge-gray text-[10px] flex-shrink-0">CV</span>
                                        )}
                                    </div>
                                </button>
                            ))}
                        </div>

                        {pages > 1 && (
                            <div className="flex justify-center gap-2 mt-6">
                                <button className="btn btn-sm btn-ghost" disabled={page === 1} onClick={() => setPage(p => p - 1)}>← Prev</button>
                                <span className="text-sm text-text-2 self-center">Page {page} of {pages}</span>
                                <button className="btn btn-sm btn-ghost" disabled={page >= pages} onClick={() => setPage(p => p + 1)}>Next →</button>
                            </div>
                        )}
                    </>
                )}
            </div>

            {/* Detail panel */}
            {selected && (
                <div className="card" style={{ width: 360, flexShrink: 0, position: 'sticky', top: 24, maxHeight: 'calc(100vh - 120px)', overflowY: 'auto' }}>
                    <div className="card-p" style={{ borderBottom: '1px solid var(--border)' }}>
                        <div className="flex items-start justify-between gap-2 mb-3">
                            <div className="flex items-center gap-3">
                                {selected.photo_url ? (
                                    <img src={selected.photo_url} alt="" className="w-12 h-12 rounded-full object-cover" />
                                ) : (
                                    <div className="w-12 h-12 rounded-full bg-brand/10 text-brand flex items-center justify-center font-bold text-lg">
                                        {selected.display_name?.[0]?.toUpperCase() || '?'}
                                    </div>
                                )}
                                <div>
                                    <p className="font-bold">{selected.display_name}</p>
                                    <span className={`badge ${PROGRAMME_BADGE[selected.programme] || 'badge-gray'} text-[10px]`}>
                                        {PROGRAMME_LABEL[selected.programme] || selected.programme}
                                    </span>
                                </div>
                            </div>
                            <button onClick={() => setSelected(null)} className="text-text-3 hover:text-text text-lg leading-none">×</button>
                        </div>
                    </div>

                    <div className="card-p space-y-3">
                        {selected.city && (
                            <div>
                                <p className="text-[10px] font-bold uppercase text-text-3 mb-0.5">Location</p>
                                <p className="text-sm">{selected.city}</p>
                            </div>
                        )}
                        {selected.industry && (
                            <div>
                                <p className="text-[10px] font-bold uppercase text-text-3 mb-0.5">Industry</p>
                                <p className="text-sm">{selected.industry}</p>
                            </div>
                        )}
                        {selected.years_experience && (
                            <div>
                                <p className="text-[10px] font-bold uppercase text-text-3 mb-0.5">Experience</p>
                                <p className="text-sm">{selected.years_experience}</p>
                            </div>
                        )}
                        {selected.skills && (
                            <div>
                                <p className="text-[10px] font-bold uppercase text-text-3 mb-0.5">Skills</p>
                                <p className="text-sm" style={{ whiteSpace: 'pre-wrap' }}>{selected.skills}</p>
                            </div>
                        )}
                        {selected.availability && (
                            <div>
                                <p className="text-[10px] font-bold uppercase text-text-3 mb-0.5">Availability</p>
                                <p className="text-sm">{selected.availability}</p>
                            </div>
                        )}

                        <div className="pt-3 flex flex-col gap-2">
                            {selected.cv_url && (
                                <a href={selected.cv_url} target="_blank" rel="noreferrer"
                                    className="btn btn-sm btn-ghost w-full">
                                    Download CV ↗
                                </a>
                            )}
                            {selected.email && (
                                <a href={`mailto:${selected.email}`}
                                    className="btn btn-sm btn-primary w-full">
                                    Contact via Email
                                </a>
                            )}
                            {!selected.email && (
                                <p className="text-xs text-text-3 text-center">
                                    Contact this member through BPU admin to arrange an introduction.
                                </p>
                            )}
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
