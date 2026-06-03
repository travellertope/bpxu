'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';

export default function EmployerRegisterPage() {
    const router = useRouter();

    const [form, setForm] = useState({
        company_name: '',
        contact_name: '',
        email: '',
        password: '',
    });
    const [loading, setLoading] = useState(false);
    const [error, setError] = useState('');

    function set(field: keyof typeof form, value: string) {
        setForm(prev => ({ ...prev, [field]: value }));
    }

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setError('');
        setLoading(true);

        if (!form.company_name.trim()) { setError('Company name is required.'); setLoading(false); return; }
        if (!form.contact_name.trim()) { setError('Contact name is required.'); setLoading(false); return; }
        if (!form.email.trim()) { setError('Email is required.'); setLoading(false); return; }
        if (form.password.length < 8) { setError('Password must be at least 8 characters.'); setLoading(false); return; }

        try {
            const res = await fetch('/api/employer/register', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(form),
            });

            const data = await res.json();

            if (!res.ok || !data.success) {
                setError(data.error || 'Registration failed. Please try again.');
                setLoading(false);
                return;
            }

            router.push('/employer/jobs');
        } catch {
            setError('Something went wrong. Please try again.');
            setLoading(false);
        }
    }

    return (
        <div className="min-h-screen flex flex-col">
            <header className="topbar">
                <div className="topbar-inner">
                    <a href="/" className="topbar-brand"><img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="Black Professionals United" /></a>
                    <div className="flex items-center gap-3">
                        <a href="/employer" className="btn btn-ghost btn-sm">← Employer portal</a>
                    </div>
                </div>
            </header>

            <main className="flex-1 flex items-center justify-center p-6">
                <div className="w-full max-w-md fade-up">
                    {/* Brand */}
                    <div className="text-center mb-8">
                        <div className="inline-flex items-center gap-2 text-2xl font-extrabold tracking-tight">
                            <span className="text-brand">BPU</span>
                            <span className="text-text"> Employer Portal</span>
                        </div>
                        <p className="mt-2 text-sm text-text-2">Create your employer account</p>
                    </div>

                    <div className="card card-p space-y-5">
                        {error && (
                            <div className="alert alert-red text-sm">{error}</div>
                        )}

                        <div className="text-center space-y-1">
                            <h1 className="text-xl font-bold">Employer registration</h1>
                            <p className="text-sm text-text-2">
                                Fill in your details to start posting jobs.
                            </p>
                        </div>

                        <div className="divider" />

                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div>
                                <label htmlFor="company_name" className="field-label">Company name *</label>
                                <input
                                    id="company_name"
                                    type="text"
                                    className="field-input"
                                    placeholder="Acme Corp"
                                    value={form.company_name}
                                    onChange={e => set('company_name', e.target.value)}
                                    required
                                    disabled={loading}
                                />
                            </div>

                            <div>
                                <label htmlFor="contact_name" className="field-label">Contact name *</label>
                                <input
                                    id="contact_name"
                                    type="text"
                                    className="field-input"
                                    placeholder="Jane Smith"
                                    value={form.contact_name}
                                    onChange={e => set('contact_name', e.target.value)}
                                    required
                                    disabled={loading}
                                    autoComplete="name"
                                />
                            </div>

                            <div>
                                <label htmlFor="emp-email" className="field-label">Email address *</label>
                                <input
                                    id="emp-email"
                                    type="email"
                                    className="field-input"
                                    placeholder="hiring@company.com"
                                    value={form.email}
                                    onChange={e => set('email', e.target.value)}
                                    required
                                    disabled={loading}
                                    autoComplete="email"
                                />
                            </div>

                            <div>
                                <label htmlFor="emp-password" className="field-label">Password *</label>
                                <input
                                    id="emp-password"
                                    type="password"
                                    className="field-input"
                                    placeholder="Min. 8 characters"
                                    value={form.password}
                                    onChange={e => set('password', e.target.value)}
                                    required
                                    disabled={loading}
                                    autoComplete="new-password"
                                />
                            </div>

                            <button
                                type="submit"
                                className="btn btn-amber btn-lg w-full justify-center"
                                disabled={loading}
                            >
                                {loading ? 'Creating account…' : 'Create employer account'}
                            </button>
                        </form>

                        <p className="text-center text-sm text-text-2">
                            Already have an account?{' '}
                            <a href="/login?returnTo=/employer/jobs" className="font-semibold text-brand-dark hover:underline">
                                Sign in
                            </a>
                        </p>
                    </div>
                </div>
            </main>
        </div>
    );
}
