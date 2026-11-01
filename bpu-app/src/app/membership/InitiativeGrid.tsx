'use client';

import Link from 'next/link';

interface Initiative {
    title: string;
    description: string;
    cta: string;
    href: string;
    icon: React.ReactNode;
}

function InitiativeCard({ item }: { item: Initiative }) {
    return (
        <div
            className="flex flex-col"
            style={{
                background: '#fff',
                border: '2px solid #111317',
                borderRadius: 12,
                boxShadow: '4px 4px 0px 0px #001478',
                transition: 'transform 0.15s, box-shadow 0.15s',
            }}
            onMouseEnter={e => {
                (e.currentTarget as HTMLElement).style.transform = 'translate(-2px,-2px)';
                (e.currentTarget as HTMLElement).style.boxShadow = '6px 6px 0px 0px #001478';
            }}
            onMouseLeave={e => {
                (e.currentTarget as HTMLElement).style.transform = '';
                (e.currentTarget as HTMLElement).style.boxShadow = '4px 4px 0px 0px #001478';
            }}
        >
            {/* Body */}
            <div className="flex flex-col gap-4 p-7 flex-1">
                {/* Icon + title */}
                <div className="flex items-center gap-4 pb-4" style={{ borderBottom: '2px solid #111317' }}>
                    <div
                        className="shrink-0 flex items-center justify-center rounded-lg"
                        style={{
                            width: 52, height: 52,
                            background: 'var(--bg)',
                            border: '2px solid #111317',
                            color: 'var(--brand)',
                        }}
                    >
                        {item.icon}
                    </div>
                    <h2 className="font-black text-base uppercase leading-tight tracking-tight" style={{ color: '#111317' }}>
                        {item.title}
                    </h2>
                </div>

                {/* Description */}
                <p className="text-sm font-medium leading-relaxed flex-1" style={{ color: 'var(--text-2)' }}>
                    {item.description}
                </p>
            </div>

            {/* CTA */}
            <div className="px-7 pb-7">
                <Link
                    href={item.href}
                    className="block w-full text-center text-xs font-black uppercase py-3.5 rounded-lg"
                    style={{
                        background: 'var(--brand)',
                        color: '#fff',
                        border: '2px solid #111317',
                        textDecoration: 'none',
                        letterSpacing: '0.08em',
                    }}
                >
                    {item.cta}
                </Link>
            </div>
        </div>
    );
}

interface Props {
    initiatives: Initiative[];
}

export default function InitiativeGrid({ initiatives }: Props) {
    return (
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
            {initiatives.map(item => (
                <InitiativeCard key={item.title} item={item} />
            ))}
        </div>
    );
}
