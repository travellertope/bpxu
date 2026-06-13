'use client';

import { useState } from 'react';

export default function ChangePasswordForm() {
    const [currentPassword, setCurrentPassword] = useState('');
    const [newPassword, setNewPassword] = useState('');
    const [confirmPassword, setConfirmPassword] = useState('');
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState('');
    const [success, setSuccess] = useState('');

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setError('');
        setSuccess('');

        if (!currentPassword.trim()) {
            setError('Please enter your current password.');
            return;
        }
        if (newPassword.length < 8) {
            setError('New password must be at least 8 characters.');
            return;
        }
        if (newPassword !== confirmPassword) {
            setError('New passwords do not match.');
            return;
        }

        setSaving(true);
        try {
            const res = await fetch('/api/paired/account/change-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ current_password: currentPassword, new_password: newPassword }),
            });
            const data = await res.json().catch(() => ({}));
            if (!res.ok) {
                setError(data.message || data.error || 'Failed to change password.');
                return;
            }
            setSuccess('Password changed successfully.');
            setTimeout(() => setSuccess(''), 4000);
            setCurrentPassword('');
            setNewPassword('');
            setConfirmPassword('');
        } catch {
            setError('Something went wrong. Please try again.');
        } finally {
            setSaving(false);
        }
    }

    return (
        <div className="card card-p" style={{ display: 'flex', flexDirection: 'column', gap: '20px' }}>
            {error && <div className="alert alert-red">{error}</div>}
            {success && <div className="alert alert-green">{success}</div>}

            <form onSubmit={handleSubmit} style={{ display: 'flex', flexDirection: 'column', gap: '16px' }}>
                <div>
                    <label className="field-label" htmlFor="current-pw">Current Password</label>
                    <input
                        id="current-pw"
                        type="password"
                        className="field-input mt-1"
                        value={currentPassword}
                        onChange={e => setCurrentPassword(e.target.value)}
                        required
                        autoComplete="current-password"
                    />
                </div>

                <div>
                    <label className="field-label" htmlFor="new-pw">New Password</label>
                    <input
                        id="new-pw"
                        type="password"
                        className="field-input mt-1"
                        value={newPassword}
                        onChange={e => setNewPassword(e.target.value)}
                        required
                        minLength={8}
                        autoComplete="new-password"
                    />
                    <p className="text-xs mt-1" style={{ color: 'var(--text-3)' }}>Minimum 8 characters.</p>
                </div>

                <div>
                    <label className="field-label" htmlFor="confirm-pw">Confirm New Password</label>
                    <input
                        id="confirm-pw"
                        type="password"
                        className="field-input mt-1"
                        value={confirmPassword}
                        onChange={e => setConfirmPassword(e.target.value)}
                        required
                        autoComplete="new-password"
                    />
                </div>

                <button type="submit" className="btn btn-purple" disabled={saving}>
                    {saving ? 'Updating...' : 'Update Password'}
                </button>
            </form>
        </div>
    );
}
