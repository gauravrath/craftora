'use client';

import Link from 'next/link';
import { useEffect, useState } from 'react';
import { getCart } from '@/lib/cart';

export function CartIconButton() {
  const [count, setCount] = useState(0);

  useEffect(() => {
    async function syncCount() {
      try {
        const cart = await getCart();
        setCount(cart.summary?.item_count ?? 0);
      } catch {
        setCount(0);
      }
    }

    syncCount();
    const interval = window.setInterval(syncCount, 5000);
    return () => window.clearInterval(interval);
  }, []);

  return (
    <Link href="/cart" aria-label={`Shopping cart${count > 0 ? `, ${count} items` : ''}`} className="relative inline-flex size-10 items-center justify-center rounded-md text-[#51473e] transition hover:bg-[#f2e9dc] hover:text-[#a64934]">
      <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" className="size-5" stroke="currentColor" strokeWidth="1.7"><path strokeLinecap="round" strokeLinejoin="round" d="M3.5 4.5h2l2.2 11.1a2 2 0 0 0 2 1.6h7.8a2 2 0 0 0 1.9-1.5L21 8H6.2" /><circle cx="10" cy="20" r="1" /><circle cx="18" cy="20" r="1" /></svg>
      {count > 0 ? (
        <span className="absolute right-0 top-0 inline-flex min-h-5 min-w-5 items-center justify-center rounded-full bg-[#a64934] px-1 text-[10px] font-bold text-white">
          {count}
        </span>
      ) : null}
    </Link>
  );
}
