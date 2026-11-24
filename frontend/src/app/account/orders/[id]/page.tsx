"use client";

import Link from 'next/link';
import { useEffect, useState } from 'react';
import { useParams, useRouter } from 'next/navigation';
import { cancelOrder, getOrder, readAuthSession } from '@/lib/auth';
import type { OrderRecord } from '@/types/auth';

export default function OrderDetailsPage() {
  const params = useParams();
  const router = useRouter();
  const [order, setOrder] = useState<OrderRecord | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [cancelling, setCancelling] = useState(false);

  useEffect(() => {
    const session = readAuthSession();
    if (!session) {
      router.replace('/login');
      return;
    }

    const id = Array.isArray(params?.id) ? params.id[0] : params?.id;
    if (!id) {
      setError('Order id is missing.');
      setLoading(false);
      return;
    }

    getOrder(id)
      .then((data) => setOrder(data))
      .catch((err) => {
        setError(err instanceof Error ? err.message : 'Unable to load order details.');
      })
      .finally(() => setLoading(false));
  }, [params, router]);

  async function handleCancel() {
    if (!order) return;

    setCancelling(true);
    try {
      const updated = await cancelOrder(order.id);
      setOrder(updated);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unable to cancel order.');
    } finally {
      setCancelling(false);
    }
  }

  if (loading) {
    return <main className="min-h-screen bg-slate-950 px-6 py-12 text-slate-100"><div className="mx-auto max-w-4xl rounded-3xl border border-slate-800 bg-slate-900/80 p-8 text-slate-300">Loading order details...</div></main>;
  }

  if (error || !order) {
    return <main className="min-h-screen bg-slate-950 px-6 py-12 text-slate-100"><div className="mx-auto max-w-4xl rounded-3xl border border-rose-500/40 bg-rose-500/10 p-8 text-rose-200">{error ?? 'Order not found.'}</div></main>;
  }

  return (
    <main className="min-h-screen bg-slate-950 px-6 py-12 text-slate-100">
      <div className="mx-auto max-w-4xl space-y-6">
        <div className="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
          <div>
            <p className="text-sm uppercase tracking-[0.2em] text-emerald-300">Order details</p>
            <h1 className="mt-2 text-3xl font-bold text-white">{order.order_number}</h1>
          </div>
          <div className="flex items-center gap-3">
            <Link href="/account/orders" className="rounded-xl border border-slate-700 bg-slate-900 px-4 py-2 text-sm font-medium text-slate-200 hover:border-slate-500">
              Back to orders
            </Link>
            {order.status !== 'cancelled' ? (
              <button type="button" onClick={handleCancel} disabled={cancelling} className="rounded-xl border border-rose-500/40 bg-rose-500/10 px-4 py-2 text-sm font-medium text-rose-200 hover:bg-rose-500/20 disabled:opacity-60">
                {cancelling ? 'Cancelling...' : 'Cancel order'}
              </button>
            ) : null}
          </div>
        </div>

        <div className="rounded-3xl border border-slate-800 bg-slate-900/80 p-6">
          <div className="flex flex-wrap items-center gap-3 text-sm text-slate-300">
            <span className="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2.5 py-1 text-emerald-200">{order.status}</span>
            <span className="text-slate-400">Payment: {order.payment_status}</span>
            <span className="text-slate-400">Total: ₹{Number(order.total_amount).toFixed(2)}</span>
          </div>
        </div>

        <div className="grid gap-6 lg:grid-cols-2">
          <div className="rounded-3xl border border-slate-800 bg-slate-900/80 p-6">
            <h2 className="text-lg font-semibold text-white">Items</h2>
            <div className="mt-4 space-y-4">
              {order.items.map((item, index) => (
                <div key={`${item.product_name}-${index}`} className="rounded-2xl border border-slate-800 bg-slate-950/40 p-4">
                  <div className="flex items-center justify-between gap-3">
                    <div>
                      <p className="font-medium text-white">{item.product_name}</p>
                      {item.variant_name ? <p className="text-sm text-slate-400">{item.variant_name}</p> : null}
                    </div>
                    <p className="text-sm text-slate-200">₹{Number(item.total_price).toFixed(2)}</p>
                  </div>
                  <p className="mt-2 text-sm text-slate-400">Qty: {item.quantity} · Unit: ₹{Number(item.unit_price).toFixed(2)}</p>
                </div>
              ))}
            </div>
          </div>

          <div className="rounded-3xl border border-slate-800 bg-slate-900/80 p-6">
            <h2 className="text-lg font-semibold text-white">Summary</h2>
            <div className="mt-4 space-y-3 text-sm text-slate-300">
              <div className="flex items-center justify-between"><span>Subtotal</span><span>₹{Number(order.subtotal).toFixed(2)}</span></div>
              <div className="flex items-center justify-between"><span>Discount</span><span>-₹{Number(order.discount_amount).toFixed(2)}</span></div>
              <div className="flex items-center justify-between"><span>Shipping</span><span>₹{Number(order.shipping_amount).toFixed(2)}</span></div>
              <div className="flex items-center justify-between"><span>Tax</span><span>₹{Number(order.tax_amount).toFixed(2)}</span></div>
              <div className="flex items-center justify-between border-t border-slate-800 pt-3 text-base font-semibold text-white"><span>Total</span><span>₹{Number(order.total_amount).toFixed(2)}</span></div>
            </div>
          </div>
        </div>
      </div>
    </main>
  );
}
