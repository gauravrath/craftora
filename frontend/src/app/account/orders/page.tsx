"use client";

import Link from 'next/link';
import { useEffect, useState } from 'react';
import { useRouter } from 'next/navigation';
import { getOrders, readAuthSession } from '@/lib/auth';
import type { OrderRecord } from '@/types/auth';

export default function OrdersPage() {
  const router = useRouter();
  const [orders, setOrders] = useState<OrderRecord[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const session = readAuthSession();
    if (!session) {
      router.replace('/login');
      return;
    }

    getOrders()
      .then((data) => setOrders(data))
      .catch((err) => {
        setError(err instanceof Error ? err.message : 'Unable to load orders.');
      })
      .finally(() => setLoading(false));
  }, [router]);

  return (
    <main className="min-h-screen bg-slate-950 px-6 py-12 text-slate-100">
      <div className="mx-auto max-w-5xl">
        <div className="mb-6 flex items-center justify-between gap-3">
          <div>
            <p className="text-sm uppercase tracking-[0.2em] text-emerald-300">Orders</p>
            <h1 className="mt-2 text-3xl font-bold text-white">Your purchases</h1>
          </div>
          <Link href="/account/profile" className="rounded-xl border border-slate-700 bg-slate-900 px-4 py-2 text-sm font-medium text-slate-200 hover:border-slate-500">
            Back to profile
          </Link>
        </div>

        {loading ? (
          <div className="rounded-3xl border border-slate-800 bg-slate-900/80 p-6 text-slate-300">Loading your orders...</div>
        ) : error ? (
          <div className="rounded-3xl border border-rose-500/40 bg-rose-500/10 p-6 text-rose-200">{error}</div>
        ) : orders.length === 0 ? (
          <div className="rounded-3xl border border-dashed border-slate-700 bg-slate-900/60 p-10 text-center text-slate-300">
            No orders yet. Start shopping to see your order history here.
          </div>
        ) : (
          <div className="space-y-4">
            {orders.map((order) => (
              <article key={order.id} className="rounded-3xl border border-slate-800 bg-slate-900/80 p-5 shadow-lg shadow-slate-950/20">
                <div className="flex flex-col gap-3 border-b border-slate-800 pb-4 md:flex-row md:items-center md:justify-between">
                  <div>
                    <p className="text-xs uppercase tracking-[0.2em] text-slate-400">Order</p>
                    <h2 className="mt-1 text-xl font-semibold text-white">{order.order_number}</h2>
                  </div>
                  <div className="flex items-center gap-3 text-sm text-slate-300">
                    <span className="rounded-full border border-emerald-500/30 bg-emerald-500/10 px-2.5 py-1 text-emerald-200">{order.status}</span>
                    <span className="text-slate-400">₹{Number(order.total_amount).toFixed(2)}</span>
                  </div>
                </div>

                <div className="mt-4 grid gap-4 md:grid-cols-3">
                  <div>
                    <p className="text-xs uppercase tracking-[0.2em] text-slate-500">Payment</p>
                    <p className="mt-2 text-sm text-slate-200">{order.payment_status}</p>
                  </div>
                  <div>
                    <p className="text-xs uppercase tracking-[0.2em] text-slate-500">Items</p>
                    <p className="mt-2 text-sm text-slate-200">{order.items.reduce((sum, item) => sum + item.quantity, 0)} items</p>
                  </div>
                  <div>
                    <p className="text-xs uppercase tracking-[0.2em] text-slate-500">Placed</p>
                    <p className="mt-2 text-sm text-slate-200">{order.created_at ? new Date(order.created_at).toLocaleDateString() : 'Recently'}</p>
                  </div>
                </div>

                <div className="mt-5 flex flex-wrap gap-3">
                  <Link href={`/account/orders/${order.id}`} className="rounded-xl border border-sky-500/40 bg-sky-500/10 px-4 py-2 text-sm font-medium text-sky-200 hover:bg-sky-500/20">
                    View details
                  </Link>
                </div>
              </article>
            ))}
          </div>
        )}
      </div>
    </main>
  );
}
