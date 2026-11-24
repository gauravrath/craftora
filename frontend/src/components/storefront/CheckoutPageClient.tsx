'use client';

import { useEffect, useMemo, useState } from 'react';
import Link from 'next/link';
import { getCheckoutSummary, setShippingMethod, applyCheckoutCoupon, setPaymentMethod, updateCheckoutAddress, validateCheckout, saveCheckoutAddress, removeCheckoutCoupon, createRazorpayOrder, verifyRazorpayPayment } from '@/lib/checkout';
import type { CheckoutSummary, CheckoutAddress } from '@/lib/checkout';
import { formatPrice } from '@/lib/catalog';

declare global {
  interface Window {
    Razorpay?: new (options: any) => { open: () => void; on: (event: string, callback: (...args: any[]) => void) => void };
  }
}

const emptyAddress: CheckoutAddress = {
  full_name: '',
  phone: '',
  address_line_1: '',
  address_line_2: '',
  city: '',
  state: '',
  postal_code: '',
  country: 'United States',
};

export function CheckoutPageClient() {
  const [summary, setSummary] = useState<CheckoutSummary | null>(null);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [shippingAddress, setShippingAddress] = useState<CheckoutAddress>(emptyAddress);
  const [billingAddress, setBillingAddress] = useState<CheckoutAddress>(emptyAddress);
  const [useShippingAsBilling, setUseShippingAsBilling] = useState(true);
  const [shippingMethod, setShippingMethodValue] = useState<'standard' | 'express'>('standard');
  const [paymentMethod, setPaymentMethodValue] = useState<'cod' | 'razorpay'>('cod');
  const [couponCode, setCouponCode] = useState('');
  const [processingOrder, setProcessingOrder] = useState(false);

  async function loadCheckout() {
    setLoading(true);
    try {
      const result = await getCheckoutSummary();
      setSummary(result);
      if (result.shipping_address) setShippingAddress(result.shipping_address);
      if (result.billing_address) setBillingAddress(result.billing_address);
      if (result.shipping_method) setShippingMethodValue(result.shipping_method.code as 'standard' | 'express');
      if (result.payment_method) setPaymentMethodValue(result.payment_method.code as 'cod' | 'razorpay');
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unable to load checkout');
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    loadCheckout();
  }, []);

  const total = useMemo(() => summary?.grand_total ?? summary?.subtotal ?? 0, [summary]);

  async function handleSaveAddress() {
    try {
      setError(null);
      await saveCheckoutAddress(shippingAddress);
      await loadCheckout();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unable to save address');
    }
  }

  async function handleUpdateAddress() {
    try {
      setError(null);
      await updateCheckoutAddress(shippingAddress);
      await loadCheckout();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unable to update address');
    }
  }

  async function handleShippingMethodChange(method: 'standard' | 'express') {
    try {
      setShippingMethodValue(method);
      await setShippingMethod(method);
      await loadCheckout();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unable to select shipping method');
    }
  }

  async function handleCouponApply() {
    try {
      setError(null);
      await applyCheckoutCoupon(couponCode);
      setCouponCode('');
      await loadCheckout();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unable to apply coupon');
    }
  }

  async function handleCouponRemove() {
    try {
      setError(null);
      await removeCheckoutCoupon();
      await loadCheckout();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unable to remove coupon');
    }
  }

  async function handlePaymentChange(method: 'cod' | 'razorpay') {
    try {
      setPaymentMethodValue(method);
      await setPaymentMethod(method);
      await loadCheckout();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unable to select payment method');
    }
  }

  async function handleValidate() {
    try {
      setError(null);
      await validateCheckout({
        shipping_address: shippingAddress,
        billing_address: useShippingAsBilling ? shippingAddress : billingAddress,
        use_shipping_address_as_billing: useShippingAsBilling,
        shipping_method: shippingMethod,
        coupon_code: couponCode,
        payment_method: paymentMethod,
      });
      await loadCheckout();
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Checkout validation failed');
    }
  }

  async function handlePlaceOrder() {
    try {
      setError(null);
      setProcessingOrder(true);

      await validateCheckout({
        shipping_address: shippingAddress,
        billing_address: useShippingAsBilling ? shippingAddress : billingAddress,
        use_shipping_address_as_billing: useShippingAsBilling,
        shipping_method: shippingMethod,
        coupon_code: couponCode,
        payment_method: paymentMethod,
      });

      if (paymentMethod === 'cod') {
        const order = await createRazorpayOrder({ payment_method: 'cod' });
        setProcessingOrder(false);
        setError(null);
        window.alert(`Cash on delivery order created successfully. Order #${order.order_number}`);
        return;
      }

      const order = await createRazorpayOrder({
        payment_method: 'razorpay',
        shipping_address: shippingAddress,
        billing_address: useShippingAsBilling ? shippingAddress : billingAddress,
        use_shipping_address_as_billing: useShippingAsBilling,
        shipping_method: shippingMethod,
        coupon_code: couponCode,
      });

      const script = document.createElement('script');
      script.src = 'https://checkout.razorpay.com/v1/checkout.js';
      script.async = true;

      await new Promise<void>((resolve, reject) => {
        script.onload = () => resolve();
        script.onerror = () => reject(new Error('Unable to load the Razorpay checkout script.'));
        document.body.appendChild(script);
      });

      if (!window.Razorpay) {
        throw new Error('Razorpay is not available in this browser.');
      }

      const razorpay = new window.Razorpay({
        key: order.key_id,
        amount: order.amount,
        currency: order.currency,
        name: 'Craftora',
        description: `Order ${order.order_number}`,
        order_id: order.razorpay_order_id,
        handler: async function (response: any) {
          try {
            await verifyRazorpayPayment({
              order_id: order.order_id,
              razorpay_order_id: response.razorpay_order_id,
              razorpay_payment_id: response.razorpay_payment_id,
              razorpay_signature: response.razorpay_signature,
            });
            window.alert('Payment verified successfully.');
          } catch (err) {
            setError(err instanceof Error ? err.message : 'Payment verification failed');
          } finally {
            setProcessingOrder(false);
          }
        },
        theme: { color: '#0f172a' },
      });

      razorpay.open();
      setProcessingOrder(false);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Unable to place order');
      setProcessingOrder(false);
    }
  }

  if (loading) {
    return <div className="mx-auto max-w-6xl px-4 py-16 text-slate-300">Loading checkout...</div>;
  }

  if (!summary || (summary.cart && summary.cart.item_count === 0)) {
    return (
      <main className="min-h-screen bg-slate-950 px-4 py-16 text-slate-100">
        <div className="mx-auto max-w-3xl rounded-3xl border border-dashed border-slate-700 bg-slate-900/60 p-12 text-center">
          <p className="text-xs uppercase tracking-[0.26em] text-emerald-300">Checkout</p>
          <h1 className="mt-4 text-4xl font-bold">Your cart is empty</h1>
          <p className="mt-4 text-slate-300">Add products before starting checkout.</p>
          <Link href="/shop" className="mt-8 inline-flex rounded-full bg-emerald-500 px-6 py-3 font-semibold text-slate-950">
            Return to shopping
          </Link>
        </div>
      </main>
    );
  }

  return (
    <main className="min-h-screen bg-slate-950 px-4 py-12 text-slate-100 sm:px-6 lg:px-8">
      <div className="mx-auto max-w-6xl">
        <div className="mb-8">
          <p className="text-xs font-medium uppercase tracking-[0.26em] text-emerald-300">Checkout</p>
          <h1 className="mt-3 text-4xl font-bold tracking-tight text-white">Complete your order</h1>
        </div>

        {error ? <div className="mb-6 rounded-2xl border border-rose-500/40 bg-rose-500/10 px-4 py-3 text-sm text-rose-200">{error}</div> : null}

        <div className="grid gap-8 lg:grid-cols-[1.15fr_0.85fr]">
          <section className="space-y-6">
            <div className="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
              <h2 className="text-xl font-semibold text-white">Shipping address</h2>
              <div className="mt-4 grid gap-4 md:grid-cols-2">
                <input value={shippingAddress.full_name} onChange={(e) => setShippingAddress({ ...shippingAddress, full_name: e.target.value })} placeholder="Full name" className="rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                <input value={shippingAddress.phone} onChange={(e) => setShippingAddress({ ...shippingAddress, phone: e.target.value })} placeholder="Phone" className="rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                <input value={shippingAddress.address_line_1} onChange={(e) => setShippingAddress({ ...shippingAddress, address_line_1: e.target.value })} placeholder="Address line 1" className="md:col-span-2 rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                <input value={shippingAddress.address_line_2 ?? ''} onChange={(e) => setShippingAddress({ ...shippingAddress, address_line_2: e.target.value })} placeholder="Address line 2" className="md:col-span-2 rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                <input value={shippingAddress.city} onChange={(e) => setShippingAddress({ ...shippingAddress, city: e.target.value })} placeholder="City" className="rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                <input value={shippingAddress.state} onChange={(e) => setShippingAddress({ ...shippingAddress, state: e.target.value })} placeholder="State" className="rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                <input value={shippingAddress.postal_code} onChange={(e) => setShippingAddress({ ...shippingAddress, postal_code: e.target.value })} placeholder="Postal code" className="rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                <input value={shippingAddress.country} onChange={(e) => setShippingAddress({ ...shippingAddress, country: e.target.value })} placeholder="Country" className="rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
              </div>

              <div className="mt-4 flex flex-wrap gap-3">
                <button type="button" onClick={handleSaveAddress} className="rounded-full bg-emerald-500 px-5 py-2.5 text-sm font-semibold text-slate-950">Save address</button>
                <button type="button" onClick={handleUpdateAddress} className="rounded-full border border-slate-700 bg-slate-950 px-5 py-2.5 text-sm font-semibold text-slate-100">Update selected</button>
              </div>
            </div>

            <div className="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
              <div className="flex items-center justify-between gap-3">
                <h2 className="text-xl font-semibold text-white">Billing address</h2>
                <label className="flex items-center gap-2 text-sm text-slate-300">
                  <input type="checkbox" checked={useShippingAsBilling} onChange={(e) => setUseShippingAsBilling(e.target.checked)} />
                  Use shipping address
                </label>
              </div>

              {!useShippingAsBilling ? (
                <div className="mt-4 grid gap-4 md:grid-cols-2">
                  <input value={billingAddress.full_name} onChange={(e) => setBillingAddress({ ...billingAddress, full_name: e.target.value })} placeholder="Billing full name" className="rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                  <input value={billingAddress.phone} onChange={(e) => setBillingAddress({ ...billingAddress, phone: e.target.value })} placeholder="Billing phone" className="rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                  <input value={billingAddress.address_line_1} onChange={(e) => setBillingAddress({ ...billingAddress, address_line_1: e.target.value })} placeholder="Billing address line 1" className="md:col-span-2 rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                  <input value={billingAddress.address_line_2 ?? ''} onChange={(e) => setBillingAddress({ ...billingAddress, address_line_2: e.target.value })} placeholder="Billing address line 2" className="md:col-span-2 rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                  <input value={billingAddress.city} onChange={(e) => setBillingAddress({ ...billingAddress, city: e.target.value })} placeholder="City" className="rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                  <input value={billingAddress.state} onChange={(e) => setBillingAddress({ ...billingAddress, state: e.target.value })} placeholder="State" className="rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                  <input value={billingAddress.postal_code} onChange={(e) => setBillingAddress({ ...billingAddress, postal_code: e.target.value })} placeholder="Postal code" className="rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                  <input value={billingAddress.country} onChange={(e) => setBillingAddress({ ...billingAddress, country: e.target.value })} placeholder="Country" className="rounded-2xl border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                </div>
              ) : null}
            </div>

            <div className="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
              <h2 className="text-xl font-semibold text-white">Shipping method</h2>
              <div className="mt-4 grid gap-3 md:grid-cols-2">
                {(['standard', 'express'] as const).map((method) => (
                  <button
                    key={method}
                    type="button"
                    onClick={() => handleShippingMethodChange(method)}
                    className={`rounded-2xl border p-4 text-left ${shippingMethod === method ? 'border-emerald-500 bg-emerald-500/10' : 'border-slate-700 bg-slate-950'}`}
                  >
                    <div className="flex items-center justify-between gap-4">
                      <span className="text-base font-semibold text-white">{method === 'standard' ? 'Standard Shipping' : 'Express Shipping'}</span>
                      <span className="text-sm text-emerald-300">{formatPrice(method === 'standard' ? 9.99 : 19.99)}</span>
                    </div>
                    <p className="mt-2 text-sm text-slate-400">{method === 'standard' ? '5 business days' : '2 business days'}</p>
                  </button>
                ))}
              </div>
            </div>

            <div className="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
              <h2 className="text-xl font-semibold text-white">Coupon</h2>
              <div className="mt-4 flex gap-3">
                <input value={couponCode} onChange={(e) => setCouponCode(e.target.value)} placeholder="Enter coupon code" className="w-full rounded-full border border-slate-700 bg-slate-950 px-4 py-3 text-sm text-white" />
                <button type="button" onClick={handleCouponApply} className="rounded-full bg-emerald-500 px-4 py-3 text-sm font-semibold text-slate-950">Apply</button>
              </div>
              {summary?.coupon ? (
                <div className="mt-4 flex items-center justify-between rounded-2xl border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                  <span>{summary.coupon.code}</span>
                  <button type="button" onClick={handleCouponRemove} className="text-emerald-300 underline">Remove</button>
                </div>
              ) : null}
            </div>

            <div className="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
              <h2 className="text-xl font-semibold text-white">Payment method</h2>
              <div className="mt-4 grid gap-3 md:grid-cols-2">
                {(['cod', 'razorpay'] as const).map((method) => (
                  <button
                    key={method}
                    type="button"
                    onClick={() => handlePaymentChange(method)}
                    className={`rounded-2xl border p-4 text-left ${paymentMethod === method ? 'border-emerald-500 bg-emerald-500/10' : 'border-slate-700 bg-slate-950'}`}
                  >
                    <div className="text-base font-semibold text-white">{method === 'cod' ? 'Cash on Delivery' : 'Razorpay'}</div>
                    <div className="mt-2 text-sm text-slate-400">{method === 'cod' ? 'Pay when your order arrives' : 'Secure online payment (Phase 8 integration)'} </div>
                  </button>
                ))}
              </div>
            </div>
          </section>

          <aside className="space-y-6 rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
            <div>
              <h2 className="text-xl font-semibold text-white">Order summary</h2>
            </div>

            <div className="space-y-3 text-sm text-slate-300">
              {summary?.items?.map((item) => (
                <div key={`${item.product_id}-${item.variant ?? 'standard'}`} className="flex justify-between gap-4">
                  <div>
                    <p className="font-medium text-white">{item.product_name}</p>
                    <p className="text-xs text-slate-400">Qty {item.quantity}</p>
                  </div>
                  <span>{formatPrice(item.line_total)}</span>
                </div>
              ))}
            </div>

            <div className="space-y-3 border-t border-slate-800 pt-4 text-sm text-slate-300">
              <div className="flex justify-between"><span>Subtotal</span><span>{formatPrice(summary?.subtotal ?? 0)}</span></div>
              <div className="flex justify-between"><span>Discount</span><span>-{formatPrice(summary?.discount ?? 0)}</span></div>
              <div className="flex justify-between"><span>Tax</span><span>{formatPrice(summary?.tax ?? 0)}</span></div>
              <div className="flex justify-between"><span>Shipping</span><span>{formatPrice(summary?.shipping ?? 0)}</span></div>
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
              <div className="flex items-center justify-between text-base font-semibold text-white">
                <span>Grand total</span>
                <span>{formatPrice(total)}</span>
              </div>
            </div>

            <div className="space-y-3">
              <button type="button" onClick={handleValidate} className="w-full rounded-full bg-white px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-100">
                Review checkout
              </button>
              <button type="button" onClick={handlePlaceOrder} disabled={processingOrder} className="w-full rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-slate-950 transition hover:bg-emerald-400 disabled:cursor-not-allowed disabled:opacity-70">
                {processingOrder ? 'Processing...' : paymentMethod === 'razorpay' ? 'Pay with Razorpay' : 'Place COD order'}
              </button>
            </div>
          </aside>
        </div>
      </div>
    </main>
  );
}
