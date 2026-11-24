'use client';

import { useState } from 'react';

export function WishlistButton() {
  const [saved, setSaved] = useState(false);

  return (
    <button
      type="button"
      aria-label={saved ? 'Remove from wishlist' : 'Add to wishlist'}
      aria-pressed={saved}
      onClick={() => setSaved((current) => !current)}
      className="absolute right-3 top-3 inline-flex size-10 items-center justify-center rounded-full bg-white/95 text-[#5b5149] shadow-sm transition hover:scale-105 hover:text-[#bc563d] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#bc563d]"
    >
      <svg aria-hidden="true" viewBox="0 0 24 24" fill={saved ? 'currentColor' : 'none'} className="size-5" stroke="currentColor" strokeWidth="1.8">
        <path strokeLinecap="round" strokeLinejoin="round" d="M20.8 8.8c0 5.3-8.8 10.1-8.8 10.1S3.2 14.1 3.2 8.8A4.7 4.7 0 0 1 12 6.2a4.7 4.7 0 0 1 8.8 2.6Z" />
      </svg>
    </button>
  );
}