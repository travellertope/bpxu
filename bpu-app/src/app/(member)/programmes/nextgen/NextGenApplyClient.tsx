'use client';

import { useState, useRef } from 'react';
import type { BPUUser } from '@/lib/auth';

const SELECT_CLASS = 'w-full px-4 py-3 rounded-xl font-medium text-sm border-2 border-border bg-white focus:outline-none focus:ring-2 focus:ring-brand';
const INPUT_CLASS  = 'w-full px-4 py-3 rounded-xl font-medium text-sm border-2 border-border bg-white focus:outline-none focus:ring-2 focus:ring-brand';
const LABEL_CLASS  = 'block text-xs font-bold uppercase tracking-wider text-text-2 mb-1.5';

interface Props { user: BPUUser; }

export default function NextGenApplyClient({ user }: Props) {
    const p = user.profile;
    const fullName = [p.first_name, p.last_name].filter(Boolean).join(' ');
    const profileCity = p.location_city || p.where_in_the_uk || '';

    const [name,         setName]         = useState(fullName);
    const [email,        setEmail]        = useState(user.email || '');
    const [phone,        setPhone]        = useState(p.phone_number || '');
    const [dob,          setDob]          = useState(p.birthday || '');
    const [location,     setLocation]     = useState(profileCity);
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
            <div className="max-w-lg mx-auto py-24 px-4 text-center">
                <div className="w-16 h-16 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-2xl mx-auto mb-6">✓</div>
                <h2 className="text-2xl font-bold mb-3">Application Received</h2>
                <p className="text-text-2">
                    Thank you for applying to the #NextGen Apprenticeship Programme. Our team will review your application and be in touch shortly.
                </p>
                <a href="/dashboard" className="btn btn-primary mt-8 inline-block">Back to Dashboard</a>
            </div>
        );
    }

    return (
        <div className="max-w-2xl mx-auto px-4 py-8">
            <div className="mb-8">
                <div className="flex items-center gap-3 mb-2">
                    <a href="/membership/nextgen" className="text-xs text-text-3 hover:text-text">← Back to programme info</a>
                </div>
                <h1 className="text-2xl font-bold">#NextGen Apprenticeship Programme</h1>
                <p className="text-text-2 mt-1">Complete your application below. Fields pre-filled from your profile can be updated.</p>
            </div>

            <div className="card card-p">
                <form onSubmit={handleSubmit} className="space-y-5">
                    {error && (
                        <div className="p-4 rounded-lg border border-red-300 bg-red-50 text-red-700 text-sm">
                            {error}
                        </div>
                    )}

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label className={LABEL_CLASS}>Full Name *</label>
                            <input type="text" required value={name} onChange={e => setName(e.target.value)}
                                placeholder="Your full name" className={INPUT_CLASS} />
                        </div>
                        <div>
                            <label className={LABEL_CLASS}>Email Address *</label>
                            <input type="email" required value={email} onChange={e => setEmail(e.target.value)}
                                placeholder="your@email.com" className={INPUT_CLASS} />
                        </div>
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label className={LABEL_CLASS}>Phone Number *</label>
                            <input type="text" required value={phone} onChange={e => setPhone(e.target.value)}
                                placeholder="+44 7700 000000" className={INPUT_CLASS} />
                        </div>
                        <div>
                            <label className={LABEL_CLASS}>Date of Birth *</label>
                            <input type="date" required min="2005-01-01" max="2009-12-31" value={dob} onChange={e => setDob(e.target.value)} className={INPUT_CLASS} />
                            <p className="text-xs text-text-3 mt-1">Must be aged 16–19.</p>
                        </div>
                    </div>

                    <div>
                        <label className={LABEL_CLASS}>Location (City / Region) *</label>
                        <input type="text" required value={location} onChange={e => setLocation(e.target.value)}
                            placeholder="e.g. London, Manchester" className={INPUT_CLASS} />
                    </div>

                    <div>
                        <label className={LABEL_CLASS}>What do you hope to learn from this apprenticeship?</label>
                        <input type="text" value={aspiration} onChange={e => setAspiration(e.target.value)}
                            placeholder="I hope to learn..." className={INPUT_CLASS} />
                    </div>

                    <div>
                        <label className={LABEL_CLASS}>Any disabilities, health conditions, or additional support needs?</label>
                        <input type="text" value={disabilities} onChange={e => setDisabilities(e.target.value)}
                            placeholder="Please share anything that would help us support you" className={INPUT_CLASS} />
                    </div>

                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label className={LABEL_CLASS}>Current School / College</label>
                            <input type="text" value={school} onChange={e => setSchool(e.target.value)}
                                placeholder="School or college name" className={INPUT_CLASS} />
                        </div>
                        <div>
                            <label className={LABEL_CLASS}>Current Level of Study</label>
                            <input type="text" value={level} onChange={e => setLevel(e.target.value)}
                                placeholder="GCSE, A-level, College, etc." className={INPUT_CLASS} />
                        </div>
                    </div>

                    <div>
                        <label className={LABEL_CLASS}>Subjects / Areas of Interest</label>
                        <select value={area} onChange={e => setArea(e.target.value)} className={SELECT_CLASS}>
                            <option value="">Select One</option>
                            <option value="Business">Business</option>
                            <option value="IT">IT</option>
                            <option value="Engineering">Engineering</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Healthcare">Healthcare</option>
                            <option value="Others (Input Below)">Others (Input Below)</option>
                        </select>
                        {area === 'Others (Input Below)' && (
                            <input type="text" value={otherArea} onChange={e => setOtherArea(e.target.value)}
                                placeholder="Please specify your area of interest" className={`${INPUT_CLASS} mt-2`} />
                        )}
                    </div>

                    <div>
                        <label className={LABEL_CLASS}>Key Skills *</label>
                        <input type="text" required value={skills} onChange={e => setSkills(e.target.value)}
                            placeholder="Communication, Time Management, Leadership…" className={INPUT_CLASS} />
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
                            className="w-full px-4 py-6 rounded-xl border-2 border-dashed border-border bg-white text-center cursor-pointer hover:border-brand transition-colors"
                            onClick={() => fileRef.current?.click()}
                            onDragOver={e => e.preventDefault()}
                            onDrop={e => { e.preventDefault(); const f = e.dataTransfer.files[0]; if (f) setCvFile(f); }}
                        >
                            <input ref={fileRef} type="file" accept=".pdf,.docx" className="hidden"
                                onChange={e => setCvFile(e.target.files?.[0] || null)} />
                            {cvFile ? (
                                <div className="flex items-center justify-center gap-2">
                                    <span className="font-semibold text-sm">📄 {cvFile.name}</span>
                                    <button type="button" onClick={ev => { ev.stopPropagation(); setCvFile(null); if (fileRef.current) fileRef.current.value = ''; }}
                                        className="text-red-500 text-xs font-bold hover:underline">Remove</button>
                                </div>
                            ) : (
                                <>
                                    <p className="font-semibold text-sm mb-1">Click to upload or drag &amp; drop</p>
                                    <p className="text-xs text-text-3">PDF, DOCX — optional</p>
                                </>
                            )}
                        </div>
                    </div>

                    <button type="submit" disabled={submitting}
                        className="btn btn-primary w-full py-3 disabled:opacity-60">
                        {submitting ? 'Submitting…' : 'Submit Application'}
                    </button>
                </form>
            </div>
        </div>
    );
}
