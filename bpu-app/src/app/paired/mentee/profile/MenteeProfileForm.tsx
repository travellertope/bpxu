'use client';

import { useState, useEffect, useRef } from 'react';

interface MenteeProfile {
    first_name?: string;
    last_name?: string;
    phone_number?: string;
    gender?: string;
    country?: string;
    city?: string;
    employment_status?: string;
    linkedin_profile?: string;
    years_of_experience?: string;
    mentorship_availability?: string;
    bp_network?: string;
    industry?: string;
    career_goals?: string;
    skills_to_develop?: string[];
    bio?: string;
}

interface Props {
    initialProfile: MenteeProfile;
}

const INDUSTRIES = [
    'Advertising/Public Relations', 'Aerospace/Aviation', 'Arts & Entertainment',
    'Banking/Mortgage', 'Business Development', 'Clerical/Administrative',
    'Construction/Facilities', 'Consulting', 'Consumer Goods', 'Customer Service',
    'Education/Training', 'Energy', 'Engineering', 'Financial Services',
    'Government/Military', 'Healthcare', 'Hospitality/Travel', 'Human Resources',
    'Insurance', 'Internet/Technology', 'Legal', 'Management/Executive',
    'Manufacturing/Operations', 'Marketing', 'Media/Publishing', 'Non-Profit',
    'Pharmaceutical/Biotech', 'Professional Services', 'Real Estate',
    'Retail/E-Commerce', 'Science/Research', 'Social Services', 'Technology/IT',
    'Telecommunications', 'Transport/Logistics', 'Other',
];

const EMPLOYMENT_STATUSES = [
    'Employed Full-Time',
    'Employed Part-Time',
    'Self-employed',
    'Not employed but looking for work',
    'Student',
    'Freelance/Contract',
    'Career break',
    'Retired',
    'Other',
];

const EXPERIENCE_OPTIONS = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11-15', '16-20', '20+'];

const AVAILABILITY_OPTIONS = [
    'Once a month',
    'Twice a month',
    'Once in 2 months',
    'Weekly',
    'Flexible',
];

const BP_NETWORKS = ['UK', 'Europe', 'Ireland', 'Australia'];

const GENDERS = ['Male', 'Female', 'Non-binary', 'Prefer not to say', 'Other'];

export default function MenteeProfileForm({ initialProfile }: Props) {
    const [firstName, setFirstName] = useState(initialProfile.first_name || '');
    const [lastName, setLastName] = useState(initialProfile.last_name || '');
    const [phone, setPhone] = useState(initialProfile.phone_number || '');
    const [gender, setGender] = useState(initialProfile.gender || '');
    const [country, setCountry] = useState(initialProfile.country || '');
    const [city, setCity] = useState(initialProfile.city || '');
    const [employmentStatus, setEmploymentStatus] = useState(initialProfile.employment_status || '');
    const [linkedin, setLinkedin] = useState(initialProfile.linkedin_profile || '');
    const [yearsExp, setYearsExp] = useState(initialProfile.years_of_experience || '');
    const [mentorshipAvailability, setMentorshipAvailability] = useState(initialProfile.mentorship_availability || '');
    const [bpNetwork, setBpNetwork] = useState(initialProfile.bp_network || '');
    const [industry, setIndustry] = useState(initialProfile.industry || '');
    const [careerGoals, setCareerGoals] = useState(initialProfile.career_goals || '');
    const [skills, setSkills] = useState<string[]>(initialProfile.skills_to_develop || []);
    const [bio, setBio] = useState(initialProfile.bio || '');

    const [skillInput, setSkillInput] = useState('');
    const [skillSuggestions, setSkillSuggestions] = useState<string[]>([]);
    const [allSkills, setAllSkills] = useState<string[]>([]);
    const [showSuggestions, setShowSuggestions] = useState(false);
    const [saving, setSaving] = useState(false);
    const [success, setSuccess] = useState('');
    const [error, setError] = useState('');
    const suggestionsRef = useRef<HTMLDivElement>(null);
    const inputRef = useRef<HTMLInputElement>(null);

    useEffect(() => {
        async function fetchSkills() {
            try {
                const res = await fetch('/api/paired/skills');
                if (res.ok) {
                    const data = await res.json();
                    setAllSkills((data.skills || []).map((s: { name: string }) => s.name));
                }
            } catch { /* */ }
        }
        fetchSkills();
    }, []);

    useEffect(() => {
        if (skillInput.length < 2) { setSkillSuggestions([]); setShowSuggestions(false); return; }
        const q = skillInput.toLowerCase();
        const matches = allSkills.filter(s => s.toLowerCase().includes(q) && !skills.includes(s)).slice(0, 6);
        setSkillSuggestions(matches);
        setShowSuggestions(matches.length > 0);
    }, [skillInput, allSkills, skills]);

    useEffect(() => {
        function handleClickOutside(e: MouseEvent) {
            if (suggestionsRef.current && !suggestionsRef.current.contains(e.target as Node) &&
                inputRef.current && !inputRef.current.contains(e.target as Node)) {
                setShowSuggestions(false);
            }
        }
        document.addEventListener('mousedown', handleClickOutside);
        return () => document.removeEventListener('mousedown', handleClickOutside);
    }, []);

    function addSkill(s: string) {
        const trimmed = s.trim();
        if (trimmed && !skills.includes(trimmed)) setSkills(prev => [...prev, trimmed]);
        setSkillInput('');
        setShowSuggestions(false);
    }

    function removeSkill(s: string) {
        setSkills(prev => prev.filter(x => x !== s));
    }

    async function handleSave() {
        setSaving(true);
        setError('');
        setSuccess('');
        try {
            const res = await fetch('/api/paired/mentee/profile', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    first_name: firstName,
                    last_name: lastName,
                    phone_number: phone,
                    gender,
                    country,
                    city,
                    employment_status: employmentStatus,
                    linkedin_profile: linkedin,
                    years_of_experience: yearsExp,
                    mentorship_availability: mentorshipAvailability,
                    bp_network: bpNetwork,
                    industry,
                    career_goals: careerGoals,
                    skills_to_develop: skills,
                    bio,
                }),
            });
            const data = await res.json();
            if (!res.ok) {
                setError(data.message || data.error || 'Failed to save profile.');
                return;
            }
            setSuccess('Profile saved successfully.');
            setTimeout(() => setSuccess(''), 4000);
        } catch {
            setError('Something went wrong. Please try again.');
        } finally {
            setSaving(false);
        }
    }

    return (
        <div style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
            {error && <div className="alert alert-red">{error}</div>}
            {success && <div className="alert alert-green">{success}</div>}

            {/* Personal Info */}
            <div className="card card-p" style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                <h2 className="text-lg font-bold">Personal Information</h2>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label className="field-label" htmlFor="first-name">First Name</label>
                        <input id="first-name" type="text" className="field-input mt-1" value={firstName} onChange={e => setFirstName(e.target.value)} />
                    </div>
                    <div>
                        <label className="field-label" htmlFor="last-name">Last Name</label>
                        <input id="last-name" type="text" className="field-input mt-1" value={lastName} onChange={e => setLastName(e.target.value)} />
                    </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label className="field-label" htmlFor="phone">Phone Number</label>
                        <input id="phone" type="tel" className="field-input mt-1" value={phone} onChange={e => setPhone(e.target.value)} />
                    </div>
                    <div>
                        <label className="field-label" htmlFor="gender">Gender</label>
                        <select id="gender" className="field-input mt-1" value={gender} onChange={e => setGender(e.target.value)}>
                            <option value="">Select gender</option>
                            {GENDERS.map(g => <option key={g} value={g}>{g}</option>)}
                        </select>
                    </div>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label className="field-label" htmlFor="country">Country</label>
                        <input id="country" type="text" className="field-input mt-1" value={country} onChange={e => setCountry(e.target.value)} placeholder="e.g. United Kingdom" />
                    </div>
                    <div>
                        <label className="field-label" htmlFor="city">City / Town</label>
                        <input id="city" type="text" className="field-input mt-1" value={city} onChange={e => setCity(e.target.value)} placeholder="e.g. London" />
                    </div>
                </div>

                <div>
                    <label className="field-label" htmlFor="bp-network">BP Network</label>
                    <select id="bp-network" className="field-input mt-1" value={bpNetwork} onChange={e => setBpNetwork(e.target.value)}>
                        <option value="">Select network</option>
                        {BP_NETWORKS.map(n => <option key={n} value={n}>{n}</option>)}
                    </select>
                    <p className="text-xs mt-1" style={{ color: 'var(--text-3)' }}>The Black Professionals United regional network you belong to.</p>
                </div>
            </div>

            {/* Career Info */}
            <div className="card card-p" style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                <h2 className="text-lg font-bold">Career Information</h2>

                <div>
                    <label className="field-label" htmlFor="industry">Industry / Field of Expertise</label>
                    <select id="industry" className="field-input mt-1" value={industry} onChange={e => setIndustry(e.target.value)}>
                        <option value="">Select industry</option>
                        {INDUSTRIES.map(i => <option key={i} value={i}>{i}</option>)}
                    </select>
                </div>

                <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label className="field-label" htmlFor="employment">Employment Status</label>
                        <select id="employment" className="field-input mt-1" value={employmentStatus} onChange={e => setEmploymentStatus(e.target.value)}>
                            <option value="">Select status</option>
                            {EMPLOYMENT_STATUSES.map(s => <option key={s} value={s}>{s}</option>)}
                        </select>
                    </div>
                    <div>
                        <label className="field-label" htmlFor="years-exp">Years of Experience</label>
                        <select id="years-exp" className="field-input mt-1" value={yearsExp} onChange={e => setYearsExp(e.target.value)}>
                            <option value="">Select years</option>
                            {EXPERIENCE_OPTIONS.map(y => <option key={y} value={y}>{y}</option>)}
                        </select>
                    </div>
                </div>

                <div>
                    <label className="field-label" htmlFor="linkedin">LinkedIn Profile URL</label>
                    <input id="linkedin" type="url" className="field-input mt-1" value={linkedin} onChange={e => setLinkedin(e.target.value)} placeholder="https://linkedin.com/in/your-profile" />
                </div>
            </div>

            {/* Mentorship Profile */}
            <div className="card card-p" style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                <h2 className="text-lg font-bold">Mentorship Profile</h2>

                <div>
                    <label className="field-label" htmlFor="availability">Preferred Mentorship Availability</label>
                    <select id="availability" className="field-input mt-1" value={mentorshipAvailability} onChange={e => setMentorshipAvailability(e.target.value)}>
                        <option value="">Select availability</option>
                        {AVAILABILITY_OPTIONS.map(a => <option key={a} value={a}>{a}</option>)}
                    </select>
                </div>

                <div>
                    <label className="field-label" htmlFor="bio">About Me</label>
                    <textarea
                        id="bio"
                        className="field-input mt-1"
                        rows={3}
                        value={bio}
                        onChange={e => setBio(e.target.value)}
                        placeholder="A short introduction about yourself..."
                    />
                </div>

                <div>
                    <label className="field-label" htmlFor="career-goals">Career Goals</label>
                    <textarea
                        id="career-goals"
                        className="field-input mt-1"
                        rows={3}
                        value={careerGoals}
                        onChange={e => setCareerGoals(e.target.value)}
                        placeholder="What are you hoping to achieve through mentorship?"
                    />
                </div>

                {/* Skills tags */}
                <div>
                    <label className="field-label">Skills to Develop</label>
                    {skills.length > 0 && (
                        <div className="flex flex-wrap gap-2 mt-2 mb-2">
                            {skills.map(s => (
                                <span key={s} className="badge badge-purple flex items-center gap-1">
                                    {s}
                                    <button
                                        type="button"
                                        onClick={() => removeSkill(s)}
                                        style={{ background: 'none', border: 'none', cursor: 'pointer', padding: '0 2px', color: 'inherit', lineHeight: 1 }}
                                        aria-label={`Remove ${s}`}
                                    >
                                        ×
                                    </button>
                                </span>
                            ))}
                        </div>
                    )}
                    <div style={{ position: 'relative' }}>
                        <input
                            ref={inputRef}
                            type="text"
                            className="field-input mt-1"
                            value={skillInput}
                            onChange={e => setSkillInput(e.target.value)}
                            onKeyDown={e => {
                                if ((e.key === 'Enter' || e.key === ',') && skillInput.trim()) {
                                    e.preventDefault();
                                    addSkill(skillInput);
                                }
                            }}
                            placeholder="Type a skill and press Enter..."
                        />
                        {showSuggestions && (
                            <div
                                ref={suggestionsRef}
                                className="card"
                                style={{
                                    position: 'absolute', top: '100%', left: 0, right: 0,
                                    zIndex: 20, marginTop: 4, overflow: 'hidden',
                                    boxShadow: '0 4px 16px rgba(0,0,0,.1)',
                                }}
                            >
                                {skillSuggestions.map(s => (
                                    <button
                                        key={s}
                                        type="button"
                                        className="w-full text-left px-3 py-2 text-sm hover:bg-surface transition-colors"
                                        style={{ background: 'none', border: 'none', cursor: 'pointer' }}
                                        onMouseDown={e => { e.preventDefault(); addSkill(s); }}
                                    >
                                        {s}
                                    </button>
                                ))}
                            </div>
                        )}
                    </div>
                </div>
            </div>

            <button onClick={handleSave} disabled={saving} className="btn btn-purple">
                {saving ? 'Saving...' : 'Save Profile'}
            </button>

            <p className="text-sm text-center" style={{ color: 'var(--text-3)' }}>
                <a href="/paired/settings/change-password" className="hover:underline" style={{ color: 'var(--purple)' }}>
                    Change password
                </a>
            </p>
        </div>
    );
}
