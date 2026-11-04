'use client';

import { useEffect, useRef, useState } from 'react';

interface StatNode {
    value: string;
    label: string;
    sub?: string;
    // position as % of container
    x: number;
    y: number;
    highlight?: boolean;
}

const NODES: StatNode[] = [
    { value: '73%',     label: "Hold a Master's",       sub: 'or higher',          x: 50,  y: 8,  highlight: true },
    { value: '58%',     label: 'First-gen immigrants',  sub: 'global skills asset', x: 82,  y: 22 },
    { value: '47.5%',   label: 'Experienced racism',    sub: 'personally',          x: 18,  y: 32, highlight: true },
    { value: '12.9%',   label: 'Minority ethnic',       sub: 'Scotland census',     x: 72,  y: 48 },
    { value: '10.3%',   label: 'Ethnicity pay gap',     sub: 'Scotland average',    x: 28,  y: 58 },
    { value: '28%',     label: 'Black women',           sub: 'in desired role',     x: 60,  y: 70, highlight: true },
    { value: '11.7pp',  label: 'Employment gap',        sub: 'vs white population', x: 12,  y: 76 },
    { value: '16.1%',   label: "Children faced racism", sub: 'of respondents',      x: 82,  y: 82 },
];

// Which nodes are connected by lines [from index, to index]
const EDGES: [number, number][] = [
    [0, 1], [0, 2], [0, 4],
    [1, 3], [2, 4], [3, 5],
    [4, 6], [5, 7], [3, 7],
    [1, 5],
];

function useCountUp(target: number, decimals: number, triggered: boolean, duration = 1200) {
    const [val, setVal] = useState(0);
    useEffect(() => {
        if (!triggered) return;
        let start: number | null = null;
        const step = (ts: number) => {
            if (!start) start = ts;
            const prog = Math.min((ts - start) / duration, 1);
            const ease = 1 - Math.pow(1 - prog, 3);
            setVal(parseFloat((target * ease).toFixed(decimals)));
            if (prog < 1) requestAnimationFrame(step);
        };
        requestAnimationFrame(step);
    }, [triggered, target, decimals, duration]);
    return val;
}

function parseStatValue(raw: string): { num: number; suffix: string; decimals: number } {
    const match = raw.match(/^([\d.]+)(.*)/);
    if (!match) return { num: 0, suffix: '', decimals: 0 };
    const num = parseFloat(match[1]);
    const decimals = (match[1].split('.')[1] ?? '').length;
    return { num, suffix: match[2] ?? '', decimals };
}

function NodeCard({ node, triggered }: { node: StatNode; triggered: boolean }) {
    const { num, suffix, decimals } = parseStatValue(node.value);
    const animated = useCountUp(num, decimals, triggered);
    const display = `${animated.toFixed(decimals)}${suffix}`;

    return (
        <div
            className="absolute flex flex-col items-center text-center select-none"
            style={{
                left: `${node.x}%`,
                top: `${node.y}%`,
                transform: 'translate(-50%, -50%)',
                zIndex: 2,
            }}
        >
            <div
                className="rounded-2xl px-3 py-2 transition-transform hover:scale-105"
                style={{
                    background: node.highlight
                        ? 'rgba(217,56,30,0.18)'
                        : 'rgba(255,255,255,0.08)',
                    border: `1px solid ${node.highlight ? 'rgba(217,56,30,0.5)' : 'rgba(255,255,255,0.15)'}`,
                    backdropFilter: 'blur(8px)',
                    WebkitBackdropFilter: 'blur(8px)',
                    minWidth: 72,
                }}
            >
                <p
                    className="font-black leading-none tabular-nums"
                    style={{
                        fontSize: 'clamp(0.85rem, 2.2vw, 1.35rem)',
                        color: node.highlight ? '#d9381e' : '#fff',
                    }}
                >
                    {display}
                </p>
                <p className="text-white/70 leading-tight mt-0.5" style={{ fontSize: '0.6rem', maxWidth: 80 }}>
                    {node.label}
                </p>
                {node.sub && (
                    <p className="text-white/40 leading-tight" style={{ fontSize: '0.5rem' }}>
                        {node.sub}
                    </p>
                )}
            </div>
        </div>
    );
}

export default function StatsConstellation() {
    const containerRef = useRef<HTMLDivElement>(null);
    const [triggered, setTriggered] = useState(false);
    const [dims, setDims] = useState({ w: 400, h: 480 });

    useEffect(() => {
        const el = containerRef.current;
        if (!el) return;

        const resize = () => {
            const rect = el.getBoundingClientRect();
            setDims({ w: rect.width, h: rect.height });
        };
        resize();
        const ro = new ResizeObserver(resize);
        ro.observe(el);

        const io = new IntersectionObserver(
            ([entry]) => { if (entry.isIntersecting) setTriggered(true); },
            { threshold: 0.3 }
        );
        io.observe(el);

        return () => { ro.disconnect(); io.disconnect(); };
    }, []);

    return (
        <div
            ref={containerRef}
            className="relative w-full"
            style={{ height: 'clamp(340px, 55vw, 520px)' }}
            aria-hidden="true"
        >
            {/* SVG connecting lines */}
            <svg
                className="absolute inset-0 w-full h-full"
                style={{ zIndex: 1 }}
                viewBox={`0 0 ${dims.w} ${dims.h}`}
                preserveAspectRatio="none"
            >
                <defs>
                    <linearGradient id="line-grad" x1="0%" y1="0%" x2="100%" y2="0%">
                        <stop offset="0%" stopColor="rgba(217,56,30,0.4)" />
                        <stop offset="100%" stopColor="rgba(255,255,255,0.1)" />
                    </linearGradient>
                </defs>
                {EDGES.map(([a, b], i) => {
                    const na = NODES[a];
                    const nb = NODES[b];
                    const x1 = (na.x / 100) * dims.w;
                    const y1 = (na.y / 100) * dims.h;
                    const x2 = (nb.x / 100) * dims.w;
                    const y2 = (nb.y / 100) * dims.h;
                    // midpoint for slight curve
                    const mx = (x1 + x2) / 2 + (Math.sin(i) * 20);
                    const my = (y1 + y2) / 2 + (Math.cos(i) * 10);
                    return (
                        <path
                            key={i}
                            d={`M${x1},${y1} Q${mx},${my} ${x2},${y2}`}
                            fill="none"
                            stroke="rgba(255,255,255,0.08)"
                            strokeWidth="1"
                        />
                    );
                })}
                {/* Pulsing dots at node positions */}
                {NODES.map((node, i) => (
                    <circle
                        key={i}
                        cx={(node.x / 100) * dims.w}
                        cy={(node.y / 100) * dims.h}
                        r="3"
                        fill={node.highlight ? 'rgba(217,56,30,0.6)' : 'rgba(255,255,255,0.2)'}
                    />
                ))}
            </svg>

            {/* Stat cards */}
            {NODES.map((node, i) => (
                <NodeCard key={i} node={node} triggered={triggered} />
            ))}
        </div>
    );
}
