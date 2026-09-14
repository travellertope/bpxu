'use client';

import { useState } from 'react';
import CampaignsPanel from './CampaignsPanel';
import ListsPanel from './ListsPanel';
import MailPoetImportPanel from './MailPoetImportPanel';

type Tab = 'campaigns' | 'lists' | 'mailpoet';

const TABS: { key: Tab; label: string }[] = [
    { key: 'campaigns', label: 'Campaigns' },
    { key: 'lists', label: 'Lists' },
    { key: 'mailpoet', label: 'Import from MailPoet' },
];

export default function NewsletterAdmin() {
    const [tab, setTab] = useState<Tab>('campaigns');

    return (
        <div className="space-y-5">
            <div className="flex gap-2 flex-wrap" style={{ borderBottom: '1px solid var(--border)' }}>
                {TABS.map(t => (
                    <button
                        key={t.key}
                        onClick={() => setTab(t.key)}
                        className="btn btn-sm"
                        style={{
                            border: 'none',
                            borderBottom: tab === t.key ? '2px solid var(--purple)' : '2px solid transparent',
                            borderRadius: 0,
                            background: 'transparent',
                            color: tab === t.key ? 'var(--purple)' : undefined,
                            fontWeight: tab === t.key ? 700 : 500,
                        }}
                    >
                        {t.label}
                    </button>
                ))}
            </div>

            {tab === 'campaigns' && <CampaignsPanel />}
            {tab === 'lists' && <ListsPanel />}
            {tab === 'mailpoet' && <MailPoetImportPanel />}
        </div>
    );
}
