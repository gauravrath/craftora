'use client';

import { useState } from 'react';
import { addCartItem } from '@/lib/cart';

export function AddToCartButton({ productId, variantId, label = 'Add to cart' }: { productId: string; variantId?: string | null; label?: string }) {
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [message, setMessage] = useState<string | null>(null);

  async function handleClick() {
    setIsSubmitting(true);
    setMessage(null);

    try {
      await addCartItem(productId, 1, variantId ?? null);
      setMessage('Added to cart');
    } catch (error) {
      setMessage(error instanceof Error ? error.message : 'Unable to add item to cart.');
    } finally {
      setIsSubmitting(false);
      window.setTimeout(() => setMessage(null), 1800);
    }
  }

  return (
    <div className="flex flex-col items-start gap-2">
      <button
        type="button"
        onClick={handleClick}
        disabled={isSubmitting}
        className="rounded-full bg-emerald-500 px-6 py-3 text-sm font-semibold text-slate-950 shadow-lg shadow-emerald-500/30 transition hover:bg-emerald-400 disabled:cursor-not-allowed disabled:opacity-70"
      >
        {isSubmitting ? 'Adding...' : label}
      </button>
      {message ? <p className="text-xs text-emerald-300">{message}</p> : null}
    </div>
  );
}
