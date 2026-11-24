import Link from 'next/link';
import { ProductFilters } from '@/components/storefront/ProductFilters';
import { ProductCard } from '@/components/storefront/ProductCard';
import { getProducts } from '@/lib/catalog';

export default async function ProductsPage({
  searchParams,
}: {
  searchParams?: Promise<Record<string, string | string[] | undefined>>;
}) {
  const params = (await searchParams) ?? {};
  const query = {
    search: typeof params.search === 'string' ? params.search : undefined,
    sort_by: typeof params.sort_by === 'string' ? params.sort_by : 'created_at',
    sort_dir: typeof params.sort_dir === 'string' ? params.sort_dir : 'desc',
    per_page: typeof params.per_page === 'string' ? params.per_page : '12',
    page: typeof params.page === 'string' ? params.page : '1',
    min_price: typeof params.min_price === 'string' ? params.min_price : undefined,
    max_price: typeof params.max_price === 'string' ? params.max_price : undefined,
  };

  const response = await getProducts(query);
  const products = response.data ?? [];
  const totalPages = response.meta?.last_page ?? 1;
  const currentPage = response.meta?.current_page ?? 1;

  return (
    <main className="min-h-screen bg-slate-950 text-slate-100">
      <div className="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <div className="mb-8 flex items-end justify-between gap-4">
          <div>
            <p className="text-xs font-medium uppercase tracking-[0.26em] text-emerald-300">Shop</p>
            <h1 className="mt-3 text-4xl font-bold tracking-tight text-white">Wellness essentials</h1>
          </div>
          <Link href="/" className="text-sm text-slate-300 hover:text-emerald-300">Back home</Link>
        </div>

        <ProductFilters />

        <div className="mt-8 flex items-center justify-between text-sm text-slate-400">
          <p>{response.meta?.total ?? 0} products</p>
          <p>Page {currentPage} of {totalPages}</p>
        </div>

        {products.length === 0 ? (
          <div className="mt-10 rounded-3xl border border-dashed border-slate-700 bg-slate-900/60 p-12 text-center text-slate-300">
            No products match your current filters.
          </div>
        ) : (
          <div className="mt-8 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            {products.map((product) => (
              <ProductCard key={product.id} product={product} />
            ))}
          </div>
        )}

        {totalPages > 1 ? (
          <div className="mt-10 flex items-center justify-center gap-3">
            {Array.from({ length: totalPages }, (_, index) => index + 1).map((page) => (
              <Link
                key={page}
                href={`/products${page === 1 ? '' : `?page=${page}`}`}
                className={`inline-flex h-10 w-10 items-center justify-center rounded-full border text-sm font-medium ${page === currentPage ? 'border-emerald-500 bg-emerald-500 text-slate-950' : 'border-slate-700 bg-slate-900 text-slate-200 hover:border-slate-500'}`}
              >
                {page}
              </Link>
            ))}
          </div>
        ) : null}
      </div>
    </main>
  );
}
