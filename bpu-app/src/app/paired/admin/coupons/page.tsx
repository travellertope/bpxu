import { getBPUSession } from '@/lib/auth';
import { redirect } from 'next/navigation';
import CouponAdmin from './CouponAdmin';

export default async function AdminCouponsPage() {
    const session = await getBPUSession();
    if (!session.authenticated || !session.user) {
        redirect('/login?returnTo=/paired/admin/coupons');
    }
    if (!session.user.roles.includes('administrator')) {
        redirect('/paired/dashboard');
    }

    return (
        <div className="fade-up">
            <h1 className="text-3xl font-bold mb-2">Coupon Management</h1>
            <p className="text-text-2 mb-8">Create and manage discount coupons.</p>
            <CouponAdmin />
        </div>
    );
}
