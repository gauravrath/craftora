'use client';

import Link from 'next/link';
import { useEffect, useMemo, useState } from 'react';
import { applyCartCoupon, getCart, removeCartCoupon, removeCartItem, updateCartItem } from '@/lib/cart';
import type { CartPayload } from '@/lib/cart';
import { formatPrice } from '@/lib/catalog';

export function CartPageClient() {
  const [cart, setCart] = useState<CartPayload | null>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [couponCode, setCouponCode] = useState('');
  const [error, setError] = useState<string | null>(null);

  async function loadCart() {
    setIsLoading(true);
    try {
      const response = await getCart();
      setCart(response);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unable to load cart.');
    } finally {
      setIsLoading(false);
    }
  }

  useEffect(() => {
    loadCart();
  }, []);

  async function handleQuantityChange(productId: string, nextQuantity: number) {
    if (!cart || nextQuantity < 0) {
      return;
    }

    if (nextQuantity === 0) {
      await removeCartItem(productId);
      await loadCart();
      return;
    }

    await updateCartItem(productId, nextQuantity);
    await loadCart();
  }

  async function handleApplyCoupon() {
    if (!couponCode.trim()) {
      setError('Enter a coupon code.');
      return;
    }

    try {
      await applyCartCoupon(couponCode.trim());
      setCouponCode('');
      setError(null);
      await loadCart();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unable to apply coupon.');
    }
  }

  async function handleRemoveCoupon() {
    try {
      await removeCartCoupon();
      setError(null);
      await loadCart();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unable to remove coupon.');
    }
  }

  const summary = useMemo(() => cart?.summary ?? null, [cart]);

  if (isLoading) {
    return <div className="mx-auto max-w-6xl px-4 py-16 text-slate-300">Loading your cart...</div>;
  }

  if (!cart || cart.items.length === 0) {
    return (
      <main className="min-h-screen bg-slate-950 px-4 py-16 text-slate-100">
        <div className="mx-auto max-w-3xl rounded-3xl border border-dashed border-slate-700 bg-slate-900/60 p-12 text-center">
          <p className="text-xs font-medium uppercase tracking-[0.26em] text-emerald-300">Cart</p>
          <h1 className="mt-4 text-4xl font-bold">Your cart is empty</h1>
          <p className="mt-4 text-slate-300">Add a few wellness essentials to get started.</p>
          <Link href="/shop" className="mt-8 inline-flex rounded-full bg-emerald-500 px-6 py-3 font-semibold text-slate-950">
            Continue shopping
          </Link>
        </div>
      </main>
    );
  }

  return (
    <main className="min-h-screen bg-slate-950 px-4 py-12 text-slate-100 sm:px-6 lg:px-8">
      <div className="mx-auto max-w-6xl">
        <div className="mb-8 flex items-end justify-between gap-4">
          <div>
            <p className="text-xs font-medium uppercase tracking-[0.26em] text-emerald-300">Cart</p>
            <h1 className="mt-3 text-4xl font-bold tracking-tight text-white">Your shopping cart</h1>
          </div>
          <Link href="/shop" className="text-sm text-slate-300 hover:text-emerald-300">Continue shopping</Link>
        </div>

        <div className="grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
          <section className="space-y-4">
            {cart.items.map((item) => (
              <div key={item.id} className="rounded-3xl border border-slate-800 bg-slate-900/70 p-5">
                <div className="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                  <div>
                    <p className="text-lg font-semibold text-white">{item.product?.name ?? 'Product'}</p>
                    <p className="mt-1 text-sm text-slate-400">{item.variant?.name ?? 'Standard option'}</p>
                  </div>

                  <div className="flex items-center gap-3">
                    <button
                      type="button"
                      onClick={() => handleQuantityChange(item.product_id, item.quantity - 1)}
                      className="h-9 w-9 rounded-full border border-slate-700 bg-slate-950 text-lg text-slate-200"
                      aria-label="Decrease quantity"
                    >
                      −
                    </button>
                    <span className="min-w-8 text-center text-base font-semibold text-white">{item.quantity}</span>
                    <button
                      type="button"
                      onClick={() => handleQuantityChange(item.product_id, item.quantity + 1)}
                      className="h-9 w-9 rounded-full border border-slate-700 bg-slate-950 text-lg text-slate-200"
                      aria-label="Increase quantity"
                    >
                      +
                    </button>
                  </div>

                  <div className="flex items-center gap-4">
                    <p className="text-lg font-bold text-emerald-300">{formatPrice(item.total_price)}</p>
                    <button
                      type="button"
                      onClick={() => handleQuantityChange(item.product_id, 0)}
                      className="text-sm text-slate-400 hover:text-rose-300"
                    >
                      Remove
                    </button>
                  </div>
                </div>
              </div>
            ))}
          </section>

          <aside className="space-y-6 rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
            <div>
              <h2 className="text-xl font-semibold text-white">Order summary</h2>
            </div>

            <div className="space-y-3 text-sm text-slate-300">
              <div className="flex justify-between"><span>Subtotal</span><span>{formatPrice(summary?.subtotal ?? 0)}</span></div>
              <div className="flex justify-between"><span>Discount</span><span>-{formatPrice(summary?.discount_amount ?? 0)}</span></div>
              <div className="flex justify-between"><span>Tax</span><span>{formatPrice(summary?.tax_total ?? 0)}</span></div>
              <div className="flex justify-between"><span>Shipping</span><span>{formatPrice(summary?.shipping_amount ?? 0)}</span></div>
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
              <div className="flex items-center justify-between text-base font-semibold text-white">
                <span>Total</span>
                <span>{formatPrice(summary?.total ?? 0)}</span>
              </div>
            </div>

            <div className="space-y-3">
              <label className="block text-sm text-slate-300">Coupon code</label>
              <div className="flex gap-2">
                <input
                  value={couponCode}
                  onChange={(event) => setCouponCode(event.target.value)}
                  placeholder="SAVE10"
                  className="w-full rounded-full border border-slate-700 bg-slate-950 px-4 py-2.5 text-sm text-white placeholder:text-slate-500 focus:border-emerald-500 outline-none"
                />
                <button type="button" onClick={handleApplyCoupon} className="rounded-full bg-emerald-500 px-4 py-2.5 text-sm font-semibold text-slate-950">
                  Apply
                </button>
              </div>
              {cart.coupon ? (
                <button type="button" onClick={handleRemoveCoupon} className="text-sm text-emerald-300 underline">
                  Remove {cart.coupon.code}
                </button>
              ) : null}
            </div>

            {error ? <p className="text-sm text-rose-300">{error}</p> : null}

            <Link href="/checkout" className="inline-flex w-full items-center justify-center rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-100">
              Proceed to checkout
            </Link>
          </aside>
        </div>
      </div>
    </main>
  );
}
