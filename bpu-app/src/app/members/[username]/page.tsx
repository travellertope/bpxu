import { getBPUSession } from '@/lib/auth';
import MemberProfileClient from './MemberProfileClient';
import { PublicMemberProfile } from './types';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

async function fetchMemberProfile(username: string): Promise<PublicMemberProfile | null> {
  try {
    const res = await fetch(
      `${WP_URL}/wp-json/bpu/v1/member/${encodeURIComponent(username)}`,
      { cache: 'no-store' }
    );
    if (!res.ok) return null;
    return await res.json();
  } catch {
    return null;
  }
}

export default async function MemberProfilePage({
  params,
}: {
  params: Promise<{ username: string }>;
}) {
  const { username } = await params;
  const [member, session] = await Promise.all([
    fetchMemberProfile(username),
    getBPUSession(),
  ]);

  /* Support both slug and numeric-ID URLs */
  const isOwner = session.authenticated && !!session.user && (
    session.user.username === username ||
    (member !== null && String(member.id) === username) ||
    session.user.username === member?.username
  );

  return (
    <MemberProfileClient
      member={member}
      username={username}
      isOwner={isOwner}
      viewer={session.user ?? null}
    />
  );
}
