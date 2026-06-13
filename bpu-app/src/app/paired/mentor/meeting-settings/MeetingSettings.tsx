'use client';

import { useState, useEffect } from 'react';

const PROVIDERS = [
    { value: 'google_meet', label: 'Google Meet' },
    { value: 'zoom', label: 'Zoom' },
    { value: 'teams', label: 'Microsoft Teams' },
    { value: 'custom', label: 'Custom URL' },
] as const;

const TIMEZONES = [
    'Europe/London',
    'Europe/Dublin',
    'America/New_York',
    'America/Chicago',
    'America/Denver',
    'America/Los_Angeles',
    'Africa/Lagos',
    'Africa/Johannesburg',
    'Asia/Dubai',
    'Asia/Kolkata',
    'Australia/Sydney',
];

type Provider = typeof PROVIDERS[number]['value'];

interface MeetingData {
    meeting_provider: Provider;
    custom_url?: string;
    calendar_sync?: boolean;
    timezone: string;
}

export default function MeetingSettings() {
    const [provider, setProvider] = useState<Provider>('google_meet');
    const [customUrl, setCustomUrl] = useState('');
    const [calendarSync, setCalendarSync] = useState(false);
    const [timezone, setTimezone] = useState('Europe/London');
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    useEffect(() => {
        async function load() {
            try {
                const res = await fetch('/api/paired/mentor/meeting-settings');
                const data: MeetingData = await res.json();
                if (!res.ok) throw new Error('Failed to load meeting settings.');
                setProvider(data.meeting_provider || 'google_meet');
                setCustomUrl(data.custom_url || '');
                setCalendarSync(data.calendar_sync || false);
                setTimezone(data.timezone || 'Europe/London');
            } catch (e) {
                setError(e instanceof Error ? e.message : 'Failed to load settings.');
            } finally {
                setLoading(false);
            }
        }
        load();
    }, []);

    async function handleSave() {
        setSaving(true);
        setError('');
        setSuccess('');
        try {
            const res = await fetch('/api/paired/mentor/meeting-settings', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    meeting_provider: provider,
                    custom_url: provider === 'custom' ? customUrl : undefined,
                    timezone,
                }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to save settings.');

            if (provider === 'google_meet') {
                await fetch('/api/paired/mentor/calendar-connect', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ enabled: calendarSync }),
                });
            }

            setSuccess('Meeting settings saved successfully.');
            setTimeout(() => setSuccess(''), 4000);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to save settings.');
        } finally {
            setSaving(false);
        }
    }

    if (loading) {
        return (
            <div className="fade-up">
                <div className="text-center text-sm text-text-2 py-12">Loading meeting settings...</div>
            </div>
        );
    }

    return (
        <div className="fade-up" style={{ display: 'flex', flexDirection: 'column', gap: '32px', maxWidth: '640px' }}>
            <div>
                <h1 className="text-3xl font-extrabold tracking-tight">Meeting Settings</h1>
                <p className="mt-2" style={{ color: 'var(--text-2)' }}>
                    Configure how you meet with your mentees.
                </p>
            </div>

            {error && <div className="alert alert-red">{error}</div>}
            {success && <div className="alert alert-green">{success}</div>}

            <div className="card card-p" style={{ display: 'flex', flexDirection: 'column', gap: '24px' }}>
                {/* Meeting provider */}
                <div>
                    <label className="field-label">Meeting Provider</label>
                    <div style={{ display: 'flex', flexDirection: 'column', gap: '8px', marginTop: '8px' }}>
                        {PROVIDERS.map(p => (
                            <label
                                key={p.value}
                                className="flex items-center gap-3 cursor-pointer rounded-lg p-3"
                                style={{
                                    border: `1px solid ${provider === p.value ? 'var(--purple)' : 'var(--border)'}`,
                                    background: provider === p.value ? 'var(--purple-bg)' : 'transparent',
                                }}
                            >
                                <input
                                    type="radio"
                                    name="provider"
                                    value={p.value}
                                    checked={provider === p.value}
                                    onChange={() => setProvider(p.value)}
                                    style={{ accentColor: 'var(--purple)' }}
                                />
                                <span className="font-medium text-sm">{p.label}</span>
                            </label>
                        ))}
                    </div>
                </div>

                {/* Custom URL input */}
                {provider === 'custom' && (
                    <div>
                        <label className="field-label" htmlFor="custom-url">Meeting URL</label>
                        <input
                            id="custom-url"
                            type="url"
                            className="field-input mt-1"
                            placeholder="https://meet.example.com/your-room"
                            value={customUrl}
                            onChange={e => setCustomUrl(e.target.value)}
                        />
                        <p className="text-xs mt-1" style={{ color: 'var(--text-3)' }}>
                            This link will be shared with mentees when they book a session.
                        </p>
                    </div>
                )}

                {/* Google Calendar sync toggle */}
                {provider === 'google_meet' && (
                    <div>
                        <label className="flex items-center gap-3 cursor-pointer">
                            <input
                                type="checkbox"
                                checked={calendarSync}
                                onChange={e => setCalendarSync(e.target.checked)}
                                style={{ accentColor: 'var(--purple)', width: '18px', height: '18px' }}
                            />
                            <div>
                                <span className="font-medium text-sm">Google Calendar Sync</span>
                                <p className="text-xs" style={{ color: 'var(--text-3)' }}>
                                    Automatically create Google Calendar events and generate Meet links for bookings.
                                </p>
                            </div>
                        </label>
                    </div>
                )}

                {/* Timezone */}
                <div>
                    <label className="field-label" htmlFor="timezone">Timezone</label>
                    <select
                        id="timezone"
                        className="field-input mt-1"
                        value={timezone}
                        onChange={e => setTimezone(e.target.value)}
                    >
                        {TIMEZONES.map(tz => (
                            <option key={tz} value={tz}>{tz.replace(/_/g, ' ')}</option>
                        ))}
                    </select>
                </div>

                {/* Save button */}
                <button
                    onClick={handleSave}
                    disabled={saving}
                    className="btn btn-purple"
                >
                    {saving ? 'Saving...' : 'Save Settings'}
                </button>
            </div>
        </div>
    );
}
