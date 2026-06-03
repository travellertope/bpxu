'use client';

import { useState, useRef } from 'react';
import Link from 'next/link';
import { Employer } from '../../jobs/types';

const WP_BACKEND_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

interface Props {
    initialProfile: Employer | null;
    jwt: string;
}

export default function EmployerProfileForm({ initialProfile, jwt }: Props) {
    const [profile, setProfile] = useState<Partial<Employer>>(initialProfile ?? {});
    const [saving, setSaving] = useState(false);
    const [uploadingLogo, setUploadingLogo] = useState(false);
    const [saved, setSaved] = useState(false);
    const [error, setError] = useState('');
    const fileRef = useRef<HTMLInputElement>(null);

    const set = (key: keyof Employer, value: string) => {
        setSaved(false);
        setProfile(p => ({ ...p, [key]: value }));
    };

    async function handleSave(e: React.FormEvent) {
        e.preventDefault();
        setSaving(true);
        setError('');
        setSaved(false);
        try {
            const res = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/employer/profile`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    Authorization: `Bearer ${jwt}`,
                },
                body: JSON.stringify({
                    name:        profile.name        ?? '',
                    tagline:     profile.tagline      ?? '',
                    website:     profile.website      ?? '',
                    twitter:     profile.twitter      ?? '',
                    video:       profile.video        ?? '',
                    description: profile.description  ?? '',
                }),
            });
            const data = await res.json();
            if (!res.ok || !data.success) {
                setError(data.message || 'Failed to save profile.');
            } else {
                setProfile(data.profile ?? profile);
                setSaved(true);
            }
        } catch {
            setError('Network error. Please try again.');
        } finally {
            setSaving(false);
        }
    }

    async function handleLogoChange(e: React.ChangeEvent<HTMLInputElement>) {
        const file = e.target.files?.[0];
        if (!file) return;
        setUploadingLogo(true);
        setError('');
        try {
            const form = new FormData();
            form.append('logo', file);
            const res = await fetch(`${WP_BACKEND_URL}/wp-json/bpu/v1/employer/logo`, {
                method: 'POST',
                headers: { Authorization: `Bearer ${jwt}` },
                body: form,
            });
            const data = await res.json();
            if (!res.ok || !data.success) {
                setError(data.message || 'Logo upload failed.');
            } else {
                setProfile(p => ({ ...p, logo_url: data.logo_url }));
            }
        } catch {
            setError('Logo upload failed. Please try again.');
        } finally {
            setUploadingLogo(false);
            if (fileRef.current) fileRef.current.value = '';
        }
    }

    const initials = (profile.name ?? '')
        .split(/\s+/)
        .slice(0, 2)
        .map(w => w[0]?.toUpperCase() ?? '')
        .join('');

    return (
        <div className="min-h-screen flex flex-col">
            <header className="topbar">
                <div className="topbar-inner">
                    <a href="/" className="topbar-brand">
                        {/* eslint-disable-next-line @next/next/no-img-element */}
                        <img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="Black Professionals United" />
                    </a>
                    <div className="flex items-center gap-3">
                        <span className="text-sm text-text-2 hidden sm:block">{profile.name}</span>
                        <Link href="/jobs" className="btn btn-ghost btn-sm">View job board</Link>
                        <Link href="/employer/jobs/new" className="btn btn-amber btn-sm">Post a job +</Link>
                    </div>
                </div>
            </header>

            <main className="flex-1 wrap py-8">
                {/* Nav tabs */}
                <nav className="flex gap-1 mb-8 border-b border-border">
                    {[
                        { label: 'My Jobs', href: '/employer/jobs' },
                        { label: 'Company Profile', href: '/employer/profile' },
                    ].map(tab => (
                        <Link
                            key={tab.href}
                            href={tab.href}
                            className="px-4 py-2 text-sm font-medium border-b-2 -mb-px transition-colors"
                            style={{
                                borderColor: tab.href === '/employer/profile' ? 'var(--brand)' : 'transparent',
                                color: tab.href === '/employer/profile' ? 'var(--brand)' : 'var(--text-2)',
                            }}
                        >
                            {tab.label}
                        </Link>
                    ))}
                </nav>

                <div className="max-w-2xl">
                    <h1 className="text-2xl font-bold mb-1">Company Profile</h1>
                    <p className="text-text-2 text-sm mb-8">
                        This information appears on every job listing you post, so candidates can learn about your organisation.
                    </p>

                    {/* Logo section */}
                    <div className="card card-p mb-6">
                        <h2 className="section-title mb-4">Company Logo</h2>
                        <div className="flex items-center gap-5">
                            {profile.logo_url ? (
                                <div
                                    className="rounded-xl overflow-hidden border border-border bg-surface shrink-0"
                                    style={{ width: 80, height: 80 }}
                                >
                                    {/* eslint-disable-next-line @next/next/no-img-element */}
                                    <img
                                        src={profile.logo_url}
                                        alt={profile.name ?? 'Company logo'}
                                        style={{ width: '100%', height: '100%', objectFit: 'contain' }}
                                    />
                                </div>
                            ) : (
                                <div
                                    className="rounded-xl flex items-center justify-center text-xl font-bold border border-border shrink-0"
                                    style={{ width: 80, height: 80, background: 'var(--brand-bg)', color: 'var(--brand)' }}
                                >
                                    {initials || '?'}
                                </div>
                            )}
                            <div className="space-y-2">
                                <button
                                    type="button"
                                    onClick={() => fileRef.current?.click()}
                                    disabled={uploadingLogo}
                                    className="btn btn-outline btn-sm"
                                >
                                    {uploadingLogo ? 'Uploading…' : profile.logo_url ? 'Replace logo' : 'Upload logo'}
                                </button>
                                <p className="text-xs text-text-3">PNG, JPG, SVG or WebP — max 2 MB</p>
                                <input
                                    ref={fileRef}
                                    type="file"
                                    accept="image/png,image/jpeg,image/svg+xml,image/webp"
                                    className="sr-only"
                                    onChange={handleLogoChange}
                                />
                            </div>
                        </div>
                    </div>

                    {/* Profile form */}
                    <form onSubmit={handleSave} className="card card-p space-y-5">
                        <h2 className="section-title">Company Details</h2>

                        <div>
                            <label htmlFor="ep-name" className="field-label">Company name</label>
                            <input
                                id="ep-name"
                                type="text"
                                className="field-input"
                                value={profile.name ?? ''}
                                onChange={e => set('name', e.target.value)}
                                required
                                placeholder="e.g. Acme Corp"
                            />
                        </div>

                        <div>
                            <label htmlFor="ep-tagline" className="field-label">Tagline <span className="text-text-3 font-normal">(shown under company name on job cards)</span></label>
                            <input
                                id="ep-tagline"
                                type="text"
                                className="field-input"
                                value={profile.tagline ?? ''}
                                onChange={e => set('tagline', e.target.value)}
                                placeholder="e.g. A B Corp, Employee Owned Tech Agency"
                                maxLength={120}
                            />
                        </div>

                        <div>
                            <label htmlFor="ep-website" className="field-label">Website</label>
                            <input
                                id="ep-website"
                                type="url"
                                className="field-input"
                                value={profile.website ?? ''}
                                onChange={e => set('website', e.target.value)}
                                placeholder="https://yourcompany.com"
                            />
                        </div>

                        <div>
                            <label htmlFor="ep-twitter" className="field-label">Twitter / X handle</label>
                            <div className="relative">
                                <span
                                    className="absolute left-3 top-1/2 -translate-y-1/2 text-text-3 text-sm pointer-events-none"
                                >@</span>
                                <input
                                    id="ep-twitter"
                                    type="text"
                                    className="field-input pl-7"
                                    value={(profile.twitter ?? '').replace(/^@/, '')}
                                    onChange={e => set('twitter', e.target.value.replace(/^@/, ''))}
                                    placeholder="yourcompany"
                                />
                            </div>
                        </div>

                        <div>
                            <label htmlFor="ep-video" className="field-label">Company video <span className="text-text-3 font-normal">(YouTube/Vimeo embed URL — shown on job detail pages)</span></label>
                            <input
                                id="ep-video"
                                type="url"
                                className="field-input"
                                value={profile.video ?? ''}
                                onChange={e => set('video', e.target.value)}
                                placeholder="https://www.youtube.com/embed/…"
                            />
                        </div>

                        <div>
                            <label htmlFor="ep-desc" className="field-label">About the company <span className="text-text-3 font-normal">(shown on every job listing)</span></label>
                            <textarea
                                id="ep-desc"
                                className="field-input"
                                rows={6}
                                value={profile.description ?? ''}
                                onChange={e => set('description', e.target.value)}
                                placeholder="Tell candidates about your mission, culture, and why they should join your team…"
                                style={{ resize: 'vertical' }}
                            />
                        </div>

                        {error && (
                            <div className="alert alert-red text-sm">{error}</div>
                        )}

                        {saved && (
                            <div className="alert alert-green text-sm">Profile saved successfully.</div>
                        )}

                        <div className="flex items-center gap-3 pt-1">
                            <button
                                type="submit"
                                className="btn btn-amber"
                                disabled={saving}
                            >
                                {saving ? 'Saving…' : 'Save profile'}
                            </button>
                            <Link href="/employer/jobs" className="btn btn-ghost btn-sm">
                                Back to jobs
                            </Link>
                        </div>
                    </form>
                </div>
            </main>

            <footer className="py-6 text-center text-xs text-text-3 border-t border-border mt-8">
                © {new Date().getFullYear()} Black Professionals United ·{' '}
                <a href="/" className="hover:underline">Back to Portal</a>
            </footer>
        </div>
    );
}
