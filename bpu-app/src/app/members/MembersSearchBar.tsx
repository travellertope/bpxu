'use client';

import { useRouter, useSearchParams } from 'next/navigation';
import { useRef } from 'react';

export default function MembersSearchBar() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const debounce = useRef<ReturnType<typeof setTimeout> | null>(null);

  const handleChange = (val: string) => {
    if (debounce.current) clearTimeout(debounce.current);
    debounce.current = setTimeout(() => {
      const params = new URLSearchParams(searchParams?.toString() ?? '');
      if (val) {
        params.set('search', val);
      } else {
        params.delete('search');
      }
      router.replace(`/members?${params.toString()}`);
    }, 300);
  };

  return (
    <div className="relative hidden sm:flex items-center">
      <svg className="absolute left-3 text-gray-400" width={13} height={13} fill="none" stroke="currentColor" strokeWidth={2} viewBox="0 0 24 24">
        <circle cx={11} cy={11} r={8}/><path d="m21 21-4.35-4.35"/>
      </svg>
      <input
        type="text"
        defaultValue={searchParams?.get('search') ?? ''}
        onChange={e => handleChange(e.target.value)}
        placeholder="Search members…"
        className="pl-8 pr-3 py-1.5 rounded-lg text-xs font-medium focus:outline-none"
        style={{ border: '2px solid #1E2229', background: '#fff', width: 200 }}
      />
    </div>
  );
}
