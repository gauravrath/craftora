import Link from 'next/link';
import { youtubeChannelUrl } from '@/lib/home-content';

const policyLinks = [
  { href: '/privacy-policy', label: 'Privacy Policy' },
  { href: '/terms-and-conditions', label: 'Terms & Conditions' },
  { href: '/refund-policy', label: 'Returns & Refunds' },
  { href: '/shipping-policy', label: 'Shipping Policy' },
];

export function Footer() {
  return (
    <footer className="border-t border-[#e9dfd3] bg-[#f2e9dc] text-[#332d28]">
      <div className="mx-auto grid max-w-7xl gap-10 px-5 py-12 sm:px-8 lg:grid-cols-[1.4fr_0.8fr_0.9fr_1.2fr] lg:px-12">
        <div>
          <div className="mb-4 flex items-center gap-3">
            <div className="flex size-10 items-center justify-center rounded-md bg-[#a64934] font-serif text-xl text-white">C</div>
            <div>
              <p className="font-serif text-xl text-[#332d28]">Craftora</p>
              <p className="text-[9px] font-medium uppercase text-[#77836a]">Made for making</p>
            </div>
          </div>
          <p className="max-w-md text-sm leading-6 text-[#665b50]">Handmade finds, creative materials, and ideas for the things you can make yourself.</p>
        </div>

        <div>
          <h3 className="mb-4 text-xs font-semibold uppercase text-[#77836a]">Explore</h3>
          <ul className="space-y-3 text-sm text-[#665b50]">
            <li><Link href="/about" className="hover:text-[#a64934]">About Us</Link></li>
            <li><Link href="/shop" className="hover:text-[#a64934]">Shop All</Link></li>
            <li><Link href="/diy-kits" className="hover:text-[#a64934]">DIY Kits</Link></li>
            <li><Link href="/craft-materials" className="hover:text-[#a64934]">Craft Materials</Link></li>
            <li><Link href="/home-decor" className="hover:text-[#a64934]">Home Decor</Link></li>
            <li><Link href="/diy-ideas" className="hover:text-[#a64934]">DIY Ideas</Link></li>
            <li><Link href="/diy-videos" className="hover:text-[#a64934]">DIY Videos</Link></li>
            <li><Link href="/faq" className="hover:text-[#a64934]">FAQ</Link></li>
            <li><Link href="/contact" className="hover:text-[#a64934]">Contact Us</Link></li>
          </ul>
        </div>

        <div>
          <h3 className="mb-4 text-xs font-semibold uppercase text-[#77836a]">Help & policies</h3>
          <ul className="space-y-3 text-sm text-[#665b50]">
            {policyLinks.map((link) => <li key={link.href}><Link href={link.href} className="hover:text-[#a64934]">{link.label}</Link></li>)}
          </ul>
        </div>

        <div>
          <h3 className="font-serif text-xl">A note from our worktable</h3>
          <p className="mt-2 text-sm leading-6 text-[#665b50]">Get creative ideas and new project inspiration in your inbox.</p>
          <div className="mt-4 flex gap-2">
            <label htmlFor="newsletter-email" className="sr-only">Email address for newsletter</label>
            <input id="newsletter-email" type="email" placeholder="Email address" disabled className="min-w-0 flex-1 rounded-md border border-[#ddcfbf] bg-white/70 px-3 py-2 text-sm text-[#665b50] placeholder:text-[#978b7f] disabled:cursor-not-allowed" />
            <button type="button" disabled aria-label="Newsletter signup coming soon" className="rounded-md bg-[#a64934] px-3 py-2 text-sm font-semibold text-white opacity-65">Join</button>
          </div>
          <p className="mt-2 text-xs text-[#776b5e]">Newsletter signup coming soon.</p>
        </div>
      </div>
      <div className="border-t border-[#e1d5c7]">
        <div className="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-4 text-xs text-[#776b5e] sm:flex-row sm:items-center sm:justify-between sm:px-8 lg:px-12">
          <p>© {new Date().getFullYear()} Craftora. All rights reserved.</p>
          <a href={youtubeChannelUrl} target="_blank" rel="noreferrer" className="font-semibold text-[#a64934] hover:underline">Visit Craftora on YouTube ↗</a>
        </div>
      </div>
    </footer>
  );
}
