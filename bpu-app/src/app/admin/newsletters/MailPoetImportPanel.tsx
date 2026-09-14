'use client';

import { useState, useEffect, useCallback } from 'react';

interface MailPoetStatus {
    available: boolean;
    using_external_db?: boolean;
    newsletter_count?: number;
    segment_count?: number;
    subscriber_count?: number;
}

interface DbSettings {
    configured: boolean;
    db_host: string;
    db_name: string;
    db_user: string;
    has_password: boolean;
    db_prefix: string;
}

export default function MailPoetImportPanel() {
    const [status, setStatus] = useState<MailPoetStatus | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');

    const [dbSettings, setDbSettings] = useState<DbSettings | null>(null);
    const [showDbSettings, setShowDbSettings] = useState(false);
    const [dbHost, setDbHost] = useState('');
    const [dbName, setDbName] = useState('');
    const [dbUser, setDbUser] = useState('');
    const [dbPassword, setDbPassword] = useState('');
    const [dbPrefix, setDbPrefix] = useState('wp_');
    const [savingDb, setSavingDb] = useState(false);
    const [dbMessage, setDbMessage] = useState('');

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

    const fetchDbSettings = useCallback(async () => {
        try {
            const res = await fetch('/api/paired/admin/newsletter/mailpoet/settings');
            const data = await res.json();
            if (res.ok) {
                setDbSettings(data);
                setDbHost(data.db_host || '');
                setDbName(data.db_name || '');
                setDbUser(data.db_user || '');
                setDbPrefix(data.db_prefix || 'wp_');
                setShowDbSettings(!data.configured);
            }
        } catch { /* non-fatal */ }
    }, []);

    useEffect(() => {
        fetchStatus();
        fetchDbSettings();
    }, [fetchStatus, fetchDbSettings]);

    async function handleSaveDbSettings() {
        setSavingDb(true);
        setDbMessage('');
        try {
            const res = await fetch('/api/paired/admin/newsletter/mailpoet/settings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    db_host: dbHost,
                    db_name: dbName,
                    db_user: dbUser,
                    ...(dbPassword ? { db_password: dbPassword } : {}),
                    db_prefix: dbPrefix,
                }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to save settings.');
            setDbSettings(data);
            setDbPassword('');
            setDbMessage('Saved.');
            await fetchStatus();
        } catch (e) {
            setDbMessage(e instanceof Error ? e.message : 'Failed to save settings.');
        } finally {
            setSavingDb(false);
        }
    }

    async function handleClearDbSettings() {
        if (!confirm('Stop using an external MailPoet database and check this site\'s own database instead?')) return;
        setSavingDb(true);
        setDbMessage('');
        try {
            const res = await fetch('/api/paired/admin/newsletter/mailpoet/settings', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ db_host: '', db_name: '', db_user: '', db_prefix: 'wp_' }),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to clear settings.');
            setDbSettings(data);
            setDbHost('');
            setDbName('');
            setDbUser('');
            setDbPassword('');
            setDbPrefix('wp_');
            await fetchStatus();
        } catch (e) {
            setDbMessage(e instanceof Error ? e.message : 'Failed to clear settings.');
        } finally {
            setSavingDb(false);
        }
    }

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

    return (
        <div className="space-y-5">
            <div className="card card-p flex items-center justify-between flex-wrap gap-3">
                <div>
                    <p className="text-sm font-semibold">MailPoet database</p>
                    <p className="text-xs text-text-3">
                        {dbSettings?.configured
                            ? `Reading from an external database: ${dbSettings.db_user}@${dbSettings.db_host}/${dbSettings.db_name} (table prefix "${dbSettings.db_prefix}").`
                            : "Reading from this site's own database. If MailPoet runs on a separate WordPress install (e.g. a /mailer/ subdirectory site), point this at its database below."}
                    </p>
                </div>
                <button className="btn btn-outline btn-sm" onClick={() => setShowDbSettings(v => !v)}>
                    {showDbSettings ? 'Close' : 'Configure'}
                </button>
            </div>

            {showDbSettings && (
                <div className="card card-p space-y-3" style={{ maxWidth: 520 }}>
                    <p className="text-xs text-text-3">
                        Find these values in the <code>wp-config.php</code> file of the other WordPress install: <code>DB_HOST</code>,{' '}
                        <code>DB_NAME</code>, <code>DB_USER</code>, <code>DB_PASSWORD</code>, and <code>$table_prefix</code>.
                        It must be reachable from this server (same MySQL host, or credentials that allow remote access).
                    </p>
                    <div>
                        <label htmlFor="mp-db-host" className="field-label mb-1 block">DB Host</label>
                        <input id="mp-db-host" type="text" className="field-input" placeholder="localhost or host:port" value={dbHost} onChange={e => setDbHost(e.target.value)} />
                    </div>
                    <div>
                        <label htmlFor="mp-db-name" className="field-label mb-1 block">Database Name</label>
                        <input id="mp-db-name" type="text" className="field-input" value={dbName} onChange={e => setDbName(e.target.value)} />
                    </div>
                    <div>
                        <label htmlFor="mp-db-user" className="field-label mb-1 block">DB Username</label>
                        <input id="mp-db-user" type="text" className="field-input" value={dbUser} onChange={e => setDbUser(e.target.value)} />
                    </div>
                    <div>
                        <label htmlFor="mp-db-pass" className="field-label mb-1 block">DB Password</label>
                        <input
                            id="mp-db-pass"
                            type="password"
                            className="field-input"
                            placeholder={dbSettings?.has_password ? 'Leave blank to keep current password' : ''}
                            value={dbPassword}
                            onChange={e => setDbPassword(e.target.value)}
                        />
                    </div>
                    <div>
                        <label htmlFor="mp-db-prefix" className="field-label mb-1 block">Table Prefix</label>
                        <input id="mp-db-prefix" type="text" className="field-input" placeholder="wp_" value={dbPrefix} onChange={e => setDbPrefix(e.target.value)} />
                    </div>
                    {dbMessage && <p className="text-xs" style={{ color: dbMessage === 'Saved.' ? 'var(--ok)' : 'var(--err)' }}>{dbMessage}</p>}
                    <div className="flex gap-2">
                        <button onClick={handleSaveDbSettings} disabled={savingDb || !dbHost || !dbName || !dbUser} className="btn btn-purple btn-sm">
                            {savingDb ? 'Saving...' : 'Save & Test'}
                        </button>
                        {dbSettings?.configured && (
                            <button onClick={handleClearDbSettings} disabled={savingDb} className="btn btn-ghost btn-sm" style={{ color: 'var(--err)' }}>
                                Use this site&apos;s database instead
                            </button>
                        )}
                    </div>
                </div>
            )}

            {loading ? (
                <div className="text-center text-sm text-text-2 py-12">Checking for MailPoet...</div>
            ) : error ? (
                <div className="card card-p text-center text-sm py-10" style={{ color: 'var(--err)' }}>{error}</div>
            ) : !status?.available ? (
                <div className="card card-p text-center py-10">
                    <p className="font-semibold text-text-2">MailPoet not found</p>
                    <p className="text-sm text-text-3 mt-1 max-w-md mx-auto">
                        No MailPoet tables were found in {dbSettings?.configured ? 'the configured database' : "this site's own database"}.
                        If MailPoet runs on a separate WordPress install, configure its database above — or export a CSV of its
                        subscribers and use the CSV import on the Lists tab instead.
                    </p>
                </div>
            ) : (
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
            )}
        </div>
    );
}
