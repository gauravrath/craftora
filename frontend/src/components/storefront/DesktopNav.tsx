'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { isActiveRoute, mainNavigation } from '@/lib/navigation';

export function DesktopNav() {
  const pathname = usePathname();

  return (
    <nav aria-label="Main navigation" className="hidden min-h-11 items-center justify-center gap-7 border-t border-[#ece3d8] text-sm lg:flex">
      {mainNavigation.map((item) => {
        const active = isActiveRoute(pathname, item.href);

        return (
          <Link
            key={item.href}
            href={item.href}
            aria-current={active ? 'page' : undefined}
            className={`whitespace-nowrap border-b-2 py-3 transition ${active ? 'border-[#a64934] font-semibold text-[#a64934]' : 'border-transparent text-[#62574d] hover:text-[#a64934]'}`}
          >
            {item.label}
          </Link>
        );
      })}
    </nav>
  );
}
