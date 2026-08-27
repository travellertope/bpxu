'use client';

import { useEffect, useState } from 'react';

interface FieldRow {
    field: string;
    label: string;
    missing_count: number;
    missing_pct: number;
}

interface CompletenessData {
    total_members: number;
    fully_complete_count: number;
    fully_complete_pct: number;
    fields: FieldRow[];
}

function StatCard({ label, value, color }: { label: string; value: string | number; color?: string }) {
    return (
        <div className="card card-p text-center">
            <p className="text-3xl font-extrabold" style={{ color: color || 'var(--text)' }}>{value}</p>
            <p className="text-sm text-text-3 mt-1">{label}</p>
        </div>
    );
}

export default function ProfileCompletenessAdmin() {
    const [data, setData] = useState<CompletenessData | null>(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [exportingField, setExportingField] = useState<string | null>(null);

    useEffect(() => {
        (async () => {
            setLoading(true);
            setError('');
            try {
                const res = await fetch('/api/paired/admin/profile-completeness');
                const json = await res.json();
                if (!res.ok) throw new Error(json.message || json.error || 'Failed to load profile completeness.');
                setData(json);
            } catch (e) {
                setError(e instanceof Error ? e.message : 'Failed to load profile completeness.');
            } finally {
                setLoading(false);
            }
        })();
    }, []);

    async function handleExport(field: string) {
        setExportingField(field);
        try {
            const res = await fetch(`/api/paired/admin/profile-completeness/export?field=${encodeURIComponent(field)}`);
            const json = await res.json();
            if (!res.ok) throw new Error(json.message || json.error || 'Export failed.');

            const blob = new Blob([json.csv || ''], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = json.filename || `missing-${field}.csv`;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        } catch (e) {
            alert(e instanceof Error ? e.message : 'Export failed.');
        } finally {
            setExportingField(null);
        }
    }

    if (loading) {
        return <p className="text-text-2">Loading…</p>;
    }

    if (error) {
        return <div className="alert alert-red text-sm">{error}</div>;
    }

    if (!data) {
        return null;
    }

    return (
        <div className="space-y-8">
            <div className="grid grid-cols-2 lg:grid-cols-3 gap-4">
                <StatCard label="Total Members" value={data.total_members} />
                <StatCard label="Fully Complete Profiles" value={data.fully_complete_count} color="var(--ok)" />
                <StatCard
                    label="Fully Complete Rate"
                    value={`${data.fully_complete_pct}%`}
                    color="var(--brand)"
                />
            </div>

            <div className="card overflow-hidden">
                {/* Desktop table */}
                <table className="hidden md:table w-full text-sm">
                    <thead>
                        <tr className="text-left text-text-3 border-b border-border">
                            <th className="p-3 font-medium">Field</th>
                            <th className="p-3 font-medium">Missing</th>
                            <th className="p-3 font-medium">% Missing</th>
                            <th className="p-3 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        {data.fields.map(f => (
                            <tr key={f.field} className="border-b border-border last:border-0">
                                <td className="p-3 font-medium">{f.label}</td>
                                <td className="p-3">{f.missing_count}</td>
                                <td className="p-3">
                                    <div className="flex items-center gap-2">
                                        <div className="flex-1 max-w-[140px] h-2 rounded-full bg-surface overflow-hidden">
                                            <div
                                                className="h-full rounded-full"
                                                style={{ width: `${f.missing_pct}%`, background: 'var(--err, #ef4444)' }}
                                            />
                                        </div>
                                        <span>{f.missing_pct}%</span>
                                    </div>
                                </td>
                                <td className="p-3 text-right">
                                    <button
                                        onClick={() => handleExport(f.field)}
                                        disabled={exportingField === f.field || f.missing_count === 0}
                                        className="btn btn-outline btn-sm"
                                    >
                                        {exportingField === f.field ? 'Exporting…' : 'Export CSV'}
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>

                {/* Mobile cards */}
                <div className="md:hidden divide-y divide-border">
                    {data.fields.map(f => (
                        <div key={f.field} className="p-4 space-y-2">
                            <div className="flex items-center justify-between">
                                <p className="font-medium">{f.label}</p>
                                <span className="text-sm text-text-3">{f.missing_count} ({f.missing_pct}%)</span>
                            </div>
                            <button
                                onClick={() => handleExport(f.field)}
                                disabled={exportingField === f.field || f.missing_count === 0}
                                className="btn btn-outline btn-sm w-full justify-center"
                            >
                                {exportingField === f.field ? 'Exporting…' : 'Export CSV'}
                            </button>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );
}
