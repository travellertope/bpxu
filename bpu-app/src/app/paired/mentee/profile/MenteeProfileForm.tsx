'use client';

import { useState, useEffect, useRef } from 'react';

interface MenteeProfile {
    industry?: string;
    career_goals?: string;
    skills_to_develop?: string[];
    bio?: string;
}

interface Props {
    initialProfile: MenteeProfile;
}

const INDUSTRIES = [
    'Technology',
    'Finance & Banking',
    'Healthcare',
    'Education',
    'Legal',
    'Marketing & Advertising',
    'Media & Entertainment',
    'Engineering',
    'Consulting',
    'Non-Profit',
    'Government & Public Sector',
    'Real Estate',
    'Retail & E-Commerce',
    'Energy & Utilities',
    'Construction',
    'Hospitality & Tourism',
    'Transport & Logistics',
    'Telecommunications',
    'Creative & Design',
    'Other',
];

export default function MenteeProfileForm({ initialProfile }: Props) {
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

    // Fetch available skills
    useEffect(() => {
        async function fetchSkills() {
            try {
                const res = await fetch('/api/paired/skills');
                if (res.ok) {
                    const data = await res.json();
                    const skillsMap = data.skills || {};
                    const flat = Object.values(skillsMap).flat() as string[];
                    setAllSkills(flat);
                }
            } catch {
                // Fallback skills
                setAllSkills([
                    'Leadership', 'Communication', 'Public Speaking', 'Project Management',
                    'Data Analysis', 'Strategic Thinking', 'Networking', 'Negotiation',
                    'Time Management', 'Problem Solving', 'Financial Literacy', 'Coding',
                    'Design Thinking', 'Mentoring', 'Writing', 'Marketing',
                    'Business Development', 'Career Transition', 'Interview Skills',
                    'Personal Branding', 'Entrepreneurship', 'Team Building',
                ]);
            }
        }
        fetchSkills();
    }, []);

    // Close suggestions when clicking outside
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

    // Filter suggestions
    useEffect(() => {
        if (skillInput.trim().length === 0) {
            setSkillSuggestions([]);
            return;
        }
        const lower = skillInput.toLowerCase();
        const filtered = allSkills
            .filter(s => s.toLowerCase().includes(lower) && !skills.includes(s))
            .slice(0, 8);
        setSkillSuggestions(filtered);
    }, [skillInput, allSkills, skills]);

    function addSkill(skill: string) {
        const trimmed = skill.trim();
        if (trimmed && !skills.includes(trimmed)) {
            setSkills(prev => [...prev, trimmed]);
        }
        setSkillInput('');
        setShowSuggestions(false);
        inputRef.current?.focus();
    }

    function removeSkill(skill: string) {
        setSkills(prev => prev.filter(s => s !== skill));
    }

    function handleKeyDown(e: React.KeyboardEvent<HTMLInputElement>) {
        if (e.key === 'Enter') {
            e.preventDefault();
            if (skillSuggestions.length > 0) {
                addSkill(skillSuggestions[0]);
            } else if (skillInput.trim()) {
                addSkill(skillInput);
            }
        }
        if (e.key === 'Backspace' && !skillInput && skills.length > 0) {
            removeSkill(skills[skills.length - 1]);
        }
    }

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setSaving(true);
        setError('');
        setSuccess('');

        try {
            const res = await fetch('/api/paired/mentee/profile', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    industry,
                    career_goals: careerGoals,
                    skills_to_develop: skills,
                    bio,
                }),
            });
            const data = await res.json();
            if (!res.ok) {
                setError(data.message || data.error || 'Failed to save profile.');
            } else {
                setSuccess('Profile saved successfully.');
                setTimeout(() => setSuccess(''), 4000);
            }
        } catch {
            setError('Something went wrong. Please try again.');
        } finally {
            setSaving(false);
        }
    }

    return (
        <form onSubmit={handleSubmit} className="card card-p space-y-6">
            {error && <div className="alert alert-red">{error}</div>}
            {success && <div className="alert alert-green">{success}</div>}

            {/* Industry */}
            <div>
                <label htmlFor="industry" className="field-label mb-2 block">Industry</label>
                <select
                    id="industry"
                    className="field-input"
                    value={industry}
                    onChange={(e) => setIndustry(e.target.value)}
                >
                    <option value="">Select your industry</option>
                    {INDUSTRIES.map((ind) => (
                        <option key={ind} value={ind}>{ind}</option>
                    ))}
                </select>
            </div>

            {/* Career Goals */}
            <div>
                <label htmlFor="career-goals" className="field-label mb-2 block">Career Goals</label>
                <textarea
                    id="career-goals"
                    className="field-textarea"
                    rows={4}
                    value={careerGoals}
                    onChange={(e) => setCareerGoals(e.target.value)}
                    placeholder="What are your career aspirations? Where do you see yourself in the next 2-5 years?"
                />
            </div>

            {/* Skills to Develop */}
            <div>
                <label className="field-label mb-2 block">Skills to Develop</label>
                <div
                    className="field-input"
                    style={{
                        display: 'flex',
                        flexWrap: 'wrap',
                        gap: 6,
                        padding: '8px 12px',
                        minHeight: 44,
                        alignItems: 'center',
                        cursor: 'text',
                    }}
                    onClick={() => inputRef.current?.focus()}
                >
                    {skills.map((skill) => (
                        <span
                            key={skill}
                            className="badge badge-purple"
                            style={{
                                display: 'inline-flex',
                                alignItems: 'center',
                                gap: 4,
                                paddingRight: 4,
                            }}
                        >
                            {skill}
                            <button
                                type="button"
                                onClick={(e) => { e.stopPropagation(); removeSkill(skill); }}
                                style={{
                                    background: 'none',
                                    border: 'none',
                                    cursor: 'pointer',
                                    padding: '0 2px',
                                    fontSize: '1rem',
                                    lineHeight: 1,
                                    color: 'inherit',
                                    opacity: 0.7,
                                }}
                                aria-label={`Remove ${skill}`}
                            >
                                x
                            </button>
                        </span>
                    ))}
                    <input
                        ref={inputRef}
                        type="text"
                        value={skillInput}
                        onChange={(e) => { setSkillInput(e.target.value); setShowSuggestions(true); }}
                        onFocus={() => setShowSuggestions(true)}
                        onKeyDown={handleKeyDown}
                        placeholder={skills.length === 0 ? 'Type to search skills...' : ''}
                        style={{
                            border: 'none',
                            outline: 'none',
                            background: 'transparent',
                            flex: 1,
                            minWidth: 120,
                            fontSize: 'inherit',
                            color: 'inherit',
                            padding: '2px 0',
                        }}
                    />
                </div>
                {/* Suggestions Dropdown */}
                {showSuggestions && skillSuggestions.length > 0 && (
                    <div
                        ref={suggestionsRef}
                        className="card"
                        style={{
                            marginTop: 4,
                            maxHeight: 200,
                            overflowY: 'auto',
                            boxShadow: '0 4px 16px rgba(0,0,0,.1)',
                        }}
                    >
                        {skillSuggestions.map((s) => (
                            <button
                                key={s}
                                type="button"
                                onClick={() => addSkill(s)}
                                className="w-full text-left p-3 text-sm hover:bg-surface transition-colors"
                                style={{
                                    border: 'none',
                                    background: 'transparent',
                                    cursor: 'pointer',
                                    borderBottom: '1px solid var(--border)',
                                }}
                            >
                                {s}
                            </button>
                        ))}
                    </div>
                )}
                <p className="text-xs text-text-3 mt-1">Press Enter to add a skill. Click x to remove.</p>
            </div>

            {/* Bio */}
            <div>
                <label htmlFor="bio" className="field-label mb-2 block">Bio</label>
                <textarea
                    id="bio"
                    className="field-textarea"
                    rows={4}
                    value={bio}
                    onChange={(e) => setBio(e.target.value)}
                    placeholder="Tell mentors a bit about yourself, your background, and what you're looking for in a mentorship."
                />
            </div>

            {/* Submit */}
            <button
                type="submit"
                className="btn btn-purple btn-lg w-full"
                disabled={saving}
            >
                {saving ? 'Saving...' : 'Save Profile'}
            </button>
        </form>
    );
}
