'use client';

import { useEffect, useState } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';

declare global {
    interface Window {
        grecaptcha: {
            ready: (cb: () => void) => void;
            execute: (siteKey: string, opts: { action: string }) => Promise<string>;
        };
    }
}

const SITE_KEY = process.env.NEXT_PUBLIC_RECAPTCHA_SITE_KEY || '';

const STATS = [
    { val: '7,000+', label: 'Members' },
    { val: '30+',    label: 'Corporate Partners' },
    { val: '200+',   label: 'Vacancies' },
];

export default function LoginForm({ isPaired }: { isPaired?: boolean }) {
  const router = useRouter();
  const searchParams = useSearchParams();
  const returnTo  = searchParams.get('returnTo') || '/';
  const loggedOut = searchParams.get('logged_out') === '1';

  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [loading,  setLoading]  = useState(false);
  const [error,    setError]    = useState('');

  useEffect(() => {
    if (!SITE_KEY || document.getElementById('recaptcha-script')) return;
    const script = document.createElement('script');
    script.id  = 'recaptcha-script';
    script.src = `https://www.google.com/recaptcha/api.js?render=${SITE_KEY}`;
    script.async = true;
    document.head.appendChild(script);
  }, []);

  async function getRecaptchaToken(action: string): Promise<string> {
    if (!SITE_KEY || typeof window === 'undefined' || !window.grecaptcha) return '';
    return new Promise(resolve => {
        window.grecaptcha.ready(() => {
            window.grecaptcha.execute(SITE_KEY, { action }).then(resolve).catch(() => resolve(''));
        });
    });
  }

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError('');
    setLoading(true);
    try {
      const recaptcha_token = await getRecaptchaToken('login');
      const res  = await fetch('/api/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password, recaptcha_token }),
      });
      const data = await res.json();
      if (!res.ok || !data.success) {
        setError(data.error || 'Invalid username or password.');
        setLoading(false);
        return;
      }
      router.push(returnTo);
    } catch {
      setError('Something went wrong. Please try again.');
      setLoading(false);
    }
  }

  const registerHref = `/register${returnTo !== '/' ? `?returnTo=${encodeURIComponent(returnTo)}` : ''}`;

  return (
    <main className="min-h-screen flex">

      {/* ── Left panel: image + brand ─────────────────────────────── */}
      <div className="hidden lg:flex lg:w-[58%] relative flex-col overflow-hidden">
        <div
          className="absolute inset-0 bg-cover bg-center bg-no-repeat"
          style={{ backgroundImage: `url('https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?auto=format&fit=crop&w=1400&q=80')` }}
        />
        <div
          className="absolute inset-0"
          style={{ background: 'linear-gradient(145deg,rgba(0,0,0,0.88) 0%,rgba(200,16,46,0.40) 100%)' }}
        />
        <div className="relative z-10 flex flex-col justify-between h-full p-14">
          <img
            src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png"
            alt="Black Professionals United"
            className="h-10 w-auto self-start brightness-0 invert"
          />
          <div>
            <h2 className="text-5xl font-extrabold text-white leading-[1.1] mb-5 tracking-tight">
              Include.<br />Collaborate.<br />Grow.
            </h2>
            <p className="text-white/70 text-lg leading-relaxed mb-10 max-w-xs">
              The UK&apos;s home for Black professional growth, opportunities, and community.
            </p>
            <div className="flex gap-10">
              {STATS.map(s => (
                <div key={s.label}>
                  <p className="text-2xl font-bold text-white">{s.val}</p>
                  <p className="text-white/50 text-sm mt-0.5">{s.label}</p>
                </div>
              ))}
            </div>
          </div>
          <p className="text-white/25 text-xs">
            Photo: Christina @ wocintechchat.com / Unsplash
          </p>
        </div>
      </div>

      {/* ── Right panel: form ─────────────────────────────────────── */}
      <div className="flex-1 flex flex-col items-center justify-center p-8 bg-white">
        <div className="w-full max-w-sm fade-up">

          {/* Logo — mobile only */}
          <div className="lg:hidden text-center mb-8">
            <img
              src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png"
              alt="Black Professionals United"
              className="h-12 w-auto mx-auto mb-2"
            />
            <p className="text-xs text-text-3 font-medium uppercase tracking-widest">Member Portal</p>
          </div>

          <div className="mb-8">
            <h1 className="text-2xl font-bold text-text">Welcome back</h1>
            <p className="text-sm text-text-2 mt-1">Sign in to your BPU account</p>
          </div>

          {loggedOut && (
            <div className="alert alert-green text-sm mb-5">You have been signed out.</div>
          )}
          {error && (
            <div className="alert alert-red text-sm mb-5">{error}</div>
          )}

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label htmlFor="username" className="field-label">Username or Email</label>
              <input
                id="username" type="text" autoComplete="username"
                className="field-input w-full"
                placeholder="Enter your username or email"
                value={username} onChange={e => setUsername(e.target.value)}
                required disabled={loading}
              />
            </div>

            <div>
              <div className="flex items-center justify-between mb-1">
                <label htmlFor="password" className="field-label !mb-0">Password</label>
                <a
                  href="/forgot-password"
                  className="text-xs text-text-3 hover:text-brand hover:underline"
                >
                  Forgot password?
                </a>
              </div>
              <input
                id="password" type="password" autoComplete="current-password"
                className="field-input w-full"
                placeholder="••••••••"
                value={password} onChange={e => setPassword(e.target.value)}
                required disabled={loading}
              />
            </div>

            <button
              type="submit"
              className="btn btn-amber btn-lg w-full justify-center mt-2"
              disabled={loading}
            >
              {loading ? 'Signing in…' : 'Sign in →'}
            </button>
          </form>

          <p className="mt-6 text-center text-sm text-text-2">
            Don&apos;t have an account?{' '}
            <a href={registerHref} className="font-semibold text-brand hover:underline">
              Create one free
            </a>
          </p>

          {!isPaired && (
            <div className="mt-10 pt-6 border-t border-border">
              <p className="text-center text-xs text-text-3">
                Are you hiring?{' '}
                <a href="/employer" className="hover:underline">Employer portal →</a>
              </p>
            </div>
          )}
        </div>
      </div>

    </main>
  );
}
