import Link from 'next/link';
import { HomeProductCard } from '@/components/storefront/HomeProductCard';
import type { Product } from '@/types/catalog';

export function ShopResults({
  basePath,
  products,
  total,
  currentPage,
  lastPage,
  unavailable,
  query,
  noun = 'craft',
}: {
  basePath: string;
  products: Product[];
  total: number;
  currentPage: number;
  lastPage: number;
  unavailable: boolean;
  query: Record<string, string>;
  noun?: string;
}) {
  const pageHref = (page: number) => {
    const params = new URLSearchParams();
    Object.entries(query).forEach(([key, value]) => {
      if (value) params.set(key, value);
    });
    if (page > 1) params.set('page', String(page));
    const queryString = params.toString();
    return queryString ? `${basePath}?${queryString}` : basePath;
  };

  const edgeLink = 'inline-flex min-h-10 items-center rounded-md border border-[#d9cbbd] px-4 text-sm font-semibold text-[#51473e] hover:bg-white';

  return (
    <div>
      <div className="mb-4 flex items-center justify-between text-sm text-[#776b5e]">
        <p aria-live="polite">{unavailable ? 'Products are temporarily unavailable.' : `${total} ${total === 1 ? noun : `${noun}s`}`}</p>
        {lastPage > 1 ? <p>Page {currentPage} of {lastPage}</p> : null}
      </div>

      {unavailable ? (
        <div className="rounded-md border border-dashed border-[#d8cabb] bg-white px-6 py-14 text-center">
          <h2 className="font-serif text-2xl text-[#43392f]">The shop is taking a little pause.</h2>
          <p className="mt-2 text-sm text-[#776b5e]">Please try again shortly. Your filters are ready when the catalog returns.</p>
        </div>
      ) : products.length === 0 ? (
        <div className="rounded-md border border-dashed border-[#d8cabb] bg-white px-6 py-14 text-center">
          <h2 className="font-serif text-2xl text-[#43392f]">No {noun}s found</h2>
          <p className="mt-2 text-sm text-[#776b5e]">Try a different search or clear one of your filters.</p>
          <Link href={basePath} className="mt-5 inline-flex min-h-10 items-center rounded-md bg-[#a64934] px-4 text-sm font-semibold text-white hover:bg-[#873b2a]">Clear filters</Link>
        </div>
      ) : (
        <div className="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
          {products.map((product) => <HomeProductCard key={product.id} product={product} />)}
        </div>
      )}

      {lastPage > 1 && products.length > 0 ? (
        <nav aria-label="Product pages" className="mt-8 flex flex-wrap items-center justify-center gap-2">
          {currentPage > 1 ? <Link href={pageHref(currentPage - 1)} className={edgeLink}>Previous</Link> : null}
          {Array.from({ length: lastPage }, (_, index) => index + 1).map((page) => (
            <Link key={page} href={pageHref(page)} aria-current={page === currentPage ? 'page' : undefined} className={`inline-flex size-10 items-center justify-center rounded-md border text-sm font-semibold ${page === currentPage ? 'border-[#a64934] bg-[#a64934] text-white' : 'border-[#d9cbbd] bg-white text-[#51473e] hover:border-[#a64934]'}`}>
              {page}
            </Link>
          ))}
          {currentPage < lastPage ? <Link href={pageHref(currentPage + 1)} className={edgeLink}>Next</Link> : null}
        </nav>
      ) : null}
    </div>
  );
}
