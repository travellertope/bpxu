'use client';

import { useState, useEffect } from 'react';

interface OnboardingStep {
    key: string;
    label: string;
    completed: boolean;
    href: string;
}

const STEP_LINKS: Record<string, string> = {
    profile: '/paired/mentor/settings',
    photo: '/paired/mentor/settings',
    availability: '/paired/mentor/settings',
    experience: '/paired/mentor/settings',
    education: '/paired/mentor/settings',
    meeting_settings: '/paired/mentor/meeting-settings',
    payout_settings: '/paired/mentor/payout-settings',
    kyc: '/paired/mentor/kyc',
};

export default function OnboardingChecklist() {
    const [steps, setSteps] = useState<OnboardingStep[]>([]);
    const [loading, setLoading] = useState(true);
    const [dismissed, setDismissed] = useState(false);

    useEffect(() => {
        if (typeof window !== 'undefined') {
            const stored = localStorage.getItem('paired_onboarding_dismissed');
            if (stored === 'true') {
                setDismissed(true);
                setLoading(false);
                return;
            }
        }

        async function load() {
            try {
                const res = await fetch('/api/paired/mentor/onboarding');
                const data = await res.json();
                if (!res.ok) throw new Error('Failed to load onboarding.');
                const mapped: OnboardingStep[] = (data.steps || []).map(
                    (s: { key: string; label: string; completed: boolean; link?: string }) => ({
                        ...s,
                        href: s.link || STEP_LINKS[s.key] || '/paired/mentor/settings',
                    })
                );
                setSteps(mapped);
            } catch {
                /* fail silently */
            } finally {
                setLoading(false);
            }
        }
        load();
    }, []);

    function handleDismiss() {
        localStorage.setItem('paired_onboarding_dismissed', 'true');
        setDismissed(true);
    }

    if (loading || dismissed) return null;

    const completed = steps.filter(s => s.completed).length;
    const total = steps.length;
    if (total === 0 || completed === total) return null;

    const pct = Math.round((completed / total) * 100);

    return (
        <div
            className="card card-p"
            style={{
                borderTop: '4px solid var(--purple)',
                background: 'linear-gradient(180deg, color-mix(in srgb, var(--purple) 4%, var(--surface)) 0%, var(--surface) 100%)',
            }}
        >
            <div style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                    <div>
                        <h3 className="font-bold text-lg">Complete Your Profile</h3>
                        <p className="text-sm" style={{ color: 'var(--text-2)' }}>
                            {completed} of {total} steps done ({pct}%)
                        </p>
                    </div>
                    <button
                        onClick={handleDismiss}
                        className="btn btn-ghost btn-sm"
                        style={{ color: 'var(--text-3)' }}
                    >
                        Dismiss
                    </button>
                </div>

                {/* Progress bar */}
                <div
                    style={{
                        height: '8px',
                        borderRadius: '4px',
                        background: 'var(--border)',
                        overflow: 'hidden',
                    }}
                >
                    <div
                        style={{
                            height: '100%',
                            width: `${pct}%`,
                            borderRadius: '4px',
                            background: 'var(--purple)',
                            transition: 'width 0.3s ease',
                        }}
                    />
                </div>

                {/* Steps list */}
                <div style={{ display: 'flex', flexDirection: 'column', gap: '8px' }}>
                    {steps.map(step => (
                        <a
                            key={step.key}
                            href={step.href}
                            className="flex items-center gap-3 rounded-lg p-3 hover:bg-[var(--bg)] transition-colors"
                            style={{ textDecoration: 'none', color: 'inherit' }}
                        >
                            {step.completed ? (
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <circle cx="10" cy="10" r="10" fill="#22c55e" />
                                    <path d="M6 10l3 3 5-6" stroke="#fff" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" />
                                </svg>
                            ) : (
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <circle cx="10" cy="10" r="9" stroke="var(--border)" strokeWidth="2" />
                                </svg>
                            )}
                            <span
                                className="text-sm font-medium"
                                style={{
                                    color: step.completed ? 'var(--text-3)' : 'var(--text)',
                                    textDecoration: step.completed ? 'line-through' : 'none',
                                }}
                            >
                                {step.label}
                            </span>
                        </a>
                    ))}
                </div>
            </div>
        </div>
    );
}
