'use client';

import { useState, useEffect } from 'react';

interface ReferralSettings {
    points_per_referral: number;
    referral_bonus_type: 'points' | 'discount';
    referral_bonus_value: number;
    referral_enabled: boolean;
    max_referrals_per_user: number;
}

export default function ReferralSettingsForm() {
    const [settings, setSettings] = useState<ReferralSettings>({
        points_per_referral: 10,
        referral_bonus_type: 'points',
        referral_bonus_value: 10,
        referral_enabled: true,
        max_referrals_per_user: 0,
    });
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    useEffect(() => {
        async function load() {
            try {
                const res = await fetch('/api/paired/admin/referral-settings');
                const data = await res.json();
                if (!res.ok) throw new Error(data.error || 'Failed to load settings.');
                setSettings({
                    points_per_referral: data.points_per_referral ?? 10,
                    referral_bonus_type: data.referral_bonus_type || 'points',
                    referral_bonus_value: data.referral_bonus_value ?? 10,
                    referral_enabled: data.referral_enabled !== false && data.referral_enabled !== '0',
                    max_referrals_per_user: data.max_referrals_per_user ?? 0,
                });
            } catch (e) {
                setError(e instanceof Error ? e.message : 'Failed to load settings.');
            } finally {
                setLoading(false);
            }
        }
        load();
    }, []);

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setSaving(true);
        setError('');
        setSuccess('');

        try {
            const res = await fetch('/api/paired/admin/referral-settings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    ...settings,
                    referral_enabled: settings.referral_enabled ? '1' : '0',
                }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to save.');
            setSuccess('Referral settings saved.');
            setTimeout(() => setSuccess(''), 4000);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to save.');
        } finally {
            setSaving(false);
        }
    }

    if (loading) return <div className="text-center text-sm text-text-2 py-12">Loading settings...</div>;

    return (
        <form onSubmit={handleSubmit} className="space-y-6" style={{ maxWidth: 600 }}>
            {error && <div className="alert alert-red">{error}</div>}
            {success && <div className="alert alert-green">{success}</div>}

            <div className="card card-p space-y-5">
                <h2 className="text-base font-bold">General</h2>

                <div className="flex items-center gap-3">
                    <input
                        id="referral-enabled"
                        type="checkbox"
                        checked={settings.referral_enabled}
                        onChange={e => setSettings(prev => ({ ...prev, referral_enabled: e.target.checked }))}
                    />
                    <label htmlFor="referral-enabled" className="text-sm font-medium">Referral program enabled</label>
                </div>

                <div>
                    <label htmlFor="max-referrals" className="field-label mb-1 block">Max Referrals Per User (0 = unlimited)</label>
                    <input
                        id="max-referrals"
                        type="number"
                        min="0"
                        className="field-input"
                        value={settings.max_referrals_per_user}
                        onChange={e => setSettings(prev => ({ ...prev, max_referrals_per_user: Number(e.target.value) }))}
                    />
                </div>
            </div>

            <div className="card card-p space-y-5">
                <h2 className="text-base font-bold">Rewards</h2>

                <div>
                    <label htmlFor="points-per-referral" className="field-label mb-1 block">Points Per Successful Referral</label>
                    <input
                        id="points-per-referral"
                        type="number"
                        min="0"
                        className="field-input"
                        value={settings.points_per_referral}
                        onChange={e => setSettings(prev => ({ ...prev, points_per_referral: Number(e.target.value) }))}
                    />
                    <p className="text-xs text-text-3 mt-1">Points awarded to the referrer when someone signs up.</p>
                </div>

                <div>
                    <label htmlFor="bonus-type" className="field-label mb-1 block">Referral Bonus Type</label>
                    <select
                        id="bonus-type"
                        className="field-input"
                        value={settings.referral_bonus_type}
                        onChange={e => setSettings(prev => ({ ...prev, referral_bonus_type: e.target.value as 'points' | 'discount' }))}
                    >
                        <option value="points">Points</option>
                        <option value="discount">Discount (%)</option>
                    </select>
                    <p className="text-xs text-text-3 mt-1">
                        {settings.referral_bonus_type === 'points'
                            ? 'Bonus points for the referred user.'
                            : 'Percentage discount on first booking for the referred user.'}
                    </p>
                </div>

                <div>
                    <label htmlFor="bonus-value" className="field-label mb-1 block">
                        Bonus Value {settings.referral_bonus_type === 'discount' ? '(%)' : '(points)'}
                    </label>
                    <input
                        id="bonus-value"
                        type="number"
                        min="0"
                        step={settings.referral_bonus_type === 'discount' ? '1' : '1'}
                        className="field-input"
                        value={settings.referral_bonus_value}
                        onChange={e => setSettings(prev => ({ ...prev, referral_bonus_value: Number(e.target.value) }))}
                    />
                </div>
            </div>

            <button type="submit" disabled={saving} className="btn btn-purple">
                {saving ? 'Saving...' : 'Save Settings'}
            </button>
        </form>
    );
}
