'use client';

import { useState, useRef } from 'react';

const SELECT_CLASS = 'w-full px-4 py-3 rounded-xl font-medium text-sm border-brutal bg-white focus:outline-none focus:ring-2 focus:ring-hp-accent';
const INPUT_CLASS  = 'w-full px-4 py-3 rounded-xl font-medium text-sm border-brutal bg-white focus:outline-none focus:ring-2 focus:ring-hp-accent';
const LABEL_CLASS  = 'block text-xs font-black uppercase tracking-widest mb-1.5';

export default function NextGenForm() {
    const [name,         setName]         = useState('');
    const [email,        setEmail]        = useState('');
    const [phone,        setPhone]        = useState('');
    const [dob,          setDob]          = useState('');
    const [location,     setLocation]     = useState('');
    const [aspiration,   setAspiration]   = useState('');
    const [disabilities, setDisabilities] = useState('');
    const [school,       setSchool]       = useState('');
    const [level,        setLevel]        = useState('');
    const [area,         setArea]         = useState('');
    const [otherArea,    setOtherArea]    = useState('');
    const [skills,       setSkills]       = useState('');
    const [availability, setAvailability] = useState('');
    const [cvFile,       setCvFile]       = useState<File | null>(null);
    const [submitting,   setSubmitting]   = useState(false);
    const [submitted,    setSubmitted]    = useState(false);
    const [error,        setError]        = useState('');
    const fileRef = useRef<HTMLInputElement>(null);

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setError('');
        setSubmitting(true);
        try {
            const fd = new FormData();
            fd.append('your-name',          name);
            fd.append('your-email',         email);
            fd.append('your-phone',         phone);
            fd.append('date-321',           dob);
            fd.append('your-location',      location);
            fd.append('textarea-aspiration',aspiration);
            fd.append('your-disabilities',  disabilities);
            fd.append('your-school',        school);
            fd.append('current-level',      level);
            fd.append('select-area',        area);
            fd.append('other-area',         otherArea);
            fd.append('your-skills',        skills);
            fd.append('select-jobtype',     availability);
            if (cvFile) fd.append('file-cv', cvFile);

            const res  = await fetch('/api/programmes/nextgen', { method: 'POST', body: fd });
            const data = await res.json();
            if (data.status === 'mail_sent' || data.status === 'success' || res.ok) {
                setSubmitted(true);
            } else {
                setError(data.message || data.invalid_fields?.[0]?.message || 'Something went wrong. Please try again.');
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
                <h3 className="font-black uppercase text-xl tracking-tight">Application Received</h3>
                <p className="text-text-2 max-w-sm mx-auto">
                    Thank you for applying to the #NextGen Apprenticeship Programme. Our team will review your application and be in touch shortly.
                </p>
            </div>
        );
    }

    return (
        <form onSubmit={handleSubmit} className="space-y-5">
            {error && (
                <div className="p-4 rounded-xl border-2 border-red-400 bg-red-50 text-red-700 text-sm font-medium">
                    {error}
                </div>
            )}

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label className={LABEL_CLASS}>Full Name *</label>
                    <input
                        type="text"
                        required
                        value={name}
                        onChange={e => setName(e.target.value)}
                        placeholder="Your full name"
                        className={INPUT_CLASS}
                    />
                </div>
                <div>
                    <label className={LABEL_CLASS}>Email Address *</label>
                    <input
                        type="email"
                        required
                        value={email}
                        onChange={e => setEmail(e.target.value)}
                        placeholder="your@email.com"
                        className={INPUT_CLASS}
                    />
                </div>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label className={LABEL_CLASS}>Phone Number *</label>
                    <input
                        type="text"
                        required
                        value={phone}
                        onChange={e => setPhone(e.target.value)}
                        placeholder="+44 7700 000000"
                        className={INPUT_CLASS}
                    />
                </div>
                <div>
                    <label className={LABEL_CLASS}>Date of Birth *</label>
                    <input
                        type="date"
                        required
                        min="2005-01-01"
                        max="2009-12-31"
                        value={dob}
                        onChange={e => setDob(e.target.value)}
                        className={INPUT_CLASS}
                    />
                    <p className="text-xs text-text-3 mt-1">Must be aged 16–19.</p>
                </div>
            </div>

            <div>
                <label className={LABEL_CLASS}>Location (City / Region) *</label>
                <input
                    type="text"
                    required
                    value={location}
                    onChange={e => setLocation(e.target.value)}
                    placeholder="e.g. London, Manchester"
                    className={INPUT_CLASS}
                />
            </div>

            <div>
                <label className={LABEL_CLASS}>What do you hope to learn from this apprenticeship?</label>
                <input
                    type="text"
                    value={aspiration}
                    onChange={e => setAspiration(e.target.value)}
                    placeholder="I hope to learn..."
                    className={INPUT_CLASS}
                />
            </div>

            <div>
                <label className={LABEL_CLASS}>Any disabilities, health conditions, or additional support needs? (Optional)</label>
                <input
                    type="text"
                    value={disabilities}
                    onChange={e => setDisabilities(e.target.value)}
                    placeholder="Please share anything that would help us support you"
                    className={INPUT_CLASS}
                />
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                <div>
                    <label className={LABEL_CLASS}>Current School / College</label>
                    <input
                        type="text"
                        value={school}
                        onChange={e => setSchool(e.target.value)}
                        placeholder="School or college name"
                        className={INPUT_CLASS}
                    />
                </div>
                <div>
                    <label className={LABEL_CLASS}>Current Level of Study</label>
                    <input
                        type="text"
                        value={level}
                        onChange={e => setLevel(e.target.value)}
                        placeholder="GCSE, A-level, College, etc."
                        className={INPUT_CLASS}
                    />
                </div>
            </div>

            <div>
                <label className={LABEL_CLASS}>Subjects / Areas of Interest</label>
                <select value={area} onChange={e => setArea(e.target.value)} className={SELECT_CLASS}>
                    <option value="Select One">Select One</option>
                    <option value="Business">Business</option>
                    <option value="IT">IT</option>
                    <option value="Engineering">Engineering</option>
                    <option value="Marketing">Marketing</option>
                    <option value="Healthcare">Healthcare</option>
                    <option value="Others (Input Below)">Others (Input Below)</option>
                </select>
                {area === 'Others (Input Below)' && (
                    <input
                        type="text"
                        value={otherArea}
                        onChange={e => setOtherArea(e.target.value)}
                        placeholder="Please specify your area of interest"
                        className={`${INPUT_CLASS} mt-2`}
                    />
                )}
            </div>

            <div>
                <label className={LABEL_CLASS}>Key Skills *</label>
                <input
                    type="text"
                    required
                    value={skills}
                    onChange={e => setSkills(e.target.value)}
                    placeholder="Communication, Time Management, Leadership..."
                    className={INPUT_CLASS}
                />
                <p className="text-xs text-text-3 mt-1">Separate skills with commas.</p>
            </div>

            <div>
                <label className={LABEL_CLASS}>Availability *</label>
                <select required value={availability} onChange={e => setAvailability(e.target.value)} className={SELECT_CLASS}>
                    <option value="">Select One</option>
                    <option value="Full-time">Full-time</option>
                    <option value="Part-time">Part-time</option>
                    <option value="Summer only">Summer only</option>
                    <option value="After school">After school</option>
                </select>
            </div>

            <div>
                <label className={LABEL_CLASS}>CV Upload (Optional)</label>
                <div
                    className="w-full px-4 py-6 rounded-xl border-brutal bg-white text-center cursor-pointer hover:shadow-brutal transition-shadow"
                    onClick={() => fileRef.current?.click()}
                    onDragOver={e => e.preventDefault()}
                    onDrop={e => {
                        e.preventDefault();
                        const f = e.dataTransfer.files[0];
                        if (f) setCvFile(f);
                    }}
                >
                    <input
                        ref={fileRef}
                        type="file"
                        accept=".pdf,.docx"
                        className="hidden"
                        onChange={e => setCvFile(e.target.files?.[0] || null)}
                    />
                    {cvFile ? (
                        <div className="flex items-center justify-center gap-2">
                            <span className="font-black text-sm">📄 {cvFile.name}</span>
                            <button
                                type="button"
                                onClick={ev => { ev.stopPropagation(); setCvFile(null); if (fileRef.current) fileRef.current.value = ''; }}
                                className="text-red-500 text-xs font-bold hover:underline"
                            >
                                Remove
                            </button>
                        </div>
                    ) : (
                        <>
                            <p className="font-black text-sm mb-1">Click to upload or drag &amp; drop</p>
                            <p className="text-xs text-text-3">PDF, DOCX — optional</p>
                        </>
                    )}
                </div>
            </div>

            <button
                type="submit"
                disabled={submitting}
                className="w-full bg-hp-dark text-white py-4 rounded-xl border-brutal shadow-brutal interactive-card uppercase font-black text-sm disabled:opacity-60"
            >
                {submitting ? 'Submitting…' : 'Submit Application'}
            </button>
        </form>
    );
}
