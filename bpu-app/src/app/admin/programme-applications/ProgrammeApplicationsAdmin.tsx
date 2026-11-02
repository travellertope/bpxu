'use client';

import { useState, useEffect, useCallback } from 'react';

interface ProgrammeApplication {
    id: number;
    programme: 'black-interns' | 'experienced-talent' | 'nextgen';
    status: string;
    data: Record<string, string>;
    notes: string;
    cv_url?: string;
    created_at: string;
    updated_at: string;
}

type ProgrammeFilter = 'all' | 'black-interns' | 'experienced-talent' | 'nextgen';
type StatusFilter = 'all' | 'pending' | 'under_review' | 'shortlisted' | 'accepted' | 'rejected';

const PROGRAMME_TABS: { value: ProgrammeFilter; label: string }[] = [
    { value: 'all',               label: 'All Programmes' },
    { value: 'black-interns',     label: 'Black Interns' },
    { value: 'experienced-talent',label: 'Experienced Talent' },
    { value: 'nextgen',           label: '#NextGen' },
];

const STATUS_OPTIONS: { value: string; label: string }[] = [
    { value: 'pending',      label: 'Pending' },
    { value: 'under_review', label: 'Under Review' },
    { value: 'shortlisted',  label: 'Shortlisted' },
    { value: 'accepted',     label: 'Accepted' },
    { value: 'rejected',     label: 'Rejected' },
];

const STATUS_BADGE: Record<string, string> = {
    pending:      'badge-amber',
    under_review: 'badge-purple',
    shortlisted:  'badge-blue',
    accepted:     'badge-green',
    rejected:     'badge-red',
};

const PROGRAMME_LABEL: Record<string, string> = {
    'black-interns':      'Black Interns',
    'experienced-talent': 'Experienced Talent',
    'nextgen':            '#NextGen',
};

function fmtShort(iso: string) {
    return new Date(iso).toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
}

function DataField({ label, value }: { label: string; value: string | undefined }) {
    if (!value) return null;
    return (
        <div className="mb-3">
            <p className="text-[10px] font-bold uppercase text-text-3 mb-0.5">{label}</p>
            <p className="text-sm text-text" style={{ whiteSpace: 'pre-wrap' }}>{value}</p>
        </div>
    );
}

export default function ProgrammeApplicationsAdmin() {
    const [applications, setApplications] = useState<ProgrammeApplication[]>([]);
    const [total, setTotal]               = useState(0);
    const [loading, setLoading]           = useState(true);
    const [error, setError]               = useState('');
    const [programme, setProgramme]       = useState<ProgrammeFilter>('all');
    const [statusFilter, setStatusFilter] = useState<StatusFilter>('all');
    const [search, setSearch]             = useState('');
    const [searchInput, setSearchInput]   = useState('');
    const [page, setPage]                 = useState(1);
    const perPage = 25;

    const [selected, setSelected]         = useState<ProgrammeApplication | null>(null);
    const [note, setNote]                 = useState('');
    const [savingNote, setSavingNote]     = useState(false);
    const [updatingStatus, setUpdatingStatus] = useState(false);
    const [actionError, setActionError]   = useState('');

    const fetchApplications = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const params = new URLSearchParams({ page: String(page), per_page: String(perPage) });
            if (programme !== 'all')    params.set('programme', programme);
            if (statusFilter !== 'all') params.set('status', statusFilter);
            if (search.trim())          params.set('search', search.trim());
            const res  = await fetch(`/api/paired/admin/programme-applications?${params}`);
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load applications');
            setApplications(data.applications || []);
            setTotal(data.total || 0);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load applications');
        } finally {
            setLoading(false);
        }
    }, [programme, statusFilter, search, page]);

    useEffect(() => { fetchApplications(); }, [fetchApplications]);

    async function updateStatus(newStatus: string) {
        if (!selected) return;
        setUpdatingStatus(true);
        setActionError('');
        try {
            const res  = await fetch(`/api/paired/admin/programme-applications/${selected.id}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ status: newStatus }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to update status');
            setSelected(a => a ? { ...a, status: newStatus } : a);
            fetchApplications();
        } catch (e) {
            setActionError(e instanceof Error ? e.message : 'Failed to update status');
        } finally {
            setUpdatingStatus(false);
        }
    }

    async function addNote(e: React.FormEvent) {
        e.preventDefault();
        if (!selected || !note.trim()) return;
        setSavingNote(true);
        setActionError('');
        try {
            const res  = await fetch(`/api/paired/admin/programme-applications/${selected.id}/status`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ note }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to save note');
            const appended = selected.notes ? selected.notes + '\n\n' + (data.note || note) : (data.note || note);
            setSelected(a => a ? { ...a, notes: appended } : a);
            setNote('');
        } catch (e) {
            setActionError(e instanceof Error ? e.message : 'Failed to save note');
        } finally {
            setSavingNote(false);
        }
    }

    const pages = Math.ceil(total / perPage);

    return (
        <div className="flex gap-6" style={{ alignItems: 'flex-start' }}>
            {/* Left: list */}
            <div className="flex-1 min-w-0">
                {/* Programme tabs */}
                <div className="flex flex-wrap gap-2 mb-4">
                    {PROGRAMME_TABS.map(t => (
                        <button
                            key={t.value}
                            onClick={() => { setProgramme(t.value); setPage(1); }}
                            className={`px-3 py-1.5 rounded-lg text-xs font-bold border transition-colors ${programme === t.value ? 'bg-brand text-white border-brand' : 'bg-white text-text-2 border-border hover:border-text-2'}`}
                        >
                            {t.label}
                        </button>
                    ))}
                </div>

                {/* Status filter + search */}
                <div className="flex flex-wrap gap-3 mb-5 items-center">
                    <select
                        className="input-sm"
                        value={statusFilter}
                        onChange={e => { setStatusFilter(e.target.value as StatusFilter); setPage(1); }}
                    >
                        <option value="all">All Statuses</option>
                        {STATUS_OPTIONS.map(o => <option key={o.value} value={o.value}>{o.label}</option>)}
                    </select>
                    <form className="flex gap-2 ml-auto" onSubmit={e => { e.preventDefault(); setSearch(searchInput); setPage(1); }}>
                        <input
                            className="input-sm"
                            placeholder="Search name or email…"
                            value={searchInput}
                            onChange={e => setSearchInput(e.target.value)}
                            style={{ minWidth: 200 }}
                        />
                        <button type="submit" className="btn btn-sm btn-primary">Search</button>
                    </form>
                </div>

                {error && <p className="text-sm text-red-500 mb-4">{error}</p>}

                {loading ? (
                    <div className="text-text-3 text-sm py-12 text-center">Loading applications…</div>
                ) : applications.length === 0 ? (
                    <div className="card card-p text-center text-text-3 text-sm py-12">No applications found.</div>
                ) : (
                    <>
                        <div className="flex flex-col gap-2">
                            {applications.map(app => (
                                <button
                                    key={app.id}
                                    onClick={() => { setSelected(app); setActionError(''); setNote(''); }}
                                    className="card card-p text-left w-full hover:shadow-md transition-shadow"
                                    style={{ borderColor: selected?.id === app.id ? 'var(--brand)' : undefined }}
                                >
                                    <div className="flex items-start justify-between gap-3 flex-wrap">
                                        <div className="min-w-0 flex-1">
                                            <div className="flex items-center gap-2 flex-wrap mb-1">
                                                <span className="badge badge-gray text-[10px]">{PROGRAMME_LABEL[app.programme] || app.programme}</span>
                                                <span className={`badge ${STATUS_BADGE[app.status] || 'badge-gray'} text-[10px]`}>
                                                    {app.status.replace('_', ' ')}
                                                </span>
                                            </div>
                                            <p className="font-semibold text-sm">
                                                {app.data['first-name'] || app.data['your-name'] || '—'}
                                            </p>
                                            <p className="text-xs text-text-3 mt-0.5">{app.data['your-email']}</p>
                                        </div>
                                        <p className="text-xs text-text-3 flex-shrink-0">{fmtShort(app.created_at)}</p>
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

            {/* Right: detail panel */}
            {selected && (
                <div className="card" style={{ width: 440, flexShrink: 0, position: 'sticky', top: 24, maxHeight: 'calc(100vh - 120px)', display: 'flex', flexDirection: 'column', overflowY: 'auto' }}>
                    {/* Header */}
                    <div className="card-p" style={{ borderBottom: '1px solid var(--border)' }}>
                        <div className="flex items-start justify-between gap-2 mb-2">
                            <div>
                                <span className="badge badge-gray text-[10px]">{PROGRAMME_LABEL[selected.programme] || selected.programme}</span>
                                <p className="font-bold text-sm mt-1">
                                    {selected.data['first-name'] || selected.data['your-name'] || '—'}
                                </p>
                                <p className="text-xs text-text-3">{selected.data['your-email']}</p>
                            </div>
                            <button onClick={() => setSelected(null)} className="text-text-3 hover:text-text text-lg leading-none flex-shrink-0">×</button>
                        </div>

                        <div className="mt-2">
                            <label className="text-xs text-text-3 font-semibold block mb-1">Status</label>
                            <div className="flex gap-1 flex-wrap">
                                {STATUS_OPTIONS.map(opt => (
                                    <button
                                        key={opt.value}
                                        onClick={() => updateStatus(opt.value)}
                                        disabled={updatingStatus || selected.status === opt.value}
                                        className={`px-2 py-1 rounded text-[10px] font-bold border transition-colors ${selected.status === opt.value ? 'bg-brand text-white border-brand' : 'bg-white text-text-2 border-border hover:border-text-2'}`}
                                    >
                                        {opt.label}
                                    </button>
                                ))}
                            </div>
                        </div>
                    </div>

                    {/* Submission data */}
                    <div className="card-p" style={{ borderBottom: '1px solid var(--border)' }}>
                        <p className="text-[10px] font-bold uppercase text-text-3 mb-3">Submission</p>

                        {/* Common fields */}
                        <DataField label="Email"       value={selected.data['your-email']} />
                        <DataField label="City"        value={selected.data['nearest-city'] || selected.data['your-location']} />
                        <DataField label="Nationality" value={selected.data['your-nationality']} />
                        <DataField label="Ethnicity"   value={selected.data['ethnic-background']} />
                        <DataField label="Gender"      value={selected.data['applicant-gender']} />
                        <DataField label="Work Rights" value={selected.data['work-rights']} />

                        {/* Black Interns specific */}
                        {selected.programme === 'black-interns' && <>
                            <DataField label="Career Interest" value={selected.data['selected-other']} />
                            <DataField label="University"      value={selected.data['which-uni']} />
                            <DataField label="Year of Study"   value={selected.data['year-of-study']} />
                        </>}

                        {/* Experienced Talent specific */}
                        {selected.programme === 'experienced-talent' && <>
                            <DataField label="Years Experience" value={selected.data['years-experience']} />
                        </>}

                        {/* NextGen specific */}
                        {selected.programme === 'nextgen' && <>
                            <DataField label="Phone"           value={selected.data['your-phone']} />
                            <DataField label="Date of Birth"   value={selected.data['date-321']} />
                            <DataField label="School/College"  value={selected.data['your-school']} />
                            <DataField label="Level of Study"  value={selected.data['current-level']} />
                            <DataField label="Subject Area"    value={selected.data['select-area']} />
                            <DataField label="Key Skills"      value={selected.data['your-skills']} />
                            <DataField label="Availability"    value={selected.data['select-jobtype']} />
                            <DataField label="Aspirations"     value={selected.data['textarea-aspiration']} />
                            <DataField label="Support Needs"   value={selected.data['your-disabilities']} />
                        </>}

                        {selected.cv_url && (
                            <a href={selected.cv_url} target="_blank" rel="noreferrer" className="btn btn-sm btn-ghost mt-2">
                                Download CV ↗
                            </a>
                        )}
                        <p className="text-xs text-text-3 mt-3">Submitted {fmtShort(selected.created_at)}</p>
                    </div>

                    {/* Internal notes */}
                    <div className="card-p">
                        <p className="text-[10px] font-bold uppercase text-text-3 mb-2">Internal Notes</p>
                        {selected.notes && (
                            <pre className="text-xs text-text-2 whitespace-pre-wrap mb-3 p-2 rounded" style={{ background: 'var(--bg)', border: '1px solid var(--border)' }}>
                                {selected.notes}
                            </pre>
                        )}
                        {actionError && <p className="text-xs text-red-500 mb-2">{actionError}</p>}
                        <form onSubmit={addNote}>
                            <textarea
                                className="input-sm w-full mb-2"
                                rows={2}
                                placeholder="Add an internal note…"
                                value={note}
                                onChange={e => setNote(e.target.value)}
                                style={{ resize: 'vertical', fontSize: 12 }}
                            />
                            <button
                                type="submit"
                                className="btn btn-sm btn-ghost w-full"
                                disabled={savingNote || !note.trim()}
                            >
                                {savingNote ? 'Saving…' : 'Save Note'}
                            </button>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
