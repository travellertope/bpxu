'use client';

import { useState, useEffect, useCallback } from 'react';

interface Coupon {
    id: number;
    code: string;
    discount_type: 'percentage' | 'fixed';
    discount_value: number;
    expiry_date: string;
    max_uses: number;
    current_uses: number;
    is_active: boolean;
}

interface CouponForm {
    code: string;
    discount_type: 'percentage' | 'fixed';
    discount_value: string;
    expiry_date: string;
    max_uses: string;
    is_active: boolean;
}

const EMPTY_FORM: CouponForm = { code: '', discount_type: 'percentage', discount_value: '', expiry_date: '', max_uses: '', is_active: true };

export default function CouponAdmin() {
    const [coupons, setCoupons] = useState<Coupon[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState('');
    const [showForm, setShowForm] = useState(false);
    const [editingId, setEditingId] = useState<number | null>(null);
    const [form, setForm] = useState<CouponForm>(EMPTY_FORM);
    const [saving, setSaving] = useState(false);
    const [formError, setFormError] = useState('');
    const [deletingId, setDeletingId] = useState<number | null>(null);
    const [confirmDelete, setConfirmDelete] = useState<Coupon | null>(null);

    const fetchCoupons = useCallback(async () => {
        setLoading(true);
        setError('');
        try {
            const res = await fetch('/api/paired/admin/coupons');
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to load coupons.');
            setCoupons(data.coupons || []);
        } catch (e) {
            setError(e instanceof Error ? e.message : 'Failed to load coupons.');
        } finally {
            setLoading(false);
        }
    }, []);

    useEffect(() => { fetchCoupons(); }, [fetchCoupons]);

    function openCreate() {
        setEditingId(null);
        setForm(EMPTY_FORM);
        setFormError('');
        setShowForm(true);
    }

    function openEdit(coupon: Coupon) {
        setEditingId(coupon.id);
        setForm({
            code: coupon.code,
            discount_type: coupon.discount_type,
            discount_value: String(coupon.discount_value),
            expiry_date: coupon.expiry_date || '',
            max_uses: coupon.max_uses ? String(coupon.max_uses) : '',
            is_active: coupon.is_active,
        });
        setFormError('');
        setShowForm(true);
    }

    async function handleSubmit(e: React.FormEvent) {
        e.preventDefault();
        if (!form.code.trim()) { setFormError('Code is required.'); return; }
        if (!form.discount_value || Number(form.discount_value) <= 0) { setFormError('Discount value must be positive.'); return; }

        setSaving(true);
        setFormError('');

        const body = {
            code: form.code.trim().toUpperCase(),
            discount_type: form.discount_type,
            discount_value: Number(form.discount_value),
            expiry_date: form.expiry_date || '',
            max_uses: form.max_uses ? Number(form.max_uses) : 0,
            is_active: form.is_active,
        };

        try {
            const url = editingId ? `/api/paired/admin/coupons/${editingId}` : '/api/paired/admin/coupons';
            const method = editingId ? 'PUT' : 'POST';
            const res = await fetch(url, {
                method,
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(body),
            });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to save coupon.');
            setShowForm(false);
            fetchCoupons();
        } catch (e) {
            setFormError(e instanceof Error ? e.message : 'Failed to save.');
        } finally {
            setSaving(false);
        }
    }

    async function deleteCoupon(coupon: Coupon) {
        setDeletingId(coupon.id);
        try {
            const res = await fetch(`/api/paired/admin/coupons/${coupon.id}`, { method: 'DELETE' });
            const data = await res.json();
            if (!res.ok) throw new Error(data.error || 'Failed to delete.');
            setCoupons(prev => prev.filter(c => c.id !== coupon.id));
            setConfirmDelete(null);
        } catch (e) {
            alert(e instanceof Error ? e.message : 'Delete failed.');
        } finally {
            setDeletingId(null);
        }
    }

    function formatDiscount(c: Coupon): string {
        return c.discount_type === 'percentage' ? `${c.discount_value}%` : `£${c.discount_value.toFixed(2)}`;
    }

    const isExpired = (c: Coupon) => c.expiry_date && new Date(c.expiry_date) < new Date();

    return (
        <div className="space-y-5">
            <div className="flex items-center justify-between">
                <p className="text-sm text-text-3">{coupons.length} coupon{coupons.length !== 1 ? 's' : ''}</p>
                <button onClick={openCreate} className="btn btn-purple btn-sm">+ New Coupon</button>
            </div>

            {loading ? (
                <div className="text-center text-sm text-text-2 py-12">Loading coupons...</div>
            ) : error ? (
                <div className="card card-p text-center py-10" style={{ color: 'var(--err)' }}>{error}</div>
            ) : coupons.length === 0 ? (
                <div className="card card-p text-center py-10">
                    <p className="font-semibold text-text-2">No coupons yet</p>
                    <p className="text-sm text-text-3 mt-1">Create your first coupon to get started.</p>
                </div>
            ) : (
                <>
                    {/* Desktop Table */}
                    <div className="card hidden md:block" style={{ overflow: 'hidden' }}>
                        <table style={{ width: '100%', borderCollapse: 'collapse' }}>
                            <thead>
                                <tr style={{ borderBottom: '1px solid var(--border)', background: 'var(--surface)' }}>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Code</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Discount</th>
                                    <th className="text-left text-xs font-semibold text-text-3 p-3">Expiry</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Uses</th>
                                    <th className="text-center text-xs font-semibold text-text-3 p-3">Status</th>
                                    <th className="text-right text-xs font-semibold text-text-3 p-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                {coupons.map(c => (
                                    <tr key={c.id} style={{ borderBottom: '1px solid var(--border)' }}>
                                        <td className="p-3">
                                            <span className="font-mono font-bold text-sm" style={{ color: 'var(--purple)' }}>{c.code}</span>
                                        </td>
                                        <td className="p-3 text-sm text-text-2">{formatDiscount(c)}</td>
                                        <td className="p-3 text-sm text-text-2">
                                            {c.expiry_date || 'No expiry'}
                                            {isExpired(c) && <span className="badge badge-red ml-2" style={{ fontSize: '0.65rem' }}>Expired</span>}
                                        </td>
                                        <td className="p-3 text-sm text-text-2 text-center">
                                            {c.current_uses}{c.max_uses ? ` / ${c.max_uses}` : ''}
                                        </td>
                                        <td className="p-3 text-center">
                                            <span className={`badge ${c.is_active ? 'badge-green' : 'badge-amber'}`}>
                                                {c.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                        <td className="p-3 text-right">
                                            <div className="flex items-center justify-end gap-1">
                                                <button onClick={() => openEdit(c)} className="btn btn-ghost btn-sm text-xs">Edit</button>
                                                <button
                                                    onClick={() => setConfirmDelete(c)}
                                                    className="btn btn-ghost btn-sm text-xs"
                                                    style={{ color: 'var(--err)' }}
                                                >
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>

                    {/* Mobile Cards */}
                    <div className="md:hidden space-y-3">
                        {coupons.map(c => (
                            <div key={c.id} className="card card-p space-y-2">
                                <div className="flex items-center justify-between">
                                    <span className="font-mono font-bold" style={{ color: 'var(--purple)' }}>{c.code}</span>
                                    <span className={`badge ${c.is_active ? 'badge-green' : 'badge-amber'}`}>
                                        {c.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </div>
                                <div className="flex items-center gap-4 text-sm text-text-2">
                                    <span>{formatDiscount(c)}</span>
                                    <span>{c.current_uses}{c.max_uses ? ` / ${c.max_uses}` : ''} uses</span>
                                </div>
                                <div className="text-xs text-text-3">
                                    {c.expiry_date ? `Expires: ${c.expiry_date}` : 'No expiry'}
                                    {isExpired(c) && <span className="badge badge-red ml-2" style={{ fontSize: '0.6rem' }}>Expired</span>}
                                </div>
                                <div className="flex gap-2 pt-1">
                                    <button onClick={() => openEdit(c)} className="btn btn-ghost btn-sm text-xs">Edit</button>
                                    <button onClick={() => setConfirmDelete(c)} className="btn btn-ghost btn-sm text-xs" style={{ color: 'var(--err)' }}>Delete</button>
                                </div>
                            </div>
                        ))}
                    </div>
                </>
            )}

            {/* Create / Edit Modal */}
            {showForm && (
                <div
                    style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 100, padding: 16 }}
                    onClick={() => setShowForm(false)}
                >
                    <div className="card card-p" style={{ width: '100%', maxWidth: 480 }} onClick={e => e.stopPropagation()}>
                        <h2 className="text-lg font-bold mb-4">{editingId ? 'Edit Coupon' : 'New Coupon'}</h2>
                        {formError && <div className="alert alert-red mb-4">{formError}</div>}

                        <form onSubmit={handleSubmit} className="space-y-4">
                            <div>
                                <label htmlFor="coupon-code" className="field-label mb-1 block">Coupon Code</label>
                                <input
                                    id="coupon-code"
                                    type="text"
                                    className="field-input font-mono"
                                    placeholder="SUMMER2026"
                                    value={form.code}
                                    onChange={e => setForm(prev => ({ ...prev, code: e.target.value.toUpperCase() }))}
                                    style={{ textTransform: 'uppercase' }}
                                />
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label htmlFor="discount-type" className="field-label mb-1 block">Discount Type</label>
                                    <select
                                        id="discount-type"
                                        className="field-input"
                                        value={form.discount_type}
                                        onChange={e => setForm(prev => ({ ...prev, discount_type: e.target.value as 'percentage' | 'fixed' }))}
                                    >
                                        <option value="percentage">Percentage (%)</option>
                                        <option value="fixed">Fixed Amount (£)</option>
                                    </select>
                                </div>
                                <div>
                                    <label htmlFor="discount-value" className="field-label mb-1 block">
                                        {form.discount_type === 'percentage' ? 'Percentage' : 'Amount (£)'}
                                    </label>
                                    <input
                                        id="discount-value"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        className="field-input"
                                        placeholder={form.discount_type === 'percentage' ? '10' : '5.00'}
                                        value={form.discount_value}
                                        onChange={e => setForm(prev => ({ ...prev, discount_value: e.target.value }))}
                                    />
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <label htmlFor="expiry-date" className="field-label mb-1 block">Expiry Date</label>
                                    <input
                                        id="expiry-date"
                                        type="date"
                                        className="field-input"
                                        value={form.expiry_date}
                                        onChange={e => setForm(prev => ({ ...prev, expiry_date: e.target.value }))}
                                    />
                                </div>
                                <div>
                                    <label htmlFor="max-uses" className="field-label mb-1 block">Max Uses (0 = unlimited)</label>
                                    <input
                                        id="max-uses"
                                        type="number"
                                        min="0"
                                        className="field-input"
                                        placeholder="0"
                                        value={form.max_uses}
                                        onChange={e => setForm(prev => ({ ...prev, max_uses: e.target.value }))}
                                    />
                                </div>
                            </div>
                            <div className="flex items-center gap-2">
                                <input
                                    id="is-active"
                                    type="checkbox"
                                    checked={form.is_active}
                                    onChange={e => setForm(prev => ({ ...prev, is_active: e.target.checked }))}
                                />
                                <label htmlFor="is-active" className="text-sm">Active</label>
                            </div>

                            <div className="flex gap-2 mt-6">
                                <button type="submit" disabled={saving} className="btn btn-purple flex-1">
                                    {saving ? 'Saving...' : editingId ? 'Save Changes' : 'Create Coupon'}
                                </button>
                                <button type="button" onClick={() => setShowForm(false)} className="btn btn-outline">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Delete Confirmation */}
            {confirmDelete && (
                <div
                    style={{ position: 'fixed', inset: 0, background: 'rgba(0,0,0,0.5)', display: 'flex', alignItems: 'center', justifyContent: 'center', zIndex: 100, padding: 16 }}
                    onClick={() => setConfirmDelete(null)}
                >
                    <div className="card card-p" style={{ width: '100%', maxWidth: 400 }} onClick={e => e.stopPropagation()}>
                        <h2 className="text-lg font-bold mb-2">Delete Coupon</h2>
                        <p className="text-sm text-text-2 mb-6">
                            Are you sure you want to delete coupon <strong className="font-mono">{confirmDelete.code}</strong>? This cannot be undone.
                        </p>
                        <div className="flex gap-2">
                            <button
                                onClick={() => deleteCoupon(confirmDelete)}
                                disabled={deletingId === confirmDelete.id}
                                className="btn flex-1"
                                style={{ background: 'var(--err)', color: '#fff', border: 'none' }}
                            >
                                {deletingId === confirmDelete.id ? 'Deleting...' : 'Yes, Delete'}
                            </button>
                            <button onClick={() => setConfirmDelete(null)} className="btn btn-outline">Cancel</button>
                        </div>
                    </div>
                </div>
            )}
        </div>
    );
}
