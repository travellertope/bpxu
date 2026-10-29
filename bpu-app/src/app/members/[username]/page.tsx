import { getBPUSession } from '@/lib/auth';
import MemberProfileClient from './MemberProfileClient';
import { PublicMemberProfile } from './types';

const WP_URL = process.env.NEXT_PUBLIC_WP_URL || 'https://blackprofessionals.uk';

async function fetchMemberProfile(username: string): Promise<PublicMemberProfile | null> {
  try {
    const res = await fetch(
      `${WP_URL}/wp-json/bpu/v1/member/${encodeURIComponent(username)}`,
      { next: { revalidate: 300 } }
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

  const isOwner = session.authenticated && session.user?.username === username;

  return (
    <MemberProfileClient
      member={member}
      username={username}
      isOwner={isOwner}
      viewer={session.user ?? null}
    />
  );
}
