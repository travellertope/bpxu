'use client';

import { Suspense, useState } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';

function LoginForm() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const returnTo = searchParams.get('returnTo') || '/';
  const loggedOut = searchParams.get('logged_out') === '1';

  const [username, setUsername] = useState('');
  const [password, setPassword] = useState('');
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState('');

  async function handleSubmit(e: React.FormEvent) {
    e.preventDefault();
    setError('');
    setLoading(true);

    try {
      const res = await fetch('/api/auth/login', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ username, password }),
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
    <main className="min-h-screen flex items-center justify-center p-6 bg-bg">
      <div className="w-full max-w-sm fade-up">

        {/* Brand */}
        <div className="text-center mb-8">
          <div className="inline-flex items-center gap-2 text-2xl font-extrabold tracking-tight">
            <span className="text-brand">BPU</span>
            <span className="text-text"> Portal</span>
          </div>
          <p className="mt-2 text-sm text-text-2">Black Professionals United</p>
        </div>

        <div className="card card-p space-y-5">

          {/* Signed out banner */}
          {loggedOut && (
            <div className="alert alert-green text-sm">
              You have been signed out.
            </div>
          )}

          {/* Error banner */}
          {error && (
            <div className="alert alert-red text-sm">
              {error}
            </div>
          )}

          <div className="text-center space-y-1">
            <h1 className="text-xl font-bold">Sign in to your account</h1>
            <p className="text-sm text-text-2">Use your BPU account credentials.</p>
          </div>

          <div className="divider" />

          <form onSubmit={handleSubmit} className="space-y-4">
            <div>
              <label htmlFor="username" className="field-label">Username or Email</label>
              <input
                id="username"
                type="text"
                autoComplete="username"
                className="field-input w-full"
                placeholder="Enter your username or email"
                value={username}
                onChange={e => setUsername(e.target.value)}
                required
                disabled={loading}
              />
            </div>

            <div>
              <label htmlFor="password" className="field-label">Password</label>
              <input
                id="password"
                type="password"
                autoComplete="current-password"
                className="field-input w-full"
                placeholder="Enter your password"
                value={password}
                onChange={e => setPassword(e.target.value)}
                required
                disabled={loading}
              />
            </div>

            <button
              type="submit"
              className="btn btn-amber btn-lg w-full justify-center"
              disabled={loading}
            >
              {loading ? 'Signing in…' : 'Sign in'}
            </button>
          </form>

          <p className="text-center text-sm text-text-2">
            No account?{' '}
            <a href={registerHref} className="font-semibold text-brand-dark hover:underline">
              Register free
            </a>
          </p>
        </div>

        <p className="mt-6 text-center text-xs text-text-3">
          Empowering Black professionals in the UK
        </p>
      </div>
    </main>
  );
}

export default function LoginPage() {
  return (
    <Suspense fallback={
      <main className="min-h-screen flex items-center justify-center bg-bg">
        <div className="text-sm text-text-2">Loading…</div>
      </main>
    }>
      <LoginForm />
    </Suspense>
  );
}
