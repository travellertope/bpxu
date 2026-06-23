import { Suspense } from 'react';
import { headers } from 'next/headers';
import LoginForm from './LoginForm';

export default async function LoginPage() {
  const hdrs = await headers();
  const host = hdrs.get('host') || '';
  const isPaired = host.includes('pairedbybpu.uk');

  return (
    <Suspense fallback={
      <main className="min-h-screen flex items-center justify-center bg-bg">
        <div className="text-sm text-text-2">Loading…</div>
      </main>
    }>
      <LoginForm isPaired={isPaired} />
    </Suspense>
  );
}
