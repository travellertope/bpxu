'use client';

import { useState, useEffect, useCallback } from 'react';

type SkillsMap = Record<string, string[]>;

export default function SkillsAdmin() {
    const [skills, setSkills] = useState<SkillsMap>({});
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [saving, setSaving] = useState(false);
    const [success, setSuccess] = useState('');

    const [newCategory, setNewCategory] = useState('');
    const [newSkillInputs, setNewSkillInputs] = useState<Record<string, string>>({});
    const [expandedCategory, setExpandedCategory] = useState<string | null>(null);

    const fetchSkills = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const res = await fetch('/api/paired/admin/skills');
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load skills.');
            setSkills(data.skills || {});
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load skills.');
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => { fetchSkills(); }, [fetchSkills]);

    async function handleSave() {
        setSaving(true);
        setError('');
        setSuccess('');
        try {
            const res = await fetch('/api/paired/admin/skills', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ skills }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to save.');
            setSuccess('Skills saved successfully.');
            setTimeout(() => setSuccess(''), 4000);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to save.');
        } finally {
            setSaving(false);
        }
    }

    async function handleReset() {
        if (!confirm('Reset all skills to platform defaults? Custom changes will be lost.')) return;
        setSaving(true);
        try {
            const res = await fetch('/api/paired/admin/skills/reset', { method: 'POST' });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to reset.');
            await fetchSkills();
            setSuccess('Skills reset to defaults.');
            setTimeout(() => setSuccess(''), 4000);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to reset.');
        } finally {
            setSaving(false);
        }
    }

    function addCategory() {
        const name = newCategory.trim();
        if (!name || skills[name]) return;
        setSkills(prev => ({ ...prev, [name]: [] }));
        setNewCategory('');
        setExpandedCategory(name);
    }

    function removeCategory(cat: string) {
        if (!confirm(`Delete category "${cat}" and all its skills?`)) return;
        setSkills(prev => {
            const copy = { ...prev };
            delete copy[cat];
            return copy;
        });
        if (expandedCategory === cat) setExpandedCategory(null);
    }

    function renameCategory(oldName: string, newName: string) {
        if (!newName.trim() || newName === oldName || skills[newName]) return;
        setSkills(prev => {
            const entries = Object.entries(prev);
            const result: SkillsMap = {};
            for (const [key, val] of entries) {
                result[key === oldName ? newName : key] = val;
            }
            return result;
        });
        if (expandedCategory === oldName) setExpandedCategory(newName);
    }

    function addSkill(cat: string) {
        const val = (newSkillInputs[cat] || '').trim();
        if (!val) return;
        if (skills[cat]?.some(s => s.toLowerCase() === val.toLowerCase())) return;
        setSkills(prev => ({ ...prev, [cat]: [...(prev[cat] || []), val] }));
        setNewSkillInputs(prev => ({ ...prev, [cat]: '' }));
    }

    function removeSkill(cat: string, skill: string) {
        setSkills(prev => ({ ...prev, [cat]: prev[cat].filter(s => s !== skill) }));
    }

    const categories = Object.keys(skills);
    const totalSkills = Object.values(skills).reduce((s, arr) => s + arr.length, 0);

    if (loading) return <div className="text-center text-sm text-text-2 py-12">Loading skills...</div>;

    return (
        <div className="space-y-5">
            {error && <div className="alert alert-red">{error}</div>}
            {success && <div className="alert alert-green">{success}</div>}

            {/* Summary */}
            <div className="flex items-center gap-4 text-sm text-text-3">
                <span>{categories.length} categories</span>
                <span>{totalSkills} skills</span>
            </div>

            {/* Add Category */}
            <div className="flex gap-2">
                <input
                    type="text"
                    className="field-input flex-1"
                    placeholder="New category name..."
                    value={newCategory}
                    onChange={e => setNewCategory(e.target.value)}
                    onKeyDown={e => e.key === 'Enter' && (e.preventDefault(), addCategory())}
                />
                <button onClick={addCategory} className="btn btn-purple btn-sm">+ Add Category</button>
            </div>

            {/* Categories */}
            <div className="space-y-3">
                {categories.map(cat => {
                    const isExpanded = expandedCategory === cat;
                    const catSkills = skills[cat] || [];

                    return (
                        <div key={cat} className="card" style={{ overflow: 'hidden' }}>
                            <button
                                onClick={() => setExpandedCategory(isExpanded ? null : cat)}
                                className="w-full text-left p-4 flex items-center justify-between"
                                style={{ background: 'none', border: 'none', cursor: 'pointer' }}
                            >
                                <div className="flex items-center gap-3">
                                    <svg
                                        width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"
                                        style={{ transform: isExpanded ? 'rotate(90deg)' : 'none', transition: 'transform 0.15s' }}
                                    >
                                        <polyline points="9 18 15 12 9 6" />
                                    </svg>
                                    <span className="font-semibold text-sm">{cat}</span>
                                    <span className="text-xs text-text-3">{catSkills.length} skills</span>
                                </div>
                                <button
                                    onClick={e => { e.stopPropagation(); removeCategory(cat); }}
                                    className="btn btn-ghost btn-sm text-xs"
                                    style={{ color: 'var(--err)' }}
                                >
                                    Remove
                                </button>
                            </button>

                            {isExpanded && (
                                <div className="px-4 pb-4 space-y-3" style={{ borderTop: '1px solid var(--border)' }}>
                                    {/* Rename */}
                                    <div className="pt-3">
                                        <label className="field-label mb-1 block text-xs">Category Name</label>
                                        <input
                                            type="text"
                                            className="field-input"
                                            defaultValue={cat}
                                            onBlur={e => renameCategory(cat, e.target.value)}
                                            style={{ maxWidth: 300 }}
                                        />
                                    </div>

                                    {/* Skills List */}
                                    <div className="flex flex-wrap gap-2">
                                        {catSkills.map(skill => (
                                            <span
                                                key={skill}
                                                className="flex items-center gap-1 text-xs px-2 py-1 rounded"
                                                style={{ background: 'var(--surface)', border: '1px solid var(--border)' }}
                                            >
                                                {skill}
                                                <button
                                                    onClick={() => removeSkill(cat, skill)}
                                                    style={{ background: 'none', border: 'none', cursor: 'pointer', padding: 0, color: 'var(--text-3)', fontSize: '0.85rem', lineHeight: 1 }}
                                                    aria-label={`Remove ${skill}`}
                                                >
                                                    ×
                                                </button>
                                            </span>
                                        ))}
                                        {catSkills.length === 0 && (
                                            <span className="text-xs text-text-3 italic">No skills yet</span>
                                        )}
                                    </div>

                                    {/* Add Skill */}
                                    <div className="flex gap-2">
                                        <input
                                            type="text"
                                            className="field-input flex-1"
                                            placeholder="Add a skill..."
                                            value={newSkillInputs[cat] || ''}
                                            onChange={e => setNewSkillInputs(prev => ({ ...prev, [cat]: e.target.value }))}
                                            onKeyDown={e => e.key === 'Enter' && (e.preventDefault(), addSkill(cat))}
                                            style={{ maxWidth: 300 }}
                                        />
                                        <button onClick={() => addSkill(cat)} className="btn btn-ghost btn-sm text-xs">Add</button>
                                    </div>
                                </div>
                            )}
                        </div>
                    );
                })}
            </div>

            {/* Actions */}
            <div className="flex gap-3 pt-2">
                <button onClick={handleSave} disabled={saving} className="btn btn-purple">
                    {saving ? 'Saving...' : 'Save All Changes'}
                </button>
                <button onClick={handleReset} disabled={saving} className="btn btn-outline" type="button">
                    Reset to Defaults
                </button>
            </div>
        </div>
    );
}
