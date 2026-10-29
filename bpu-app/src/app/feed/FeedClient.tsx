'use client';

import React, { useState, useEffect, useCallback, useRef } from 'react';
import Link from 'next/link';
import { BPUUser } from '@/lib/auth';

/* ── colour tokens ── */
const OCHRE  = '#C5491F';
const SLATE  = '#111317';
const PAPER  = '#F3ECE0';
const BORDER = '#1E2229';

/* ── types ── */
export interface FeedPost {
  id: number;
  author_id: number;
  author_username: string;
  author_name: string;
  author_title: string;
  author_photo: string | null;
  timestamp: string;
  created_at: string;
  category: string;
  content: string;
  hashtags: string[];
  reactions: { celebrate: number; insightful: number; support: number };
  user_reaction: 'celebrate' | 'insightful' | 'support' | null;
  poll: {
    options: { text: string; votes: number }[];
    total_votes: number;
    user_voted_index: number | null;
  } | null;
  comments: { author_id: number; author_name: string; author_photo: string | null; text: string; created_at: string }[];
  is_bookmarked: boolean;
  is_own: boolean;
}

type FeedFilter = 'connections' | 'all' | 'mine' | 'saved';
type CategoryFilter = 'ALL' | 'Placement News' | 'Milestone' | 'Technical Insight' | 'Mentorship' | 'Interactive Poll';
type SortOption = 'recent' | 'top';

interface Props {
  viewer: BPUUser;
  initialPosts: FeedPost[];
}

/* ── helpers ── */
function avatar(photo: string | null, name: string) {
  return photo || `https://www.gravatar.com/avatar/${encodeURIComponent(name)}?s=200&d=mp`;
}

function formatContent(text: string) {
  return text
    .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
    .replace(/@([A-Z][a-z]+\s[A-Z][a-z]+)/g, '<span style="color:' + OCHRE + ';font-weight:700">@$1</span>')
    .replace(/\n/g, '<br />');
}

/* ── Toast ── */
function Toast({ msg, onClose }: { msg: string; onClose: () => void }) {
  useEffect(() => { const t = setTimeout(onClose, 3200); return () => clearTimeout(t); }, [onClose]);
  return (
    <div className="fixed bottom-6 right-6 z-50 flex items-center gap-3 p-4 rounded-lg max-w-sm"
      style={{ background: SLATE, border: `3px solid ${BORDER}`, boxShadow: `8px 8px 0 ${BORDER}`, color: PAPER }}>
      <span style={{ color: OCHRE, fontSize: '1.4rem' }}>✓</span>
      <p className="text-xs font-bold" style={{ color: '#d1d5db' }}>{msg}</p>
    </div>
  );
}

/* ── Poll bar ── */
function PollBox({ post, onVote }: { post: FeedPost; onVote: (idx: number) => void }) {
  const p = post.poll!;
  return (
    <div className="rounded-xl p-4 space-y-2 my-2"
      style={{ background: `${PAPER}80`, border: `2px solid ${BORDER}` }}>
      <div className="flex justify-between items-center text-xs font-black uppercase" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
        <span>Community Poll</span>
        <span className="font-mono text-gray-500">{p.total_votes} votes</span>
      </div>
      {p.options.map((opt, i) => {
        const pct = p.total_votes > 0 ? Math.round((opt.votes / p.total_votes) * 100) : 0;
        const voted = p.user_voted_index === i;
        const hasVoted = p.user_voted_index !== null;
        return (
          <button key={i} onClick={() => !hasVoted && onVote(i)} disabled={hasVoted}
            className="w-full text-left p-2.5 rounded-lg text-xs font-bold relative overflow-hidden"
            style={{
              border: `2px solid ${BORDER}`,
              background: voted ? SLATE : '#fff',
              color: voted ? '#fff' : SLATE,
              cursor: hasVoted ? 'default' : 'pointer',
            }}>
            {hasVoted && (
              <div className="absolute inset-y-0 left-0 opacity-20" style={{ width: `${pct}%`, background: OCHRE }} />
            )}
            <div className="relative flex justify-between">
              <span>{opt.text}{voted ? ' ✓' : ''}</span>
              {hasVoted && <span className="font-mono font-bold">{pct}%</span>}
            </div>
          </button>
        );
      })}
    </div>
  );
}

/* ── Single Post Card ── */
function PostCard({
  post, viewerId, onReact, onComment, onBookmark, onVote,
}: {
  post: FeedPost;
  viewerId: number;
  onReact: (id: number, type: 'celebrate' | 'insightful' | 'support') => void;
  onComment: (id: number, text: string) => void;
  onBookmark: (id: number) => void;
  onVote: (id: number, idx: number) => void;
}) {
  const [showComments, setShowComments] = useState(false);
  const [commentText, setCommentText] = useState('');
  const [submitting, setSubmitting] = useState(false);

  const reactions = [
    {
      key: 'celebrate' as const, label: 'Celebrate',
      icon: (active: boolean) => (
        <svg width="14" height="14" viewBox="0 0 24 24" fill={active ? 'currentColor' : 'none'} stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
          <path d="M14 9V5a3 3 0 0 0-3-3l-4 9v11h11.28a2 2 0 0 0 2-1.7l1.38-9a2 2 0 0 0-2-2.3H14z"/>
          <path d="M7 22H4a2 2 0 0 1-2-2v-7a2 2 0 0 1 2-2h3"/>
        </svg>
      ),
    },
    {
      key: 'insightful' as const, label: 'Insightful',
      icon: (active: boolean) => (
        <svg width="14" height="14" viewBox="0 0 24 24" fill={active ? 'currentColor' : 'none'} stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
          <line x1="9" y1="18" x2="15" y2="18"/><line x1="10" y1="22" x2="14" y2="22"/>
          <path d="M15.09 14c.18-.98.65-1.74 1.41-2.5A4.65 4.65 0 0 0 18 8 6 6 0 0 0 6 8c0 1 .23 2.23 1.5 3.5A4.61 4.61 0 0 1 8.91 14"/>
        </svg>
      ),
    },
    {
      key: 'support' as const, label: 'Support',
      icon: (active: boolean) => (
        <svg width="14" height="14" viewBox="0 0 24 24" fill={active ? 'currentColor' : 'none'} stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
        </svg>
      ),
    },
  ];

  return (
    <article className="rounded-2xl p-6 space-y-4"
      style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}>

      {/* Author header */}
      <div className="flex items-start justify-between gap-3 pb-3 border-b-2" style={{ borderColor: `${BORDER}30` }}>
        <div className="flex items-center gap-3">
          <Link href={`/members/${post.author_username}`}
            className="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0 block"
            style={{ border: `2px solid ${BORDER}` }}>
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img src={avatar(post.author_photo, post.author_name)} alt={post.author_name} className="w-full h-full object-cover" />
          </Link>
          <div>
            <div className="flex items-center gap-2 flex-wrap">
              <Link href={`/members/${post.author_username}`}
                className="font-black text-base uppercase hover:underline"
                style={{ fontFamily: 'Plus Jakarta Sans, sans-serif', color: SLATE }}>
                {post.author_name}
              </Link>
              {post.is_own && (
                <span className="font-mono text-[9px] px-1.5 py-0.5 rounded uppercase" style={{ background: SLATE, color: PAPER }}>You</span>
              )}
            </div>
            <p className="font-bold text-xs text-gray-600 leading-tight">{post.author_title}</p>
            <span className="font-mono text-[10px] font-bold uppercase block mt-0.5" style={{ color: OCHRE }}>
              {post.timestamp}
            </span>
          </div>
        </div>
        <span className="font-black text-[10px] uppercase px-2.5 py-1 rounded flex-shrink-0"
          style={{ border: `1px solid ${SLATE}`, background: PAPER, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
          {post.category}
        </span>
      </div>

      {/* Content */}
      <div className="text-sm font-medium text-gray-800 leading-relaxed"
        dangerouslySetInnerHTML={{ __html: formatContent(post.content) }} />

      {/* Poll */}
      {post.poll && <PollBox post={post} onVote={idx => onVote(post.id, idx)} />}

      {/* Hashtags */}
      {post.hashtags.length > 0 && (
        <div className="flex flex-wrap gap-1.5">
          {post.hashtags.map(h => (
            <span key={h} className="font-mono text-[11px] font-bold" style={{ color: OCHRE }}>{h}</span>
          ))}
        </div>
      )}

      {/* Reaction + action bar */}
      <div className="pt-3 border-t-2 flex flex-wrap items-center justify-between gap-2" style={{ borderColor: `${BORDER}20` }}>
        <div className="flex items-center gap-1.5 flex-wrap">
          {reactions.map(r => (
            <button key={r.key}
              onClick={() => onReact(post.id, r.key)}
              className="px-2.5 py-1.5 rounded-lg text-xs font-black uppercase flex items-center gap-1.5"
              style={{
                border: `2px solid ${BORDER}`,
                background: post.user_reaction === r.key ? (r.key === 'celebrate' ? OCHRE : r.key === 'insightful' ? SLATE : '#166534') : `${PAPER}80`,
                color: post.user_reaction === r.key ? '#fff' : SLATE,
                fontFamily: 'Plus Jakarta Sans, sans-serif',
              }}>
              {r.icon(post.user_reaction === r.key)} <span>{post.reactions[r.key]}</span>
            </button>
          ))}
        </div>
        <div className="flex items-center gap-2">
          <button onClick={() => setShowComments(s => !s)}
            className="px-3 py-1.5 rounded-lg text-xs font-bold uppercase flex items-center gap-1.5"
            style={{ border: `2px solid ${BORDER}`, background: showComments ? PAPER : '#fff', fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
            {post.comments.length}
          </button>
          <button onClick={() => onBookmark(post.id)}
            className="p-1.5 rounded-lg"
            style={{ border: `2px solid ${BORDER}`, background: post.is_bookmarked ? OCHRE : '#fff', color: post.is_bookmarked ? '#fff' : SLATE }}
            title="Bookmark">
            <svg width="14" height="14" viewBox="0 0 24 24" fill={post.is_bookmarked ? 'currentColor' : 'none'} stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M19 21l-7-5-7 5V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2z"/></svg>
          </button>
        </div>
      </div>

      {/* Comments section */}
      {showComments && (
        <div className="pt-3 border-t space-y-3 p-3 rounded-xl" style={{ background: `${PAPER}50`, border: `2px solid ${BORDER}` }}>
          {post.comments.map((c, i) => (
            <div key={i} className="p-2.5 rounded-lg text-xs" style={{ background: '#fff', border: `1px solid ${BORDER}` }}>
              <span className="font-black uppercase block" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>{c.author_name}</span>
              <p className="font-medium text-gray-700 mt-0.5">{c.text}</p>
            </div>
          ))}
          <form onSubmit={async e => {
            e.preventDefault();
            if (!commentText.trim()) return;
            setSubmitting(true);
            await onComment(post.id, commentText.trim());
            setCommentText('');
            setSubmitting(false);
          }} className="flex gap-2">
            <input
              value={commentText} onChange={e => setCommentText(e.target.value)}
              placeholder="Write a reply…" required
              className="flex-grow p-2 rounded-lg text-xs font-medium focus:outline-none"
              style={{ border: `2px solid ${BORDER}` }} />
            <button type="submit" disabled={submitting}
              className="px-3 py-2 rounded-lg text-xs font-black uppercase text-white"
              style={{ background: SLATE, border: `2px solid ${BORDER}`, opacity: submitting ? 0.7 : 1, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
              Reply
            </button>
          </form>
        </div>
      )}
    </article>
  );
}

/* ════════════════════════════════════════════════════════════ */
export default function FeedClient({ viewer, initialPosts }: Props) {
  const [posts, setPosts] = useState<FeedPost[]>(initialPosts);
  const [filter, setFilter] = useState<FeedFilter>('connections');
  const [category, setCategory] = useState<CategoryFilter>('ALL');
  const [sort, setSort] = useState<SortOption>('recent');
  const [search, setSearch] = useState('');
  const [hashtag, setHashtag] = useState('');
  const [loading, setLoading] = useState(false);
  const [page, setPage] = useState(1);
  const [hasMore, setHasMore] = useState(false);
  const [toast, setToast] = useState<string | null>(null);

  /* Composer */
  const [showComposer, setShowComposer] = useState(false);
  const [postContent, setPostContent] = useState('');
  const [postCategory, setPostCategory] = useState<string>('Placement News');
  const [postHashtags, setPostHashtags] = useState('');
  const [pollOptions, setPollOptions] = useState(['', '']);
  const [postSubmitting, setPostSubmitting] = useState(false);

  const searchDebounce = useRef<ReturnType<typeof setTimeout> | null>(null);

  const openToast = (msg: string) => setToast(msg);

  const fetchPosts = useCallback(async (
    f: FeedFilter, cat: CategoryFilter, s: SortOption, q: string, htag: string, pg: number, append = false
  ) => {
    setLoading(true);
    try {
      const params = new URLSearchParams({ filter: f, sort: s, page: String(pg), per_page: '10' });
      if (cat !== 'ALL') params.set('category', cat);
      if (q) params.set('search', q);
      if (htag) params.set('hashtag', htag);
      const res = await fetch(`/api/feed?${params}`);
      const data = await res.json();
      if (res.ok) {
        const fetched: FeedPost[] = data.posts ?? [];
        setPosts(prev => append ? [...prev, ...fetched] : fetched);
        setHasMore(pg < (data.pages ?? 1));
      }
    } finally {
      setLoading(false);
    }
  }, []);

  // Re-fetch when filter/sort/category/hashtag changes (not search — debounced separately)
  useEffect(() => {
    setPage(1);
    setHashtag('');
    fetchPosts(filter, category, sort, search, '', 1);
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [filter, category, sort]);

  const handleSearch = (val: string) => {
    setSearch(val);
    if (searchDebounce.current) clearTimeout(searchDebounce.current);
    searchDebounce.current = setTimeout(() => {
      setPage(1);
      fetchPosts(filter, category, sort, val, hashtag, 1);
    }, 400);
  };

  const handleHashtagClick = (htag: string) => {
    setHashtag(htag);
    setPage(1);
    fetchPosts(filter, category, sort, search, htag, 1);
  };

  const loadMore = () => {
    const next = page + 1;
    setPage(next);
    fetchPosts(filter, category, sort, search, hashtag, next, true);
  };

  /* ── Interactions ── */
  const handleReact = async (postId: number, type: 'celebrate' | 'insightful' | 'support') => {
    // Optimistic update
    setPosts(prev => prev.map(p => {
      if (p.id !== postId) return p;
      const reactions = { ...p.reactions };
      const prev_reaction = p.user_reaction;
      if (prev_reaction) reactions[prev_reaction] = Math.max(0, reactions[prev_reaction] - 1);
      const new_reaction = prev_reaction === type ? null : type;
      if (new_reaction) reactions[new_reaction] = reactions[new_reaction] + 1;
      return { ...p, reactions, user_reaction: new_reaction };
    }));
    try {
      await fetch(`/api/feed/${postId}?action=react`, {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ type }),
      });
    } catch { /* optimistic stays */ }
  };

  const handleComment = async (postId: number, text: string) => {
    try {
      const res = await fetch(`/api/feed/${postId}?action=comment`, {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ text }),
      });
      const data = await res.json();
      if (res.ok && data.comment) {
        setPosts(prev => prev.map(p =>
          p.id === postId ? { ...p, comments: [...p.comments, data.comment] } : p
        ));
        openToast('Comment posted!');
      }
    } catch { /* ignore */ }
  };

  const handleBookmark = async (postId: number) => {
    setPosts(prev => prev.map(p =>
      p.id === postId ? { ...p, is_bookmarked: !p.is_bookmarked } : p
    ));
    try {
      const res = await fetch(`/api/feed/${postId}?action=bookmark`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: '{}' });
      const data = await res.json();
      if (res.ok) openToast(data.action === 'added' ? 'Post saved!' : 'Post removed from saved.');
    } catch { /* optimistic stays */ }
  };

  const handleVote = async (postId: number, optionIndex: number) => {
    try {
      const res = await fetch(`/api/feed/${postId}?action=vote`, {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ option_index: optionIndex }),
      });
      const data = await res.json();
      if (res.ok && data.poll) {
        setPosts(prev => prev.map(p =>
          p.id === postId ? { ...p, poll: data.poll } : p
        ));
        openToast('Vote recorded!');
      }
    } catch { /* ignore */ }
  };

  /* ── Create Post ── */
  const handleCreatePost = async (e: React.FormEvent) => {
    e.preventDefault();
    if (postContent.trim().length < 10) { openToast('Post must be at least 10 characters.'); return; }
    setPostSubmitting(true);
    try {
      const hashtags = postHashtags.split(/\s+/).filter(h => h.startsWith('#'));
      const poll = postCategory === 'Interactive Poll'
        ? { options: pollOptions.filter(o => o.trim()).map(o => ({ text: o.trim() })) }
        : null;
      if (poll && poll.options.length < 2) { openToast('Add at least 2 poll options.'); setPostSubmitting(false); return; }
      const res = await fetch('/api/feed', {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ content: postContent.trim(), category: postCategory, hashtags, poll }),
      });
      const data = await res.json();
      if (res.ok) {
        setPosts(prev => [data, ...prev]);
        setShowComposer(false);
        setPostContent('');
        setPostHashtags('');
        setPollOptions(['', '']);
        openToast('Update shared with your network!');
      } else {
        openToast(data.error || 'Could not post.');
      }
    } finally {
      setPostSubmitting(false);
    }
  };

  const CATEGORIES: CategoryFilter[] = ['ALL', 'Placement News', 'Milestone', 'Technical Insight', 'Mentorship', 'Interactive Poll'];

  const FILTER_TABS: { key: FeedFilter; label: string; icon: string }[] = [
    { key: 'connections', label: 'Connections', icon: '👥' },
    { key: 'all', label: 'All Community', icon: '🌍' },
    { key: 'mine', label: 'My Updates', icon: '✏️' },
    { key: 'saved', label: 'Saved', icon: '🔖' },
  ];

  const TRENDING = ['#ScotlandTech2026', '#ExperiencedTalent', '#BPUInternSpotlight', '#CloudArchitecture', '#DEIStrategy'];

  return (
    <div className="min-h-screen" style={{ background: PAPER, fontFamily: '"DM Sans", sans-serif', color: SLATE }}>

      {/* ── Topbar ── */}
      <header className="topbar" style={{ borderBottom: `2px solid ${BORDER}` }}>
        <div className="topbar-inner">
          <a href="/" className="topbar-brand">
            {/* eslint-disable-next-line @next/next/no-img-element */}
            <img src="https://blackprofessionals.uk/wp-content/uploads/2025/03/bpu_logo-.png" alt="Black Professionals UK" />
          </a>
          <nav className="flex items-center gap-3">
            <a href="/members" className="btn btn-ghost btn-sm">Directory</a>
            <a href="/passport" className="btn btn-ghost btn-sm">Passports</a>
            <a href="/dashboard" className="btn btn-ghost btn-sm">Dashboard</a>
            {viewer && (
              <a href={`/members/${encodeURIComponent(viewer.username)}`} className="btn btn-ghost btn-sm">My Profile</a>
            )}
          </nav>
        </div>
      </header>

      {/* ── Main content ── */}
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

      {/* Page header */}
      <div className="mb-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
          <h1 className="font-black text-2xl uppercase" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
            Network Feed
          </h1>
          <p className="text-xs font-bold text-gray-500 mt-1">Updates from your connections and the BPU community</p>
        </div>
        <div className="flex items-center gap-3">
          <input
            value={search} onChange={e => handleSearch(e.target.value)}
            placeholder="Search posts, tags…"
            className="px-3 py-2 rounded-xl text-xs font-medium focus:outline-none"
            style={{ border: `3px solid ${BORDER}`, background: '#fff', width: 200 }} />
          <button
            onClick={() => setShowComposer(true)}
            className="px-5 py-2.5 rounded-xl text-xs font-black uppercase text-white flex items-center gap-2"
            style={{ background: OCHRE, border: `3px solid ${BORDER}`, boxShadow: `4px 4px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
            + Post Update
          </button>
        </div>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">

        {/* LEFT sidebar */}
        <aside className="lg:col-span-3 space-y-5">

          {/* Viewer card */}
          <div className="rounded-2xl p-5 space-y-4" style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}>
            <div className="flex items-center gap-3 pb-3 border-b-2" style={{ borderColor: BORDER }}>
              <div className="w-12 h-12 rounded-xl overflow-hidden flex-shrink-0" style={{ border: `2px solid ${BORDER}` }}>
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img src={avatar(null, viewer.display_name || viewer.username || 'User')} alt={viewer.display_name || ''} className="w-full h-full object-cover" />
              </div>
              <div className="overflow-hidden">
                <h3 className="font-black text-sm uppercase truncate" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                  {viewer.display_name || viewer.username}
                </h3>
                <p className="text-xs font-bold text-gray-500 truncate">{viewer.email}</p>
              </div>
            </div>
            <Link href="/profile" className="block w-full text-center py-2 rounded-xl font-black text-xs uppercase text-white"
              style={{ background: SLATE, border: `3px solid ${BORDER}`, boxShadow: `3px 3px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
              View My Profile ↗
            </Link>
          </div>

          {/* Feed tabs */}
          <div className="rounded-2xl p-4 space-y-2" style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}>
            <span className="block text-[10px] text-gray-500 font-mono font-bold uppercase tracking-wider px-2 mb-1">Feed Views</span>
            {FILTER_TABS.map(t => (
              <button key={t.key} onClick={() => setFilter(t.key)}
                className="w-full text-left p-3 rounded-xl flex items-center gap-2 text-xs font-black uppercase"
                style={{
                  border: `2px solid ${BORDER}`,
                  background: filter === t.key ? SLATE : '#fff',
                  color: filter === t.key ? PAPER : SLATE,
                  fontFamily: 'Plus Jakarta Sans, sans-serif',
                }}>
                <span>{t.icon}</span> {t.label}
              </button>
            ))}
          </div>

          {/* Trending */}
          <div className="rounded-2xl p-5 space-y-3" style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}>
            <h4 className="font-black text-xs uppercase pb-2 border-b-2 flex items-center gap-1.5" style={{ borderColor: BORDER, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
              # Trending Topics
            </h4>
            <div className="space-y-2">
              {TRENDING.map(h => (
                <button key={h} onClick={() => handleHashtagClick(h)}
                  className="block text-left w-full text-xs font-bold font-mono hover:underline"
                  style={{ color: hashtag === h ? OCHRE : SLATE }}>
                  {h}
                </button>
              ))}
              {hashtag && (
                <button onClick={() => { setHashtag(''); fetchPosts(filter, category, sort, search, '', 1); }}
                  className="text-[10px] font-mono font-bold uppercase" style={{ color: OCHRE }}>
                  ✕ Clear filter
                </button>
              )}
            </div>
          </div>
        </aside>

        {/* CENTER feed */}
        <div className="lg:col-span-6 space-y-5">

          {/* Quick composer */}
          <div className="rounded-2xl p-5 space-y-4" style={{ background: '#fff', border: `4px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}>
            <div className="flex items-center gap-3">
              <div className="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0" style={{ border: `2px solid ${BORDER}` }}>
                {/* eslint-disable-next-line @next/next/no-img-element */}
                <img src={avatar(null, viewer.display_name || 'User')} alt="" className="w-full h-full object-cover" />
              </div>
              <button onClick={() => setShowComposer(true)}
                className="flex-grow text-left p-3 rounded-xl text-xs font-bold text-gray-500 hover:bg-gray-50 transition-colors"
                style={{ border: `2px solid ${BORDER}`, background: `${PAPER}60` }}>
                Share a milestone, placement update, or insight…
              </button>
            </div>
            <div className="flex flex-wrap items-center gap-2 pt-2 border-t-2" style={{ borderColor: `${BORDER}30` }}>
              {['Placement News', 'Milestone', 'Technical Insight', 'Interactive Poll'].map(cat => (
                <button key={cat} onClick={() => { setPostCategory(cat); setShowComposer(true); }}
                  className="px-2.5 py-1.5 rounded-lg border text-xs font-black uppercase"
                  style={{ border: `2px solid ${BORDER}`, background: `${PAPER}50`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                  {cat === 'Placement News' ? '💼' : cat === 'Milestone' ? '🏆' : cat === 'Technical Insight' ? '💻' : '📊'} {cat.split(' ')[0]}
                </button>
              ))}
              <button onClick={() => setShowComposer(true)}
                className="ml-auto px-4 py-1.5 rounded-lg text-xs font-black uppercase text-white"
                style={{ background: OCHRE, border: `2px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                Post ↗
              </button>
            </div>
          </div>

          {/* Category + sort */}
          <div className="rounded-2xl p-3 flex flex-col sm:flex-row justify-between items-center gap-3"
            style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `3px 3px 0 ${BORDER}` }}>
            <div className="flex flex-wrap items-center gap-1.5">
              {CATEGORIES.map(cat => (
                <button key={cat} onClick={() => setCategory(cat)}
                  className="px-3 py-1 rounded-lg text-xs font-black uppercase"
                  style={{
                    border: `2px solid ${BORDER}`,
                    background: category === cat ? OCHRE : PAPER,
                    color: category === cat ? '#fff' : SLATE,
                    fontFamily: 'Plus Jakarta Sans, sans-serif',
                  }}>
                  {cat === 'ALL' ? 'All' : cat === 'Interactive Poll' ? 'Polls' : cat}
                </button>
              ))}
            </div>
            <div className="flex items-center gap-2 text-xs font-black uppercase border-t sm:border-t-0 sm:border-l-2 pt-2 sm:pt-0 sm:pl-3 w-full sm:w-auto" style={{ borderColor: BORDER }}>
              <span className="text-gray-500">Sort:</span>
              <select value={sort} onChange={e => setSort(e.target.value as SortOption)}
                className="bg-transparent font-black text-xs uppercase cursor-pointer focus:outline-none" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                <option value="recent">Most Recent</option>
                <option value="top">Top Engagement</option>
              </select>
            </div>
          </div>

          {/* Feed label */}
          <div className="flex items-center justify-between border-b-2 pb-2" style={{ borderColor: BORDER }}>
            <span className="font-black text-sm uppercase flex items-center gap-2" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
              {hashtag ? `# ${hashtag}` : search ? `Search: "${search}"` : FILTER_TABS.find(t => t.key === filter)?.label + ' Feed'}
            </span>
            <button onClick={() => { setFilter('connections'); setCategory('ALL'); setSearch(''); setHashtag(''); fetchPosts('connections', 'ALL', sort, '', '', 1); }}
              className="text-[10px] font-mono font-bold uppercase" style={{ color: OCHRE }}>
              Refresh ↺
            </button>
          </div>

          {/* Posts */}
          {loading && posts.length === 0 ? (
            <div className="text-center py-12">
              <div className="inline-block w-8 h-8 rounded-full border-4 border-t-transparent animate-spin" style={{ borderColor: `${OCHRE} transparent ${OCHRE} ${OCHRE}` }} />
            </div>
          ) : posts.length === 0 ? (
            <div className="rounded-2xl p-8 text-center space-y-3" style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}>
              <p className="font-black text-xl uppercase" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>No updates yet</p>
              <p className="text-xs font-bold text-gray-500">
                {filter === 'connections' ? 'Connect with peers to see their updates here.' :
                 filter === 'saved' ? 'Bookmark posts to save them here.' :
                 'No posts match your current filters.'}
              </p>
              {filter === 'connections' && (
                <button onClick={() => setFilter('all')}
                  className="px-5 py-2 rounded-xl font-black text-xs uppercase text-white"
                  style={{ background: SLATE, border: `3px solid ${BORDER}`, boxShadow: `3px 3px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                  Browse All Community →
                </button>
              )}
            </div>
          ) : (
            <div className="space-y-5">
              {posts.map(post => (
                <PostCard key={post.id} post={post} viewerId={viewer.id}
                  onReact={handleReact} onComment={handleComment}
                  onBookmark={handleBookmark} onVote={handleVote} />
              ))}
              {hasMore && (
                <button onClick={loadMore} disabled={loading}
                  className="w-full py-3 rounded-xl font-black text-xs uppercase"
                  style={{ border: `3px solid ${BORDER}`, background: '#fff', boxShadow: `3px 3px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif', opacity: loading ? 0.6 : 1 }}>
                  {loading ? 'Loading…' : 'Load more ↓'}
                </button>
              )}
            </div>
          )}
        </div>

        {/* RIGHT sidebar */}
        <aside className="lg:col-span-3 space-y-5">

          {/* BPU Members Directory link */}
          <div className="rounded-2xl p-5 space-y-3" style={{ background: SLATE, border: `3px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}>
            <div className="pb-2 border-b-2" style={{ borderColor: 'rgba(255,255,255,0.15)' }}>
              <span className="font-black text-[10px] uppercase block" style={{ color: OCHRE, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>BPU Network</span>
              <h4 className="font-black text-sm uppercase text-white" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Find Professionals</h4>
            </div>
            <p className="text-xs font-medium text-gray-400">Discover and connect with 7,000+ verified Black professionals across the UK.</p>
            <Link href="/members"
              className="block w-full text-center py-2.5 rounded-xl font-black text-xs uppercase text-white"
              style={{ background: OCHRE, border: `3px solid ${BORDER}`, boxShadow: `3px 3px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
              Browse Directory ↗
            </Link>
          </div>

          {/* Placement spotlight */}
          <div className="rounded-2xl p-5 space-y-3" style={{ background: '#fff', border: `3px solid ${BORDER}`, boxShadow: `5px 5px 0 ${BORDER}` }}>
            <div className="pb-2 border-b-2" style={{ borderColor: BORDER }}>
              <span className="font-black text-[10px] uppercase block" style={{ color: OCHRE, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>BPU Placement Spotlight</span>
              <h4 className="font-black text-sm uppercase" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Experienced Talent Scheme</h4>
            </div>
            <p className="text-xs font-medium text-gray-600 leading-relaxed">Connecting mid-career Black professionals across Scotland with senior UK placement vacancies.</p>
            <Link href="/jobs"
              className="block w-full text-center py-2.5 rounded-xl font-black text-xs uppercase text-white"
              style={{ background: SLATE, border: `3px solid ${BORDER}`, boxShadow: `3px 3px 0 ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
              Explore Placements ↗
            </Link>
          </div>

        </aside>
      </div>


      {/* Create Post Modal */}
      {showComposer && (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div className="absolute inset-0" style={{ background: 'rgba(17,19,23,0.85)', backdropFilter: 'blur(4px)' }}
            onClick={() => setShowComposer(false)} />
          <div className="relative z-10 bg-white rounded-2xl max-w-lg w-full overflow-hidden"
            style={{ border: `4px solid ${BORDER}`, boxShadow: `12px 12px 0 ${BORDER}` }}>
            <div className="flex justify-between items-center p-5 border-b-2" style={{ background: SLATE, borderColor: BORDER }}>
              <span className="font-black text-xs uppercase tracking-wider" style={{ color: OCHRE, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>
                Share Network Update
              </span>
              <button onClick={() => setShowComposer(false)}
                className="w-8 h-8 flex items-center justify-center rounded text-white"
                style={{ background: OCHRE, border: `2px solid ${BORDER}` }}>✕</button>
            </div>
            <form onSubmit={handleCreatePost} className="p-6 space-y-4">
              <div className="flex items-center gap-3">
                <div className="w-10 h-10 rounded-xl overflow-hidden flex-shrink-0" style={{ border: `2px solid ${BORDER}` }}>
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img src={avatar(null, viewer.display_name || 'User')} alt="" className="w-full h-full object-cover" />
                </div>
                <div>
                  <span className="font-black text-xs uppercase block" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>{viewer.display_name || viewer.username}</span>
                </div>
              </div>
              <div>
                <label className="block font-black text-xs uppercase mb-1" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Category <span style={{ color: OCHRE }}>*</span></label>
                <select value={postCategory} onChange={e => setPostCategory(e.target.value)} required
                  className="w-full p-2.5 rounded-xl font-medium text-xs bg-white focus:outline-none"
                  style={{ border: `3px solid ${BORDER}` }}>
                  <option value="Placement News">Placement News & Career Move</option>
                  <option value="Milestone">Milestone & Recognition</option>
                  <option value="Technical Insight">Technical Insight & Engineering</option>
                  <option value="Mentorship">Mentorship Story or Offer</option>
                  <option value="Interactive Poll">Interactive Community Poll</option>
                </select>
              </div>
              <div>
                <label className="block font-black text-xs uppercase mb-1" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Update <span style={{ color: OCHRE }}>*</span></label>
                <textarea required rows={4} value={postContent} onChange={e => setPostContent(e.target.value)}
                  placeholder="What professional insight, placement milestone, or connection update are you sharing?"
                  className="w-full p-2.5 rounded-xl font-medium text-xs focus:outline-none"
                  style={{ border: `3px solid ${BORDER}` }} />
              </div>
              {postCategory === 'Interactive Poll' && (
                <div className="p-3 rounded-xl space-y-2" style={{ background: `${PAPER}80`, border: `2px solid ${BORDER}` }}>
                  <label className="block font-black text-xs uppercase" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Poll Options</label>
                  {pollOptions.map((opt, i) => (
                    <input key={i} value={opt} onChange={e => setPollOptions(prev => prev.map((o, j) => j === i ? e.target.value : o))}
                      placeholder={`Option ${i + 1}`}
                      className="w-full p-2 rounded text-xs font-medium bg-white focus:outline-none"
                      style={{ border: `2px solid ${BORDER}` }} />
                  ))}
                  {pollOptions.length < 4 && (
                    <button type="button" onClick={() => setPollOptions(p => [...p, ''])}
                      className="text-xs font-bold" style={{ color: OCHRE }}>+ Add Option</button>
                  )}
                </div>
              )}
              <div>
                <label className="block font-black text-xs uppercase mb-1" style={{ fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Hashtags</label>
                <input value={postHashtags} onChange={e => setPostHashtags(e.target.value)}
                  placeholder="#ScotlandTech2026 #ExperiencedTalent"
                  className="w-full p-2.5 rounded-xl font-medium text-xs focus:outline-none"
                  style={{ border: `3px solid ${BORDER}`, background: `${PAPER}50` }} />
              </div>
              <div className="flex justify-end gap-2 pt-2">
                <button type="button" onClick={() => setShowComposer(false)}
                  className="px-4 py-2 rounded-xl font-black text-xs uppercase"
                  style={{ border: `3px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif' }}>Cancel</button>
                <button type="submit" disabled={postSubmitting}
                  className="px-6 py-2 rounded-xl font-black text-xs uppercase text-white"
                  style={{ background: OCHRE, border: `3px solid ${BORDER}`, fontFamily: 'Plus Jakarta Sans, sans-serif', opacity: postSubmitting ? 0.7 : 1 }}>
                  {postSubmitting ? 'Posting…' : 'Post to Network ↗'}
                </button>
              </div>
            </form>
          </div>
        </div>
      )}

      {toast && <Toast msg={toast} onClose={() => setToast(null)} />}

      </div>{/* end max-w container */}
    </div>
  );
}
