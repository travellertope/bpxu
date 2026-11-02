'use client';

import { useState, useRef } from 'react';

const INPUT_CLASS = 'w-full px-4 py-3 rounded-xl font-medium text-sm border-brutal bg-white focus:outline-none focus:ring-2 focus:ring-hp-accent';
const LABEL_CLASS = 'block text-xs font-black uppercase tracking-widest mb-1.5';

const PROGRAMMES = [
    { value: 'Black Interns Programme',        label: 'Black Interns Programme' },
    { value: 'Experienced Talent Programme',   label: 'Experienced Talent Programme' },
    { value: 'NextGen Apprenticeship Program', label: 'NextGen Apprenticeship Programme' },
];

export default function TalentAccessForm() {
    const [orgName,     setOrgName]     = useState('');
    const [contactName, setContactName] = useState('');
    const [contactRole, setContactRole] = useState('');
    const [email,       setEmail]       = useState('');
    const [programmes,  setProgrammes]  = useState<string[]>([]);
    const [additional,  setAdditional]  = useState('');
    const [submitting,  setSubmitting]  = useState(false);
    const [submitted,   setSubmitted]   = useState(false);
    const [error,       setError]       = useState('');
    const formRef = useRef<HTMLFormElement>(null);

    function toggleProgramme(val: string) {
        setProgrammes(prev => prev.includes(val) ? prev.filter(p => p !== val) : [...prev, val]);
    }

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (programmes.length === 0) { setError('Please select at least one programme of interest.'); return; }
        setError('');
        setSubmitting(true);
        try {
            const fd = new FormData();
            fd.append('organisation-name',   orgName);
            fd.append('contact-name',        contactName);
            fd.append('contact-title',       contactRole);
            fd.append('your-email',          email);
            programmes.forEach(p => fd.append('interest-programme[]', p));
            fd.append('additional-information', additional);

            const res  = await fetch('/api/programmes/talent-access', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.status === 'mail_sent' || res.ok) {
                setSubmitted(true);
            } else {
                setError(data.message || 'Something went wrong. Please try again.');
            }
        } catch {
            setError('Could not reach the server. Please try again.');
        } finally {
            setSubmitting(false);
        }
    }

    if (submitted) {
        return (
            <div className="p-8 rounded-2xl border-brutal shadow-brutal bg-white text-center space-y-4">
                <div className="w-16 h-16 rounded-full border-brutal shadow-brutal-sm bg-hp-accent text-white flex items-center justify-center text-2xl mx-auto">✓</div>
                <h3 className="font-black uppercase text-xl tracking-tight">Interest Registered</h3>
                <p className="text-text-2 max-w-sm mx-auto">
                    Thank you for registering your interest in BPU Talent Access. Our partnerships team will be in touch shortly.
                </p>
            </div>
        );
    }

    return (
        <form ref={formRef} onSubmit={handleSubmit} className="space-y-5">
            {error && (
                <div className="p-4 rounded-xl border-2 border-red-400 bg-red-50 text-red-700 text-sm font-medium">
                    {error}
                </div>
            )}

            <div>
                <label className={LABEL_CLASS}>Organisation Name *</label>
                <input
                    type="text"
                    required
                    value={orgName}
                    onChange={e => setOrgName(e.target.value)}
                    placeholder="Name of Organisation"
                    className={INPUT_CLASS}
                />
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label className={LABEL_CLASS}>Contact Person *</label>
                    <input
                        type="text"
                        required
                        value={contactName}
                        onChange={e => setContactName(e.target.value)}
                        placeholder="Name of Contact Person"
                        className={INPUT_CLASS}
                    />
                </div>
                <div>
                    <label className={LABEL_CLASS}>Office Position *</label>
                    <input
                        type="text"
                        required
                        value={contactRole}
                        onChange={e => setContactRole(e.target.value)}
                        placeholder="Contact's Office Position"
                        className={INPUT_CLASS}
                    />
                </div>
            </div>

            <div>
                <label className={LABEL_CLASS}>Contact Email *</label>
                <input
                    type="email"
                    required
                    value={email}
                    onChange={e => setEmail(e.target.value)}
                    placeholder="Contact's Email Address"
                    className={INPUT_CLASS}
                />
            </div>

            <div>
                <label className={LABEL_CLASS}>Programme of Interest * <span className="normal-case font-medium text-text-3">(select all that apply)</span></label>
                <div className="space-y-2 mt-2">
                    {PROGRAMMES.map(p => (
                        <label key={p.value} className="flex items-center gap-3 cursor-pointer group">
                            <span className={`w-5 h-5 rounded border-brutal flex items-center justify-center flex-shrink-0 transition-colors ${programmes.includes(p.value) ? 'bg-hp-accent border-hp-accent' : 'bg-white'}`}>
                                {programmes.includes(p.value) && (
                                    <svg className="w-3 h-3 text-white" fill="none" viewBox="0 0 12 10">
                                        <path d="M1 5l3 3 7-7" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                    </svg>
                                )}
                            </span>
                            <input
                                type="checkbox"
                                className="sr-only"
                                checked={programmes.includes(p.value)}
                                onChange={() => toggleProgramme(p.value)}
                            />
                            <span className="font-semibold text-sm group-hover:text-hp-accent transition-colors">{p.label}</span>
                        </label>
                    ))}
                </div>
            </div>

            <div>
                <label className={LABEL_CLASS}>Additional Information</label>
                <input
                    type="text"
                    value={additional}
                    onChange={e => setAdditional(e.target.value)}
                    placeholder="Any additional information"
                    className={INPUT_CLASS}
                />
            </div>

            <button
                type="submit"
                disabled={submitting}
                className="w-full bg-hp-dark text-white py-4 rounded-xl border-brutal shadow-brutal interactive-card uppercase font-black text-sm disabled:opacity-60"
            >
                {submitting ? 'Submitting…' : 'Register Interest'}
            </button>
        </form>
    );
}
