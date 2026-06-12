'use client';

import { useState, useEffect, useRef, useCallback } from 'react';

/* ------------------------------------------------------------------ */
/*  Types                                                              */
/* ------------------------------------------------------------------ */

interface Profile {
    first_name?: string;
    last_name?: string;
    phone_number?: string;
    residence?: string;
    gender?: string;
    bp_network?: string;
    employment_status?: string;
    industry?: string;
    industryfield_of_expertise?: string;
    current_role?: string;
    company?: string;
    years_of_experience?: string;
    skills_separate?: string;
    user_bio?: string;
    mentorship_availability?: string;
    mentorship_requirements?: string;
    mentees_at_once?: string;
    linkedin_profile?: string;
    facebook_profile?: string;
    instagram_profile?: string;
    x_profile?: string;
    level_of_education?: string;
}

interface TimeBlock {
    day: string;
    start: string;
    end: string;
}

interface Availability {
    schedule: TimeBlock[];
    holidays: string[];
    timezone: string;
}

interface Experience {
    id: number;
    title: string;
    company: string;
    start_date: string;
    end_date: string;
    is_current: boolean;
    description: string;
}

interface Education {
    id: number;
    institution: string;
    degree: string;
    start_year: string;
    end_year: string;
}

interface Props {
    profile: Profile;
    displayName: string;
    email: string;
    avatarUrl: string;
    availability: Availability;
    experiences: Experience[];
    education: Education[];
    skillsOptions: Record<string, string[]>;
}

/* ------------------------------------------------------------------ */
/*  Constants                                                          */
/* ------------------------------------------------------------------ */

const SECTIONS = [
    { key: 'personal', label: 'Personal Info' },
    { key: 'professional', label: 'Professional' },
    { key: 'mentorship', label: 'Mentorship' },
    { key: 'availability', label: 'Availability' },
    { key: 'experience', label: 'Experience' },
    { key: 'education', label: 'Education' },
    { key: 'social', label: 'Social Links' },
] as const;

const GENDER_OPTIONS = ['Male', 'Female', 'Non-binary', 'Prefer not to say'];

const INDUSTRY_OPTIONS = [
    'Technology', 'Banking & Financial Services', 'Healthcare', 'Legal',
    'Education', 'Marketing & Communications', 'Engineering', 'Design & Creative',
    'Media & Entertainment', 'Public Sector', 'Property & Construction',
    'Consulting', 'Retail', 'Energy', 'Telecoms', 'Other',
];

const EMPLOYMENT_OPTIONS = [
    'Full-time', 'Part-time', 'Self-employed', 'Freelance',
    'Contractor', 'Student', 'Unemployed', 'Retired',
];

const EDUCATION_OPTIONS = [
    'GCSE', 'A-Level', 'HND/HNC', "Bachelor's Degree",
    "Master's Degree", 'PhD/Doctorate', 'Professional Qualification', 'Other',
];

const AVAILABILITY_OPTIONS = [
    'Once a week', 'Once a fortnight', 'Once a month', 'Once every 2 months',
];

const BP_NETWORK_OPTIONS = [
    'Black Professionals UK', 'Black Professionals Europe',
    'Black Professionals Ireland', 'Black Professionals Australia',
];

const DAYS = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

const HOURS = Array.from({ length: 25 }, (_, i) => {
    const h = String(Math.floor(i)).padStart(2, '0');
    const m = '00';
    return `${h}:${m}`;
});

const TIMEZONES = [
    'Europe/London', 'Europe/Dublin', 'Europe/Paris', 'Europe/Berlin',
    'Europe/Amsterdam', 'Europe/Brussels', 'Europe/Zurich', 'Europe/Stockholm',
    'Africa/Lagos', 'Africa/Nairobi', 'Africa/Johannesburg', 'Africa/Accra',
    'America/New_York', 'America/Chicago', 'America/Denver', 'America/Los_Angeles',
    'America/Toronto', 'Asia/Dubai', 'Asia/Kolkata', 'Asia/Singapore',
    'Australia/Sydney', 'Australia/Melbourne', 'Pacific/Auckland',
];

const MONTHS = [
    'January', 'February', 'March', 'April', 'May', 'June',
    'July', 'August', 'September', 'October', 'November', 'December',
];

/* ------------------------------------------------------------------ */
/*  Toast component                                                    */
/* ------------------------------------------------------------------ */

function Toast({ message, type, onClose }: { message: string; type: 'success' | 'error'; onClose: () => void }) {
    useEffect(() => {
        const t = setTimeout(onClose, 4000);
        return () => clearTimeout(t);
    }, [onClose]);

    return (
        <div
            className={type === 'success' ? 'alert alert-green' : 'alert alert-red'}
            style={{
                position: 'fixed', bottom: 24, right: 24, zIndex: 9999,
                maxWidth: 400, boxShadow: '0 4px 24px rgba(0,0,0,.15)',
            }}
        >
            <div className="flex items-center justify-between gap-3">
                <span className="text-sm">{message}</span>
                <button onClick={onClose} className="btn btn-ghost btn-sm" style={{ padding: '2px 8px' }}>
                    &times;
                </button>
            </div>
        </div>
    );
}

/* ------------------------------------------------------------------ */
/*  Main form component                                                */
/* ------------------------------------------------------------------ */

export default function MentorSettingsForm({
    profile: initialProfile,
    displayName,
    email,
    avatarUrl,
    availability: initialAvailability,
    experiences: initialExperiences,
    education: initialEducation,
    skillsOptions,
}: Props) {
    const [activeSection, setActiveSection] = useState<string>('personal');
    const [toast, setToast] = useState<{ message: string; type: 'success' | 'error' } | null>(null);

    /* --- Profile state --- */
    const [profile, setProfile] = useState<Profile>(initialProfile);
    const updateProfile = (key: keyof Profile, value: string) => {
        setProfile(prev => ({ ...prev, [key]: value }));
    };

    /* --- Availability state --- */
    const [schedule, setSchedule] = useState<TimeBlock[]>(initialAvailability.schedule || []);
    const [holidays, setHolidays] = useState<string[]>(initialAvailability.holidays || []);
    const [timezone, setTimezone] = useState(initialAvailability.timezone || 'Europe/London');

    /* --- Experience state --- */
    const [experiences, setExperiences] = useState<Experience[]>(initialExperiences);
    const [editingExp, setEditingExp] = useState<number | null>(null);
    const [newExp, setNewExp] = useState(false);
    const emptyExp: Omit<Experience, 'id'> = { title: '', company: '', start_date: '', end_date: '', is_current: false, description: '' };
    const [expForm, setExpForm] = useState<Omit<Experience, 'id'>>(emptyExp);

    /* --- Education state --- */
    const [educationList, setEducationList] = useState<Education[]>(initialEducation);
    const [editingEdu, setEditingEdu] = useState<number | null>(null);
    const [newEdu, setNewEdu] = useState(false);
    const emptyEdu: Omit<Education, 'id'> = { institution: '', degree: '', start_year: '', end_year: '' };
    const [eduForm, setEduForm] = useState<Omit<Education, 'id'>>(emptyEdu);

    /* --- Photo state --- */
    const [photoPreview, setPhotoPreview] = useState<string>(avatarUrl);
    const [photoUploading, setPhotoUploading] = useState(false);
    const fileInputRef = useRef<HTMLInputElement>(null);

    async function handlePhotoUpload(e: React.ChangeEvent<HTMLInputElement>) {
        const file = e.target.files?.[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            setToast({ message: 'Please select an image file.', type: 'error' });
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            setToast({ message: 'Image must be under 5MB.', type: 'error' });
            return;
        }

        setPhotoUploading(true);
        try {
            const reader = new FileReader();
            reader.onload = () => setPhotoPreview(reader.result as string);
            reader.readAsDataURL(file);

            const formData = new FormData();
            formData.append('photo', file);

            const res = await fetch('/api/paired/mentor/photo', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ photo_url: URL.createObjectURL(file) }),
            });

            if (!res.ok) throw new Error('Upload failed');
            const data = await res.json();
            if (data.photo_url) setPhotoPreview(data.photo_url);
            setToast({ message: 'Photo updated successfully.', type: 'success' });
        } catch {
            setToast({ message: 'Failed to upload photo.', type: 'error' });
        } finally {
            setPhotoUploading(false);
        }
    }

    /* --- Skills state --- */
    const [skillsInput, setSkillsInput] = useState('');
    const [skillTags, setSkillTags] = useState<string[]>(
        (initialProfile.skills_separate || '').split(',').map(s => s.trim()).filter(Boolean)
    );
    const [showSkillDropdown, setShowSkillDropdown] = useState(false);
    const skillRef = useRef<HTMLDivElement>(null);

    /* --- Holiday input state --- */
    const [holidayInput, setHolidayInput] = useState('');

    /* --- Saving state --- */
    const [saving, setSaving] = useState(false);

    /* Close skill dropdown on outside click */
    useEffect(() => {
        function handleClick(e: MouseEvent) {
            if (skillRef.current && !skillRef.current.contains(e.target as Node)) {
                setShowSkillDropdown(false);
            }
        }
        document.addEventListener('mousedown', handleClick);
        return () => document.removeEventListener('mousedown', handleClick);
    }, []);

    /* --- Toast helper --- */
    const showToast = useCallback((message: string, type: 'success' | 'error') => {
        setToast({ message, type });
    }, []);

    const clearToast = useCallback(() => setToast(null), []);

    /* --- API helpers --- */
    async function apiPut(path: string, body: unknown) {
        const res = await fetch(path, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.error || data.message || 'Request failed');
        return data;
    }

    async function apiPost(path: string, body: unknown) {
        const res = await fetch(path, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(body),
        });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.error || data.message || 'Request failed');
        return data;
    }

    async function apiDelete(path: string) {
        const res = await fetch(path, { method: 'DELETE' });
        const data = await res.json().catch(() => ({}));
        if (!res.ok) throw new Error(data.error || data.message || 'Request failed');
        return data;
    }

    /* ------------------------------------------------------------------ */
    /*  Save handlers                                                      */
    /* ------------------------------------------------------------------ */

    async function savePersonal() {
        setSaving(true);
        try {
            await apiPut('/api/paired/mentor/profile', {
                first_name: profile.first_name,
                last_name: profile.last_name,
                phone_number: profile.phone_number,
                gender: profile.gender,
                residence: profile.residence,
                user_bio: profile.user_bio,
            });
            showToast('Personal information saved.', 'success');
        } catch (err: unknown) {
            showToast(err instanceof Error ? err.message : 'Failed to save.', 'error');
        } finally {
            setSaving(false);
        }
    }

    async function saveProfessional() {
        setSaving(true);
        try {
            await apiPut('/api/paired/mentor/profile', {
                industry: profile.industry,
                industryfield_of_expertise: profile.industryfield_of_expertise,
                current_role: profile.current_role,
                company: profile.company,
                employment_status: profile.employment_status,
                years_of_experience: profile.years_of_experience,
                level_of_education: profile.level_of_education,
                skills_separate: skillTags.join(', '),
            });
            showToast('Professional details saved.', 'success');
        } catch (err: unknown) {
            showToast(err instanceof Error ? err.message : 'Failed to save.', 'error');
        } finally {
            setSaving(false);
        }
    }

    async function saveMentorship() {
        setSaving(true);
        try {
            await apiPut('/api/paired/mentor/profile', {
                mentorship_availability: profile.mentorship_availability,
                mentees_at_once: profile.mentees_at_once,
                mentorship_requirements: profile.mentorship_requirements,
                bp_network: profile.bp_network,
            });
            showToast('Mentorship preferences saved.', 'success');
        } catch (err: unknown) {
            showToast(err instanceof Error ? err.message : 'Failed to save.', 'error');
        } finally {
            setSaving(false);
        }
    }

    async function saveAvailability() {
        setSaving(true);
        try {
            await apiPut('/api/paired/mentor/availability', { schedule, holidays, timezone });
            showToast('Availability updated.', 'success');
        } catch (err: unknown) {
            showToast(err instanceof Error ? err.message : 'Failed to save.', 'error');
        } finally {
            setSaving(false);
        }
    }

    async function saveSocial() {
        setSaving(true);
        try {
            await apiPut('/api/paired/mentor/profile', {
                linkedin_profile: profile.linkedin_profile,
                facebook_profile: profile.facebook_profile,
                instagram_profile: profile.instagram_profile,
                x_profile: profile.x_profile,
            });
            showToast('Social links saved.', 'success');
        } catch (err: unknown) {
            showToast(err instanceof Error ? err.message : 'Failed to save.', 'error');
        } finally {
            setSaving(false);
        }
    }

    /* --- Experience CRUD --- */
    async function saveExperience(isNew: boolean) {
        setSaving(true);
        try {
            if (isNew) {
                const data = await apiPost('/api/paired/mentor/experiences', expForm);
                setExperiences(prev => [...prev, data.experience || { ...expForm, id: Date.now() }]);
                setNewExp(false);
            } else if (editingExp !== null) {
                await apiPut(`/api/paired/mentor/experiences/${editingExp}`, expForm);
                setExperiences(prev => prev.map(e => e.id === editingExp ? { ...e, ...expForm } : e));
                setEditingExp(null);
            }
            setExpForm(emptyExp);
            showToast('Experience saved.', 'success');
        } catch (err: unknown) {
            showToast(err instanceof Error ? err.message : 'Failed to save experience.', 'error');
        } finally {
            setSaving(false);
        }
    }

    async function deleteExperience(id: number) {
        if (!confirm('Delete this experience?')) return;
        try {
            await apiDelete(`/api/paired/mentor/experiences/${id}`);
            setExperiences(prev => prev.filter(e => e.id !== id));
            showToast('Experience deleted.', 'success');
        } catch (err: unknown) {
            showToast(err instanceof Error ? err.message : 'Failed to delete.', 'error');
        }
    }

    /* --- Education CRUD --- */
    async function saveEducation(isNew: boolean) {
        setSaving(true);
        try {
            if (isNew) {
                const data = await apiPost('/api/paired/mentor/education', eduForm);
                setEducationList(prev => [...prev, data.education || { ...eduForm, id: Date.now() }]);
                setNewEdu(false);
            } else if (editingEdu !== null) {
                await apiPut(`/api/paired/mentor/education/${editingEdu}`, eduForm);
                setEducationList(prev => prev.map(e => e.id === editingEdu ? { ...e, ...eduForm } : e));
                setEditingEdu(null);
            }
            setEduForm(emptyEdu);
            showToast('Education saved.', 'success');
        } catch (err: unknown) {
            showToast(err instanceof Error ? err.message : 'Failed to save education.', 'error');
        } finally {
            setSaving(false);
        }
    }

    async function deleteEducation(id: number) {
        if (!confirm('Delete this education entry?')) return;
        try {
            await apiDelete(`/api/paired/mentor/education/${id}`);
            setEducationList(prev => prev.filter(e => e.id !== id));
            showToast('Education deleted.', 'success');
        } catch (err: unknown) {
            showToast(err instanceof Error ? err.message : 'Failed to delete.', 'error');
        }
    }

    /* --- Skills helpers --- */
    const filteredSkills = Object.entries(skillsOptions).reduce<Record<string, string[]>>((acc, [cat, items]) => {
        const filtered = items.filter(
            s => s.toLowerCase().includes(skillsInput.toLowerCase()) && !skillTags.includes(s)
        );
        if (filtered.length > 0) acc[cat] = filtered;
        return acc;
    }, {});

    function addSkill(skill: string) {
        const trimmed = skill.trim();
        if (trimmed && !skillTags.includes(trimmed)) {
            setSkillTags(prev => [...prev, trimmed]);
        }
        setSkillsInput('');
        setShowSkillDropdown(false);
    }

    function removeSkill(skill: string) {
        setSkillTags(prev => prev.filter(s => s !== skill));
    }

    /* --- Schedule helpers --- */
    function toggleDay(day: string) {
        const hasDay = schedule.some(s => s.day === day);
        if (hasDay) {
            setSchedule(prev => prev.filter(s => s.day !== day));
        } else {
            setSchedule(prev => [...prev, { day, start: '09:00', end: '17:00' }]);
        }
    }

    function addTimeBlock(day: string) {
        setSchedule(prev => [...prev, { day, start: '09:00', end: '17:00' }]);
    }

    function updateTimeBlock(day: string, index: number, field: 'start' | 'end', value: string) {
        const dayBlocks = schedule.filter(s => s.day === day);
        const otherBlocks = schedule.filter(s => s.day !== day);
        dayBlocks[index] = { ...dayBlocks[index], [field]: value };
        setSchedule([...otherBlocks, ...dayBlocks]);
    }

    function removeTimeBlock(day: string, index: number) {
        const dayBlocks = schedule.filter(s => s.day === day);
        dayBlocks.splice(index, 1);
        const otherBlocks = schedule.filter(s => s.day !== day);
        setSchedule([...otherBlocks, ...dayBlocks]);
    }

    function addHoliday(date: string) {
        if (date && !holidays.includes(date)) {
            setHolidays(prev => [...prev, date].sort());
        }
    }

    function removeHoliday(date: string) {
        setHolidays(prev => prev.filter(d => d !== date));
    }

    /* ------------------------------------------------------------------ */
    /*  Section renderers                                                  */
    /* ------------------------------------------------------------------ */

    function renderPersonal() {
        return (
            <div className="card card-p">
                <h2 className="text-xl font-bold mb-1">Personal Information</h2>
                <p className="text-text-2 text-sm mb-6">Basic details about you.</p>

                {/* Profile photo + identity */}
                <div className="flex items-center gap-4 mb-6" style={{ padding: '16px', background: 'var(--surface)', borderRadius: 8 }}>
                    <div className="relative shrink-0">
                        {photoPreview ? (
                            <img src={photoPreview} alt="" style={{ width: 64, height: 64, borderRadius: '50%', objectFit: 'cover' }} />
                        ) : (
                            <div style={{ width: 64, height: 64, borderRadius: '50%', background: 'var(--purple-bg)', color: 'var(--purple)', display: 'flex', alignItems: 'center', justifyContent: 'center', fontSize: '1.5rem', fontWeight: 700 }}>
                                {displayName?.[0] || '?'}
                            </div>
                        )}
                        <button
                            type="button"
                            onClick={() => fileInputRef.current?.click()}
                            disabled={photoUploading}
                            style={{
                                position: 'absolute', bottom: -2, right: -2,
                                width: 24, height: 24, borderRadius: '50%',
                                background: 'var(--purple)', color: '#fff',
                                display: 'flex', alignItems: 'center', justifyContent: 'center',
                                border: '2px solid var(--bg)', cursor: 'pointer', fontSize: 12,
                            }}
                            title="Change photo"
                        >
                            {photoUploading ? '...' : '✎'}
                        </button>
                        <input
                            ref={fileInputRef}
                            type="file"
                            accept="image/*"
                            onChange={handlePhotoUpload}
                            style={{ display: 'none' }}
                        />
                    </div>
                    <div>
                        <p className="font-semibold">{displayName}</p>
                        <p className="text-sm text-text-2">{email}</p>
                        <button
                            type="button"
                            className="text-xs mt-1"
                            style={{ color: 'var(--purple)', cursor: 'pointer', background: 'none', border: 'none', padding: 0 }}
                            onClick={() => fileInputRef.current?.click()}
                            disabled={photoUploading}
                        >
                            {photoUploading ? 'Uploading...' : 'Change profile photo'}
                        </button>
                    </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label className="field-label">First name</label>
                        <input
                            className="field-input w-full"
                            value={profile.first_name || ''}
                            onChange={e => updateProfile('first_name', e.target.value)}
                        />
                    </div>
                    <div>
                        <label className="field-label">Last name</label>
                        <input
                            className="field-input w-full"
                            value={profile.last_name || ''}
                            onChange={e => updateProfile('last_name', e.target.value)}
                        />
                    </div>
                    <div>
                        <label className="field-label">Phone number</label>
                        <input
                            className="field-input w-full"
                            type="tel"
                            value={profile.phone_number || ''}
                            onChange={e => updateProfile('phone_number', e.target.value)}
                        />
                    </div>
                    <div>
                        <label className="field-label">Gender</label>
                        <select className="field-input w-full" value={profile.gender || ''} onChange={e => updateProfile('gender', e.target.value)}>
                            <option value="">Select...</option>
                            {GENDER_OPTIONS.map(g => <option key={g} value={g}>{g}</option>)}
                        </select>
                    </div>
                    <div className="md:col-span-2">
                        <label className="field-label">City / Country of residence</label>
                        <input
                            className="field-input w-full"
                            value={profile.residence || ''}
                            onChange={e => updateProfile('residence', e.target.value)}
                        />
                    </div>
                </div>

                <div className="mb-6">
                    <label className="field-label">Bio</label>
                    <textarea
                        className="field-input field-textarea w-full"
                        rows={5}
                        placeholder="Tell mentees a bit about yourself..."
                        value={profile.user_bio || ''}
                        onChange={e => updateProfile('user_bio', e.target.value)}
                    />
                </div>

                <div className="flex justify-end">
                    <button className="btn btn-purple" disabled={saving} onClick={savePersonal}>
                        {saving ? 'Saving...' : 'Save Personal Info'}
                    </button>
                </div>
            </div>
        );
    }

    function renderProfessional() {
        return (
            <div className="card card-p">
                <h2 className="text-xl font-bold mb-1">Professional Details</h2>
                <p className="text-text-2 text-sm mb-6">Your career background and expertise.</p>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label className="field-label">Industry</label>
                        <select className="field-input w-full" value={profile.industry || ''} onChange={e => updateProfile('industry', e.target.value)}>
                            <option value="">Select industry...</option>
                            {INDUSTRY_OPTIONS.map(i => <option key={i} value={i}>{i}</option>)}
                        </select>
                    </div>
                    <div>
                        <label className="field-label">Field of expertise</label>
                        <input
                            className="field-input w-full"
                            value={profile.industryfield_of_expertise || ''}
                            onChange={e => updateProfile('industryfield_of_expertise', e.target.value)}
                        />
                    </div>
                    <div>
                        <label className="field-label">Current role</label>
                        <input
                            className="field-input w-full"
                            value={profile.current_role || ''}
                            onChange={e => updateProfile('current_role', e.target.value)}
                        />
                    </div>
                    <div>
                        <label className="field-label">Company</label>
                        <input
                            className="field-input w-full"
                            value={profile.company || ''}
                            onChange={e => updateProfile('company', e.target.value)}
                        />
                    </div>
                    <div>
                        <label className="field-label">Employment status</label>
                        <select className="field-input w-full" value={profile.employment_status || ''} onChange={e => updateProfile('employment_status', e.target.value)}>
                            <option value="">Select...</option>
                            {EMPLOYMENT_OPTIONS.map(s => <option key={s} value={s}>{s}</option>)}
                        </select>
                    </div>
                    <div>
                        <label className="field-label">Years of experience</label>
                        <input
                            className="field-input w-full"
                            type="number"
                            min="0"
                            max="60"
                            value={profile.years_of_experience || ''}
                            onChange={e => updateProfile('years_of_experience', e.target.value)}
                        />
                    </div>
                    <div className="md:col-span-2">
                        <label className="field-label">Level of education</label>
                        <select className="field-input w-full" value={profile.level_of_education || ''} onChange={e => updateProfile('level_of_education', e.target.value)}>
                            <option value="">Select...</option>
                            {EDUCATION_OPTIONS.map(e => <option key={e} value={e}>{e}</option>)}
                        </select>
                    </div>
                </div>

                {/* Skills tag input */}
                <div className="mb-6" ref={skillRef}>
                    <label className="field-label">Skills</label>
                    <div
                        className="field-input w-full"
                        style={{ display: 'flex', flexWrap: 'wrap', gap: 6, padding: '8px 12px', minHeight: 44, alignItems: 'center', cursor: 'text' }}
                        onClick={() => {
                            const input = skillRef.current?.querySelector('input');
                            input?.focus();
                        }}
                    >
                        {skillTags.map(tag => (
                            <span key={tag} className="badge badge-purple" style={{ display: 'inline-flex', alignItems: 'center', gap: 4 }}>
                                {tag}
                                <button
                                    type="button"
                                    onClick={(e) => { e.stopPropagation(); removeSkill(tag); }}
                                    style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0, fontSize: 14, lineHeight: 1, color: 'inherit', opacity: 0.7 }}
                                >
                                    &times;
                                </button>
                            </span>
                        ))}
                        <input
                            type="text"
                            value={skillsInput}
                            onChange={e => { setSkillsInput(e.target.value); setShowSkillDropdown(true); }}
                            onFocus={() => setShowSkillDropdown(true)}
                            onKeyDown={e => {
                                if (e.key === 'Enter' && skillsInput.trim()) {
                                    e.preventDefault();
                                    addSkill(skillsInput);
                                }
                                if (e.key === 'Backspace' && !skillsInput && skillTags.length > 0) {
                                    removeSkill(skillTags[skillTags.length - 1]);
                                }
                            }}
                            placeholder={skillTags.length === 0 ? 'Type to search skills...' : ''}
                            style={{ border: 'none', outline: 'none', flex: 1, minWidth: 120, background: 'transparent', fontSize: 'inherit' }}
                        />
                    </div>

                    {/* Skills dropdown */}
                    {showSkillDropdown && (skillsInput || Object.keys(filteredSkills).length > 0) && (
                        <div
                            className="card"
                            style={{
                                position: 'absolute', zIndex: 50, maxHeight: 240, overflowY: 'auto',
                                width: '100%', maxWidth: 500, marginTop: 4, padding: '8px 0',
                                boxShadow: '0 4px 24px rgba(0,0,0,.12)',
                            }}
                        >
                            {Object.entries(filteredSkills).map(([category, items]) => (
                                <div key={category}>
                                    <p className="text-xs font-bold text-text-3" style={{ padding: '6px 12px 2px' }}>
                                        {category}
                                    </p>
                                    {items.slice(0, 8).map(skill => (
                                        <button
                                            key={skill}
                                            type="button"
                                            className="btn btn-ghost btn-sm w-full text-left"
                                            style={{ justifyContent: 'flex-start', borderRadius: 0, padding: '6px 16px' }}
                                            onClick={() => addSkill(skill)}
                                        >
                                            {skill}
                                        </button>
                                    ))}
                                </div>
                            ))}
                            {skillsInput.trim() && !Object.values(filteredSkills).flat().includes(skillsInput.trim()) && (
                                <button
                                    type="button"
                                    className="btn btn-ghost btn-sm w-full text-left"
                                    style={{ justifyContent: 'flex-start', borderRadius: 0, padding: '6px 16px', color: 'var(--purple)' }}
                                    onClick={() => addSkill(skillsInput)}
                                >
                                    Add &quot;{skillsInput.trim()}&quot;
                                </button>
                            )}
                            {Object.keys(filteredSkills).length === 0 && !skillsInput.trim() && (
                                <p className="text-sm text-text-3" style={{ padding: '8px 16px' }}>Type to search skills...</p>
                            )}
                        </div>
                    )}
                </div>

                <div className="flex justify-end">
                    <button className="btn btn-purple" disabled={saving} onClick={saveProfessional}>
                        {saving ? 'Saving...' : 'Save Professional Details'}
                    </button>
                </div>
            </div>
        );
    }

    function renderMentorship() {
        return (
            <div className="card card-p">
                <h2 className="text-xl font-bold mb-1">Mentorship Preferences</h2>
                <p className="text-text-2 text-sm mb-6">Configure how you want to mentor.</p>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label className="field-label">Mentorship availability</label>
                        <select className="field-input w-full" value={profile.mentorship_availability || ''} onChange={e => updateProfile('mentorship_availability', e.target.value)}>
                            <option value="">Select frequency...</option>
                            {AVAILABILITY_OPTIONS.map(a => <option key={a} value={a}>{a}</option>)}
                        </select>
                    </div>
                    <div>
                        <label className="field-label">Mentees at once (1-6)</label>
                        <input
                            className="field-input w-full"
                            type="number"
                            min="1"
                            max="6"
                            value={profile.mentees_at_once || ''}
                            onChange={e => updateProfile('mentees_at_once', e.target.value)}
                        />
                    </div>
                    <div className="md:col-span-2">
                        <label className="field-label">BP Network</label>
                        <select className="field-input w-full" value={profile.bp_network || ''} onChange={e => updateProfile('bp_network', e.target.value)}>
                            <option value="">Select network...</option>
                            {BP_NETWORK_OPTIONS.map(n => <option key={n} value={n}>{n}</option>)}
                        </select>
                    </div>
                </div>

                <div className="mb-6">
                    <label className="field-label">Mentorship requirements</label>
                    <textarea
                        className="field-input field-textarea w-full"
                        rows={4}
                        placeholder="What do you expect from your mentees?"
                        value={profile.mentorship_requirements || ''}
                        onChange={e => updateProfile('mentorship_requirements', e.target.value)}
                    />
                </div>

                <div className="flex justify-end">
                    <button className="btn btn-purple" disabled={saving} onClick={saveMentorship}>
                        {saving ? 'Saving...' : 'Save Mentorship Preferences'}
                    </button>
                </div>
            </div>
        );
    }

    function renderAvailability() {
        const todayStr = new Date().toISOString().split('T')[0];

        return (
            <div className="card card-p">
                <h2 className="text-xl font-bold mb-1">Weekly Availability</h2>
                <p className="text-text-2 text-sm mb-6">Set your available time slots for mentoring sessions.</p>

                {/* Timezone */}
                <div className="mb-6">
                    <label className="field-label">Timezone</label>
                    <select className="field-input w-full" style={{ maxWidth: 320 }} value={timezone} onChange={e => setTimezone(e.target.value)}>
                        {TIMEZONES.map(tz => <option key={tz} value={tz}>{tz.replace(/_/g, ' ')}</option>)}
                    </select>
                </div>

                {/* Day schedule grid */}
                <div className="mb-6" style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                    {DAYS.map(day => {
                        const dayBlocks = schedule.filter(s => s.day === day);
                        const isActive = dayBlocks.length > 0;

                        return (
                            <div key={day} style={{ padding: '12px 16px', background: 'var(--surface)', borderRadius: 8, border: '1px solid var(--border)' }}>
                                <div className="flex items-center gap-3 mb-2">
                                    <button
                                        type="button"
                                        onClick={() => toggleDay(day)}
                                        style={{
                                            width: 40, height: 22, borderRadius: 11, border: 'none', cursor: 'pointer',
                                            background: isActive ? 'var(--purple)' : 'var(--border)',
                                            position: 'relative', transition: 'background .2s',
                                        }}
                                    >
                                        <span style={{
                                            position: 'absolute', top: 2, width: 18, height: 18, borderRadius: '50%',
                                            background: '#fff', transition: 'left .2s',
                                            left: isActive ? 20 : 2,
                                        }} />
                                    </button>
                                    <span className="font-semibold text-sm" style={{ minWidth: 90 }}>{day}</span>
                                    {isActive && (
                                        <button type="button" className="btn btn-ghost btn-sm" style={{ fontSize: 12 }} onClick={() => addTimeBlock(day)}>
                                            + Add block
                                        </button>
                                    )}
                                </div>

                                {isActive && dayBlocks.map((block, idx) => (
                                    <div key={idx} className="flex items-center gap-2" style={{ marginLeft: 52, marginBottom: 4 }}>
                                        <select
                                            className="field-input"
                                            style={{ width: 110 }}
                                            value={block.start}
                                            onChange={e => updateTimeBlock(day, idx, 'start', e.target.value)}
                                        >
                                            {HOURS.map(h => <option key={h} value={h}>{h}</option>)}
                                        </select>
                                        <span className="text-text-3 text-sm">to</span>
                                        <select
                                            className="field-input"
                                            style={{ width: 110 }}
                                            value={block.end}
                                            onChange={e => updateTimeBlock(day, idx, 'end', e.target.value)}
                                        >
                                            {HOURS.map(h => <option key={h} value={h}>{h}</option>)}
                                        </select>
                                        {dayBlocks.length > 1 && (
                                            <button
                                                type="button"
                                                className="btn btn-ghost btn-sm"
                                                style={{ color: 'var(--err)', padding: '2px 8px' }}
                                                onClick={() => removeTimeBlock(day, idx)}
                                            >
                                                &times;
                                            </button>
                                        )}
                                    </div>
                                ))}

                                {!isActive && (
                                    <p className="text-xs text-text-3" style={{ marginLeft: 52 }}>Unavailable</p>
                                )}
                            </div>
                        );
                    })}
                </div>

                {/* Holidays */}
                <div className="mb-6">
                    <label className="field-label">Holiday / Blocked dates</label>
                    <div className="flex gap-2 mb-3">
                        <input
                            type="date"
                            className="field-input"
                            min={todayStr}
                            value={holidayInput}
                            onChange={e => setHolidayInput(e.target.value)}
                        />
                        <button
                            type="button"
                            className="btn btn-outline btn-sm"
                            onClick={() => { addHoliday(holidayInput); setHolidayInput(''); }}
                            disabled={!holidayInput}
                        >
                            Add date
                        </button>
                    </div>
                    {holidays.length > 0 && (
                        <div className="flex flex-wrap gap-2">
                            {holidays.map(d => (
                                <span key={d} className="badge" style={{ display: 'inline-flex', alignItems: 'center', gap: 4 }}>
                                    {new Date(d + 'T00:00:00').toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' })}
                                    <button
                                        type="button"
                                        onClick={() => removeHoliday(d)}
                                        style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0, fontSize: 14, lineHeight: 1, color: 'inherit', opacity: 0.7 }}
                                    >
                                        &times;
                                    </button>
                                </span>
                            ))}
                        </div>
                    )}
                </div>

                <div className="flex justify-end">
                    <button className="btn btn-purple" disabled={saving} onClick={saveAvailability}>
                        {saving ? 'Saving...' : 'Save Availability'}
                    </button>
                </div>
            </div>
        );
    }

    function renderExperience() {
        return (
            <div className="card card-p">
                <div className="flex items-center justify-between mb-6">
                    <div>
                        <h2 className="text-xl font-bold mb-1">Work Experience</h2>
                        <p className="text-text-2 text-sm">Your professional experience history.</p>
                    </div>
                    {!newExp && editingExp === null && (
                        <button className="btn btn-outline btn-sm" onClick={() => { setNewExp(true); setExpForm(emptyExp); }}>
                            + Add experience
                        </button>
                    )}
                </div>

                {/* New / Edit form */}
                {(newExp || editingExp !== null) && (
                    <div style={{ padding: 16, background: 'var(--surface)', borderRadius: 8, border: '1px solid var(--border)', marginBottom: 16 }}>
                        <p className="font-semibold text-sm mb-3">{newExp ? 'Add experience' : 'Edit experience'}</p>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label className="field-label">Job title</label>
                                <input className="field-input w-full" value={expForm.title} onChange={e => setExpForm(p => ({ ...p, title: e.target.value }))} />
                            </div>
                            <div>
                                <label className="field-label">Company</label>
                                <input className="field-input w-full" value={expForm.company} onChange={e => setExpForm(p => ({ ...p, company: e.target.value }))} />
                            </div>
                            <div>
                                <label className="field-label">Start date</label>
                                <div className="flex gap-2">
                                    <select
                                        className="field-input"
                                        style={{ flex: 1 }}
                                        value={expForm.start_date ? expForm.start_date.split('-')[1] || '' : ''}
                                        onChange={e => {
                                            const year = expForm.start_date?.split('-')[0] || '';
                                            setExpForm(p => ({ ...p, start_date: `${year}-${e.target.value}` }));
                                        }}
                                    >
                                        <option value="">Month</option>
                                        {MONTHS.map((m, i) => <option key={m} value={String(i + 1).padStart(2, '0')}>{m}</option>)}
                                    </select>
                                    <input
                                        className="field-input"
                                        style={{ width: 90 }}
                                        type="number"
                                        placeholder="Year"
                                        min="1960"
                                        max="2030"
                                        value={expForm.start_date?.split('-')[0] || ''}
                                        onChange={e => {
                                            const month = expForm.start_date?.split('-')[1] || '01';
                                            setExpForm(p => ({ ...p, start_date: `${e.target.value}-${month}` }));
                                        }}
                                    />
                                </div>
                            </div>
                            <div>
                                <label className="field-label">End date</label>
                                {expForm.is_current ? (
                                    <p className="text-sm text-text-2" style={{ paddingTop: 8 }}>Present</p>
                                ) : (
                                    <div className="flex gap-2">
                                        <select
                                            className="field-input"
                                            style={{ flex: 1 }}
                                            value={expForm.end_date ? expForm.end_date.split('-')[1] || '' : ''}
                                            onChange={e => {
                                                const year = expForm.end_date?.split('-')[0] || '';
                                                setExpForm(p => ({ ...p, end_date: `${year}-${e.target.value}` }));
                                            }}
                                        >
                                            <option value="">Month</option>
                                            {MONTHS.map((m, i) => <option key={m} value={String(i + 1).padStart(2, '0')}>{m}</option>)}
                                        </select>
                                        <input
                                            className="field-input"
                                            style={{ width: 90 }}
                                            type="number"
                                            placeholder="Year"
                                            min="1960"
                                            max="2030"
                                            value={expForm.end_date?.split('-')[0] || ''}
                                            onChange={e => {
                                                const month = expForm.end_date?.split('-')[1] || '01';
                                                setExpForm(p => ({ ...p, end_date: `${e.target.value}-${month}` }));
                                            }}
                                        />
                                    </div>
                                )}
                                <label className="flex items-center gap-2 mt-2" style={{ cursor: 'pointer' }}>
                                    <input
                                        type="checkbox"
                                        checked={expForm.is_current}
                                        onChange={e => setExpForm(p => ({ ...p, is_current: e.target.checked, end_date: e.target.checked ? '' : p.end_date }))}
                                    />
                                    <span className="text-sm">I currently work here</span>
                                </label>
                            </div>
                        </div>
                        <div className="mb-4">
                            <label className="field-label">Description</label>
                            <textarea
                                className="field-input field-textarea w-full"
                                rows={3}
                                value={expForm.description}
                                onChange={e => setExpForm(p => ({ ...p, description: e.target.value }))}
                            />
                        </div>
                        <div className="flex gap-2 justify-end">
                            <button
                                className="btn btn-ghost btn-sm"
                                onClick={() => { setNewExp(false); setEditingExp(null); setExpForm(emptyExp); }}
                            >
                                Cancel
                            </button>
                            <button
                                className="btn btn-purple btn-sm"
                                disabled={saving || !expForm.title || !expForm.company}
                                onClick={() => saveExperience(newExp)}
                            >
                                {saving ? 'Saving...' : 'Save'}
                            </button>
                        </div>
                    </div>
                )}

                {/* Existing entries */}
                {experiences.length === 0 && !newExp && (
                    <p className="text-text-3 text-sm">No experience entries yet. Add your first one above.</p>
                )}

                <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                    {experiences.map(exp => (
                        <div key={exp.id} style={{ padding: '12px 16px', background: 'var(--surface)', borderRadius: 8, border: '1px solid var(--border)' }}>
                            <div className="flex items-start justify-between">
                                <div>
                                    <p className="font-semibold">{exp.title}</p>
                                    <p className="text-sm text-text-2">{exp.company}</p>
                                    <p className="text-xs text-text-3 mt-1">
                                        {exp.start_date} {exp.is_current ? '- Present' : exp.end_date ? `- ${exp.end_date}` : ''}
                                    </p>
                                    {exp.description && <p className="text-sm mt-2">{exp.description}</p>}
                                </div>
                                <div className="flex gap-1">
                                    <button
                                        className="btn btn-ghost btn-sm"
                                        onClick={() => {
                                            setEditingExp(exp.id);
                                            setNewExp(false);
                                            setExpForm({
                                                title: exp.title,
                                                company: exp.company,
                                                start_date: exp.start_date,
                                                end_date: exp.end_date,
                                                is_current: exp.is_current,
                                                description: exp.description,
                                            });
                                        }}
                                    >
                                        Edit
                                    </button>
                                    <button
                                        className="btn btn-ghost btn-sm"
                                        style={{ color: 'var(--err)' }}
                                        onClick={() => deleteExperience(exp.id)}
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        );
    }

    function renderEducation() {
        return (
            <div className="card card-p">
                <div className="flex items-center justify-between mb-6">
                    <div>
                        <h2 className="text-xl font-bold mb-1">Education</h2>
                        <p className="text-text-2 text-sm">Your academic background.</p>
                    </div>
                    {!newEdu && editingEdu === null && (
                        <button className="btn btn-outline btn-sm" onClick={() => { setNewEdu(true); setEduForm(emptyEdu); }}>
                            + Add education
                        </button>
                    )}
                </div>

                {/* New / Edit form */}
                {(newEdu || editingEdu !== null) && (
                    <div style={{ padding: 16, background: 'var(--surface)', borderRadius: 8, border: '1px solid var(--border)', marginBottom: 16 }}>
                        <p className="font-semibold text-sm mb-3">{newEdu ? 'Add education' : 'Edit education'}</p>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label className="field-label">Institution</label>
                                <input className="field-input w-full" value={eduForm.institution} onChange={e => setEduForm(p => ({ ...p, institution: e.target.value }))} />
                            </div>
                            <div>
                                <label className="field-label">Degree / Qualification</label>
                                <input className="field-input w-full" value={eduForm.degree} onChange={e => setEduForm(p => ({ ...p, degree: e.target.value }))} />
                            </div>
                            <div>
                                <label className="field-label">Start year</label>
                                <input
                                    className="field-input w-full"
                                    type="number"
                                    min="1960"
                                    max="2030"
                                    placeholder="e.g. 2015"
                                    value={eduForm.start_year}
                                    onChange={e => setEduForm(p => ({ ...p, start_year: e.target.value }))}
                                />
                            </div>
                            <div>
                                <label className="field-label">End year</label>
                                <input
                                    className="field-input w-full"
                                    type="number"
                                    min="1960"
                                    max="2030"
                                    placeholder="e.g. 2019"
                                    value={eduForm.end_year}
                                    onChange={e => setEduForm(p => ({ ...p, end_year: e.target.value }))}
                                />
                            </div>
                        </div>
                        <div className="flex gap-2 justify-end">
                            <button
                                className="btn btn-ghost btn-sm"
                                onClick={() => { setNewEdu(false); setEditingEdu(null); setEduForm(emptyEdu); }}
                            >
                                Cancel
                            </button>
                            <button
                                className="btn btn-purple btn-sm"
                                disabled={saving || !eduForm.institution || !eduForm.degree}
                                onClick={() => saveEducation(newEdu)}
                            >
                                {saving ? 'Saving...' : 'Save'}
                            </button>
                        </div>
                    </div>
                )}

                {/* Existing entries */}
                {educationList.length === 0 && !newEdu && (
                    <p className="text-text-3 text-sm">No education entries yet. Add your first one above.</p>
                )}

                <div style={{ display: 'flex', flexDirection: 'column', gap: 12 }}>
                    {educationList.map(edu => (
                        <div key={edu.id} style={{ padding: '12px 16px', background: 'var(--surface)', borderRadius: 8, border: '1px solid var(--border)' }}>
                            <div className="flex items-start justify-between">
                                <div>
                                    <p className="font-semibold">{edu.degree}</p>
                                    <p className="text-sm text-text-2">{edu.institution}</p>
                                    <p className="text-xs text-text-3 mt-1">
                                        {edu.start_year}{edu.end_year ? ` - ${edu.end_year}` : ''}
                                    </p>
                                </div>
                                <div className="flex gap-1">
                                    <button
                                        className="btn btn-ghost btn-sm"
                                        onClick={() => {
                                            setEditingEdu(edu.id);
                                            setNewEdu(false);
                                            setEduForm({
                                                institution: edu.institution,
                                                degree: edu.degree,
                                                start_year: edu.start_year,
                                                end_year: edu.end_year,
                                            });
                                        }}
                                    >
                                        Edit
                                    </button>
                                    <button
                                        className="btn btn-ghost btn-sm"
                                        style={{ color: 'var(--err)' }}
                                        onClick={() => deleteEducation(edu.id)}
                                    >
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        );
    }

    function renderSocial() {
        return (
            <div className="card card-p">
                <h2 className="text-xl font-bold mb-1">Social Links</h2>
                <p className="text-text-2 text-sm mb-6">Connect your social profiles so mentees can learn more about you.</p>

                <div style={{ display: 'flex', flexDirection: 'column', gap: 16 }}>
                    <div>
                        <label className="field-label">LinkedIn</label>
                        <input
                            className="field-input w-full"
                            type="url"
                            placeholder="https://linkedin.com/in/yourprofile"
                            value={profile.linkedin_profile || ''}
                            onChange={e => updateProfile('linkedin_profile', e.target.value)}
                        />
                    </div>
                    <div>
                        <label className="field-label">Facebook</label>
                        <input
                            className="field-input w-full"
                            type="url"
                            placeholder="https://facebook.com/yourprofile"
                            value={profile.facebook_profile || ''}
                            onChange={e => updateProfile('facebook_profile', e.target.value)}
                        />
                    </div>
                    <div>
                        <label className="field-label">Instagram</label>
                        <input
                            className="field-input w-full"
                            type="url"
                            placeholder="https://instagram.com/yourprofile"
                            value={profile.instagram_profile || ''}
                            onChange={e => updateProfile('instagram_profile', e.target.value)}
                        />
                    </div>
                    <div>
                        <label className="field-label">X (Twitter)</label>
                        <input
                            className="field-input w-full"
                            type="url"
                            placeholder="https://x.com/yourprofile"
                            value={profile.x_profile || ''}
                            onChange={e => updateProfile('x_profile', e.target.value)}
                        />
                    </div>
                </div>

                <div className="flex justify-end mt-6">
                    <button className="btn btn-purple" disabled={saving} onClick={saveSocial}>
                        {saving ? 'Saving...' : 'Save Social Links'}
                    </button>
                </div>
            </div>
        );
    }

    /* ------------------------------------------------------------------ */
    /*  Section map                                                        */
    /* ------------------------------------------------------------------ */

    const sectionContent: Record<string, () => React.ReactNode> = {
        personal: renderPersonal,
        professional: renderProfessional,
        mentorship: renderMentorship,
        availability: renderAvailability,
        experience: renderExperience,
        education: renderEducation,
        social: renderSocial,
    };

    /* ------------------------------------------------------------------ */
    /*  Render                                                             */
    /* ------------------------------------------------------------------ */

    return (
        <>
            <div className="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {/* Sidebar — vertical on desktop, horizontal scroll on mobile */}
                <nav
                    className="flex lg:flex-col gap-1"
                    style={{ overflowX: 'auto', WebkitOverflowScrolling: 'touch' }}
                >
                    {SECTIONS.map(sec => (
                        <button
                            key={sec.key}
                            className="btn btn-ghost btn-sm text-left"
                            style={
                                activeSection === sec.key
                                    ? { background: 'var(--purple-bg)', color: 'var(--purple)', fontWeight: 600, whiteSpace: 'nowrap' }
                                    : { whiteSpace: 'nowrap' }
                            }
                            onClick={() => setActiveSection(sec.key)}
                        >
                            {sec.label}
                        </button>
                    ))}
                </nav>

                {/* Content */}
                <div className="lg:col-span-3" style={{ position: 'relative' }}>
                    {sectionContent[activeSection]()}
                </div>
            </div>

            {/* Toast */}
            {toast && <Toast message={toast.message} type={toast.type} onClose={clearToast} />}
        </>
    );
}
