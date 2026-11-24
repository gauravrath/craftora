import Image from 'next/image';
import Link from 'next/link';
import { CartIconButton } from '@/components/storefront/CartIconButton';
import { DesktopNav } from '@/components/storefront/DesktopNav';
import { MobileNav } from '@/components/storefront/MobileNav';

export function Header() {
  return (
    <header className="sticky top-0 z-50 border-b border-[#e9dfd3] bg-[#fbf8f3]/95 text-[#332d28] backdrop-blur-xl">
      <div className="mx-auto max-w-7xl px-4 sm:px-8 lg:px-12">
        <div className="flex min-h-[76px] items-center justify-between gap-4">
          <Image src="https://craftora.s3.ap-south-1.amazonaws.com/uploads/logo.png?X-Amz-Algorithm=AWS4-HMAC-SHA256&X-Amz-Content-Sha256=UNSIGNED-PAYLOAD&X-Amz-Credential=ASIAV2QJCU3U27G2N7LR%2F20261121%2Fap-south-1%2Fs3%2Faws4_request&X-Amz-Date=20261121T084117Z&X-Amz-Expires=300&X-Amz-Security-Token=IQoJb3JpZ2luX2VjEHkaCmFwLXNvdXRoLTEiSDBGAiEAns96DSt5ZyHId6vsi4peXjy1sZzO7N1zCtWW8pj5E8UCIQC4Shjy8VaYk2oCkg4J1PPWCS72TZ15heVFQtaosetKOyrlAwhCEAAaDDQwMDUyNDc0ODUyMSIMO8Z8jEEDBXSdVqAlKsIDP%2FFYnFMBMtk0S5nThW9K2qRWygKIbu7h1Xf5Cd3K5OxoF46unliUvDMyya7KdDKCIJ9S4OnFlUMqkIBL9Ug2uunPINuvXsw3nmdXuArlpQYZrJci5nFOeFk5gsQbQZx88eSzYrICCRhTVy4Ln3GG8Fnrm38QAjoutr4O4Y8FGlNOrSIHZxErFOLLvVSiIe33OAkQThXB%2F8pgMG3dq1PtAt80KV1sK5GA%2Bp6DkPlLaeas8KXqzTuwzLwEOL7%2F3MmeD9p1hHnDjC%2FufR8xOl1UbNW3E4VLiqQv9uGVsiety3Nj8uGRI8rVGepmYMx6R7v9FHG9h4%2FDnHYGGzsEa62HHSWxxRMtEr98DJ2VFBrMQwKdg6%2FkhwzA9kzPiXiyQjac7zgBJGlPTag5oiUO3thEcIiFciYYob%2FoG66JqPxobrRo%2BBOLsWIgZWdJFU%2BEto2U5Bc5QB1Khp5XEP%2BBBZlvrl7%2BFMKPbag8tjn0y86z7ED5lvhSRxundhwEwJABKr8MU8gpuVI3LcqBXXGlVAr6f60uA%2BDoqMn0Goi42Nw8v6%2BRt2xWwPmdmBkrVa0L6Te7uCRZeBEsZNWxKOfYqNsRSxMrMKOwhdgGOrkCVYf7D5WqBFbZfshvmQg5tMPsuvMqGdwiYAf7hudAuSwtZJb%2FvId0zqy0eAiH6JY0RcLTZncvbG5vx7Pne2%2Ft4%2FwoQlebcJ3atpOOsLUlqpW5zUD8xzupNEAR5nHU5WOjWG%2FvlDd9vsBkgtZ6LkLdlpRyY2eiQkxyk7HQZOtvPuh%2FUWcgcxYVID%2BP1aaXf9nX4F13il4lRN5VWcSAwjYSQSqHRuKCstKzC4QLNoYugOFFRBvuR2I7TonAFlDpHQaLbuto9USX9D%2FmphViRCkahU1%2BWI%2FxYKu35zpqyRH5BXsnkaH89SyrpzYhyBjMGK2F7Uh4sxtyEsRCFvGnvVs4ckqS0g%2BUmCpQZV6gy3JomnHv%2F2jNKKyyOyaG3dh2s7dODpOfi4HHpOiWGXRD4qO4Qu37p6JkggHxcA%3D%3D&X-Amz-Signature=06d4dcf4fe3b45a26e7d9ea77df8880bfb38bbf2d25583c3b8ba54ecd8e1a9ed&X-Amz-SignedHeaders=host&response-content-disposition=inline" alt="Craftora logo" width={200} height={40} unoptimized />
          <Link href="/" className="flex shrink-0 items-center gap-3" aria-label="Craftora home">
            <div className="flex size-10 items-center justify-center rounded-md bg-[#a64934] font-serif text-xl text-white">C</div>
            <div>
              <p className="font-serif text-xl leading-5 text-[#332d28]">Craftora</p>
              <p className="mt-1 text-[9px] font-medium uppercase text-[#77836a]">Made for making</p>
            </div>
          </Link>

          <form action="/shop" role="search" className="hidden min-w-40 max-w-md flex-1 sm:flex">
            <label htmlFor="site-search" className="sr-only">Search crafts and materials</label>
            <div className="flex w-full items-center rounded-md border border-[#e2d8cc] bg-white focus-within:border-[#a64934]">
              <input id="site-search" name="search" type="search" placeholder="Search crafts, kits, materials..." className="min-w-0 flex-1 bg-transparent px-4 py-2.5 text-sm text-[#332d28] outline-none placeholder:text-[#978b7f]" />
              <button type="submit" aria-label="Search" className="inline-flex size-10 items-center justify-center text-[#776b5e] hover:text-[#a64934]">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" className="size-5" stroke="currentColor" strokeWidth="1.8"><circle cx="10.8" cy="10.8" r="6.8" /><path strokeLinecap="round" d="m16 16 4.5 4.5" /></svg>
              </button>
            </div>
          </form>

          <div className="flex shrink-0 items-center gap-1 sm:gap-2">
            <Link href="/account/profile" aria-label="Your account" className="inline-flex size-10 items-center justify-center rounded-md text-[#51473e] hover:bg-[#f2e9dc] hover:text-[#a64934]">
              <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" className="size-5" stroke="currentColor" strokeWidth="1.7"><circle cx="12" cy="8" r="3.5" /><path strokeLinecap="round" d="M4.8 20a7.2 7.2 0 0 1 14.4 0" /></svg>
            </Link>
            <Link href="/account/profile?tab=wishlist" aria-label="Your wishlist" className="inline-flex size-10 items-center justify-center rounded-md text-[#51473e] hover:bg-[#f2e9dc] hover:text-[#a64934]">
              <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" className="size-5" stroke="currentColor" strokeWidth="1.7"><path strokeLinecap="round" strokeLinejoin="round" d="M20.8 8.8c0 5.3-8.8 10.1-8.8 10.1S3.2 14.1 3.2 8.8A4.7 4.7 0 0 1 12 6.2a4.7 4.7 0 0 1 8.8 2.6Z" /></svg>
            </Link>
            <CartIconButton />
            <MobileNav />
          </div>
        </div>

        <form action="/shop" role="search" className="pb-3 sm:hidden">
          <label htmlFor="mobile-site-search" className="sr-only">Search crafts and materials</label>
          <div className="flex items-center rounded-md border border-[#e2d8cc] bg-white focus-within:border-[#a64934]">
            <input id="mobile-site-search" name="search" type="search" placeholder="Search the shop" className="min-w-0 flex-1 bg-transparent px-3 py-2 text-sm outline-none placeholder:text-[#978b7f]" />
            <button type="submit" aria-label="Search" className="inline-flex size-9 items-center justify-center text-[#776b5e] hover:text-[#a64934]">
              <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" className="size-5" stroke="currentColor" strokeWidth="1.8"><circle cx="10.8" cy="10.8" r="6.8" /><path strokeLinecap="round" d="m16 16 4.5 4.5" /></svg>
            </button>
          </div>
        </form>

        <DesktopNav />
      </div>
    </header>
  );
}
