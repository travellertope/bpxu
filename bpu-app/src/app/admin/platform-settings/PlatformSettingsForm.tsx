'use client';

import { useState, useEffect } from 'react';

interface Settings {
    commission_rate: number;
    currency: string;
    booking_buffer_hours: number;
    max_bookings_per_day: number;
    job_admin_notification_email: string;
}

const CURRENCIES = ['GBP', 'USD', 'EUR', 'NGN', 'ZAR', 'KES', 'GHS'];

export default function PlatformSettingsForm() {
    const [settings, setSettings] = useState<Settings>({
        commission_rate: 0,
        currency: 'GBP',
        booking_buffer_hours: 24,
        max_bookings_per_day: 5,
        job_admin_notification_email: '',
    });
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    useEffect(() => {
        async function load() {
            try {
                const res = await fetch('/api/paired/admin/settings');
                const data = await res.json();
                if (!res.ok) throw new Error(data.error || 'Failed to load settings.');
                const s = data.settings || {};
                setSettings({
                    commission_rate: s.commission_rate ?? 0,
                    currency: s.currency || 'GBP',
                    booking_buffer_hours: s.booking_buffer_hours ?? 24,
                    max_bookings_per_day: s.max_bookings_per_day ?? 5,
                    job_admin_notification_email: s.job_admin_notification_email || '',
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
            const res = await fetch('/api/paired/admin/settings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(settings),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to save settings.');
            setSuccess('Settings saved successfully.');
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
                <h2 className="text-base font-bold">Financial</h2>

                <div>
                    <label htmlFor="commission-rate" className="field-label mb-1 block">Platform Commission Rate (%)</label>
                    <input
                        id="commission-rate"
                        type="number"
                        step="0.1"
                        min="0"
                        max="100"
                        className="field-input"
                        value={settings.commission_rate}
                        onChange={e => setSettings(prev => ({ ...prev, commission_rate: Number(e.target.value) }))}
                    />
                    <p className="text-xs text-text-3 mt-1">Percentage taken from each booking payment.</p>
                </div>

                <div>
                    <label htmlFor="currency" className="field-label mb-1 block">Default Currency</label>
                    <select
                        id="currency"
                        className="field-input"
                        value={settings.currency}
                        onChange={e => setSettings(prev => ({ ...prev, currency: e.target.value }))}
                    >
                        {CURRENCIES.map(c => <option key={c} value={c}>{c}</option>)}
                    </select>
                </div>
            </div>

            <div className="card card-p space-y-5">
                <h2 className="text-base font-bold">Booking Rules</h2>

                <div>
                    <label htmlFor="buffer-hours" className="field-label mb-1 block">Booking Buffer (hours)</label>
                    <input
                        id="buffer-hours"
                        type="number"
                        min="0"
                        className="field-input"
                        value={settings.booking_buffer_hours}
                        onChange={e => setSettings(prev => ({ ...prev, booking_buffer_hours: Number(e.target.value) }))}
                    />
                    <p className="text-xs text-text-3 mt-1">Minimum hours in advance a mentee must book.</p>
                </div>

                <div>
                    <label htmlFor="max-bookings" className="field-label mb-1 block">Max Bookings Per Day</label>
                    <input
                        id="max-bookings"
                        type="number"
                        min="1"
                        className="field-input"
                        value={settings.max_bookings_per_day}
                        onChange={e => setSettings(prev => ({ ...prev, max_bookings_per_day: Number(e.target.value) }))}
                    />
                    <p className="text-xs text-text-3 mt-1">Maximum bookings a mentee can make per day.</p>
                </div>
            </div>

            <div className="card card-p space-y-5">
                <h2 className="text-base font-bold">Job Board</h2>

                <div>
                    <label htmlFor="job-admin-email" className="field-label mb-1 block">Admin Job Application Notifications</label>
                    <input
                        id="job-admin-email"
                        type="email"
                        className="field-input"
                        placeholder="jobs@blackprofessionals.uk"
                        value={settings.job_admin_notification_email}
                        onChange={e => setSettings(prev => ({ ...prev, job_admin_notification_email: e.target.value }))}
                    />
                    <p className="text-xs text-text-3 mt-1">
                        For jobs posted by an administrator, new-application emails go here instead of the posting admin&apos;s own inbox. Leave blank to notify the posting admin directly.
                    </p>
                </div>
            </div>

            <button type="submit" disabled={saving} className="btn btn-purple">
                {saving ? 'Saving...' : 'Save Settings'}
            </button>
        </form>
    );
}
