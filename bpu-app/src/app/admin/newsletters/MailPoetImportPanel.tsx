'use client';

import { useState, useEffect, useCallback } from 'react';

interface MailPoetStatus {
    available: boolean;
    newsletter_count?: number;
    segment_count?: number;
    subscriber_count?: number;
}

export default function MailPoetImportPanel() {
    const [status, setStatus] = useState<MailPoetStatus | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    const [importingCampaigns, setImportingCampaigns] = useState(false);
    const [campaignsResult, setCampaignsResult] = useState('');
    const [importingLists, setImportingLists] = useState(false);
    const [listsResult, setListsResult] = useState('');

    const fetchStatus = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const res = await fetch('/api/paired/admin/newsletter/mailpoet/status');
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to check for MailPoet.');
            setStatus(data);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to check for MailPoet.');
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => { fetchStatus(); }, [fetchStatus]);

    async function handleImportCampaigns() {
        if (!confirm('Import all MailPoet newsletters as new draft campaigns here? This does not delete anything from MailPoet.')) return;
        setImportingCampaigns(true);
        setCampaignsResult('');
        try {
            const res = await fetch('/api/paired/admin/newsletter/mailpoet/import-campaigns', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({}),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Import failed.');
            setCampaignsResult(`Imported ${data.imported_count} campaign${data.imported_count !== 1 ? 's' : ''} as drafts. Open the Campaigns tab to review and edit them before sending — MailPoet's block layouts are converted to plain paragraphs, so formatting may need touching up.`);
        } catch (e) {
            setCampaignsResult(e instanceof Error ? e.message : 'Import failed.');
        } finally {
            setImportingCampaigns(false);
        }
    }

    async function handleImportLists() {
        if (!confirm('Import all MailPoet lists and their subscribed members as custom lists here?')) return;
        setImportingLists(true);
        setListsResult('');
        try {
            const res = await fetch('/api/paired/admin/newsletter/mailpoet/import-lists', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({}),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Import failed.');
            const total = (data.lists || []).reduce((sum: number, l: { subscribers_imported: number }) => sum + l.subscribers_imported, 0);
            setListsResult(`Imported ${data.lists?.length || 0} list${data.lists?.length !== 1 ? 's' : ''} with ${total} subscriber${total !== 1 ? 's' : ''} total. Open the Lists tab to review them.`);
        } catch (e) {
            setListsResult(e instanceof Error ? e.message : 'Import failed.');
        } finally {
            setImportingLists(false);
        }
    }

    if (loading) {
        return <div className="text-center text-sm text-text-2 py-12">Checking for MailPoet...</div>;
    }

    if (error) {
        return <div className="card card-p text-center text-sm py-10" style={{ color: 'var(--err)' }}>{error}</div>;
    }

    if (!status?.available) {
        return (
            <div className="card card-p text-center py-10">
                <p className="font-semibold text-text-2">MailPoet not found</p>
                <p className="text-sm text-text-3 mt-1 max-w-md mx-auto">
                    No MailPoet tables were found on this WordPress install. If MailPoet is installed on a different site,
                    export a CSV of its subscribers and use the CSV import on the Lists tab instead.
                </p>
            </div>
        );
    }

    return (
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <div className="card card-p space-y-3">
                <h2 className="text-lg font-bold">Import Campaigns</h2>
                <p className="text-sm text-text-3">
                    Found <strong>{status.newsletter_count}</strong> MailPoet newsletter{status.newsletter_count !== 1 ? 's' : ''}.
                    Importing creates a draft campaign here for each one, so you can review, edit with the rich-text editor,
                    and send from this tool.
                </p>
                <button onClick={handleImportCampaigns} disabled={importingCampaigns || !status.newsletter_count} className="btn btn-purple">
                    {importingCampaigns ? 'Importing...' : `Import ${status.newsletter_count || 0} Campaign${status.newsletter_count !== 1 ? 's' : ''}`}
                </button>
                {campaignsResult && (
                    <div className="alert alert-green text-sm">{campaignsResult}</div>
                )}
            </div>

            <div className="card card-p space-y-3">
                <h2 className="text-lg font-bold">Import Lists &amp; Subscribers</h2>
                <p className="text-sm text-text-3">
                    Found <strong>{status.segment_count}</strong> MailPoet list{status.segment_count !== 1 ? 's' : ''} and{' '}
                    <strong>{status.subscriber_count}</strong> subscriber{status.subscriber_count !== 1 ? 's' : ''} total.
                    Importing creates a matching custom list here for each MailPoet list, with its subscribed members added.
                </p>
                <button onClick={handleImportLists} disabled={importingLists || !status.segment_count} className="btn btn-purple">
                    {importingLists ? 'Importing...' : `Import ${status.segment_count || 0} List${status.segment_count !== 1 ? 's' : ''}`}
                </button>
                {listsResult && (
                    <div className="alert alert-green text-sm">{listsResult}</div>
                )}
            </div>
        </div>
    );
}
