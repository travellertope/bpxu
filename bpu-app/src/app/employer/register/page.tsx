'use client';

import { useState } from 'react';
import { useRouter } from 'next/navigation';

const PERKS = [
    '✓  Post jobs to 7,000+ Black professionals',
    '✓  Track impressions, clicks & applications',
    '✓  Inbound applications direct to your dashboard',
    '✓  Champion diversity & inclusion in your hiring',
];

export default function EmployerRegisterPage() {
    const router = useRouter();

    const [form, setForm] = useState({
        company_name: '',
        contact_name: '',
        email: '',
        password: '',
    });
    const [loading, setLoading] = useState(false);
    const [error,   setError]   = useState('');

    function set(field: keyof typeof form, value: string) {
        setForm(prev => ({ ...prev, [field]: value }));
    }

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        setError('');
        setLoading(true);

        if (!form.company_name.trim()) { setError('Company name is required.'); setLoading(false); return; }
        if (!form.contact_name.trim()) { setError('Contact name is required.'); setLoading(false); return; }
        if (!form.email.trim())        { setError('Email is required.');         setLoading(false); return; }
        if (form.password.length < 8)  { setError('Password must be at least 8 characters.'); setLoading(false); return; }

        try {
            const res  = await fetch('/api/employer/register', {
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
        <main className="min-h-screen flex">

            {/* ── Left panel: image + brand ───────────────────────────── */}
            <div className="hidden lg:flex lg:w-[52%] relative flex-col overflow-hidden">
                <div
                    className="absolute inset-0 bg-cover bg-center bg-no-repeat"
                    style={{ backgroundImage: `url('https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&w=1400&q=80')` }}
                />
                <div
                    className="absolute inset-0"
                    style={{ background: 'linear-gradient(150deg,rgba(0,0,0,0.88) 0%,rgba(200,16,46,0.38) 100%)' }}
                />

                <div className="relative z-10 flex flex-col justify-between h-full p-14">
                    <img
                        src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png"
                        alt="Black Professionals United"
                        className="h-10 w-auto self-start brightness-0 invert"
                    />

                    <div>
                        <h2 className="text-4xl font-extrabold text-white leading-[1.15] mb-4 tracking-tight">
                            Hire from the<br />best Black talent<br />in the UK.
                        </h2>
                        <p className="text-white/65 leading-relaxed mb-8 text-sm max-w-xs">
                            Partner with BPU to reach a community of 7,000+ skilled professionals actively looking for their next opportunity.
                        </p>
                        <div className="space-y-3">
                            {PERKS.map(p => (
                                <p key={p} className="text-white/80 text-sm">{p}</p>
                            ))}
                        </div>
                    </div>

                    <p className="text-white/25 text-xs">Photo: Unsplash / Amy Hirschi</p>
                </div>
            </div>

            {/* ── Right panel: form ───────────────────────────────────── */}
            <div className="flex-1 flex flex-col items-center justify-center p-8 bg-white">
                <div className="w-full max-w-md fade-up">

                    {/* Logo — mobile only */}
                    <div className="lg:hidden text-center mb-8">
                        <img
                            src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png"
                            alt="Black Professionals United"
                            className="h-12 w-auto mx-auto mb-2"
                        />
                        <p className="text-xs text-text-3 font-medium uppercase tracking-widest">Employer Portal</p>
                    </div>

                    <div className="mb-8">
                        <h1 className="text-2xl font-bold text-text">Create your employer account</h1>
                        <p className="text-sm text-text-2 mt-1">Start posting jobs to 7,000+ BPU members</p>
                    </div>

                    {error && (
                        <div className="alert alert-red text-sm mb-5">{error}</div>
                    )}

                    <form onSubmit={handleSubmit} className="space-y-4">
                        <div>
                            <label htmlFor="company_name" className="field-label">Company name *</label>
                            <input
                                id="company_name" type="text"
                                className="field-input"
                                placeholder="Acme Corp"
                                value={form.company_name} onChange={e => set('company_name', e.target.value)}
                                required disabled={loading}
                            />
                        </div>

                        <div>
                            <label htmlFor="contact_name" className="field-label">Your name *</label>
                            <input
                                id="contact_name" type="text"
                                className="field-input"
                                placeholder="Jane Smith"
                                value={form.contact_name} onChange={e => set('contact_name', e.target.value)}
                                required disabled={loading} autoComplete="name"
                            />
                        </div>

                        <div>
                            <label htmlFor="emp-email" className="field-label">Work email *</label>
                            <input
                                id="emp-email" type="email"
                                className="field-input"
                                placeholder="hiring@company.com"
                                value={form.email} onChange={e => set('email', e.target.value)}
                                required disabled={loading} autoComplete="email"
                            />
                        </div>

                        <div>
                            <label htmlFor="emp-password" className="field-label">Password *</label>
                            <input
                                id="emp-password" type="password"
                                className="field-input"
                                placeholder="Min. 8 characters"
                                value={form.password} onChange={e => set('password', e.target.value)}
                                required disabled={loading} autoComplete="new-password"
                            />
                        </div>

                        <button
                            type="submit"
                            className="btn btn-amber btn-lg w-full justify-center mt-2"
                            disabled={loading}
                        >
                            {loading ? 'Creating account…' : 'Create employer account →'}
                        </button>
                    </form>

                    <p className="mt-6 text-center text-sm text-text-2">
                        Already have an account?{' '}
                        <a href="/login?returnTo=/employer/jobs" className="font-semibold text-brand hover:underline">
                            Sign in
                        </a>
                    </p>

                    <div className="mt-8 pt-6 border-t border-border">
                        <p className="text-center text-xs text-text-3">
                            Looking for a job?{' '}
                            <a href="/register" className="hover:underline">Create a member account →</a>
                        </p>
                    </div>
                </div>
            </div>

        </main>
    );
}
