import type { Metadata } from 'next';
import Link from 'next/link';
import Image from 'next/image';
import { ShopFilters } from '@/components/storefront/ShopFilters';
import { ShopResults } from '@/components/storefront/ShopResults';
import { craftCategories } from '@/lib/home-content';
import { getProducts } from '@/lib/catalog';
import type { Product } from '@/types/catalog';

export const metadata: Metadata = {
  title: 'Shop All Crafts',
  description: 'Shop handmade decor, DIY craft kits, and creative materials from Craftora.',
};

type SearchParams = Record<string, string | string[] | undefined>;

function getParam(params: SearchParams, key: string, fallback = '') {
  const value = params[key];
  return typeof value === 'string' ? value : fallback;
}

export default async function ShopPage({ searchParams }: { searchParams: Promise<SearchParams> }) {
  const params = await searchParams;
  const search = getParam(params, 'search');
  const category = getParam(params, 'category');
  const minPrice = getParam(params, 'min_price');
  const maxPrice = getParam(params, 'max_price');
  const sortBy = getParam(params, 'sort_by', 'created_at');
  const sortDir = getParam(params, 'sort_dir', 'desc');
  const currentPage = Math.max(1, Number(getParam(params, 'page', '1')) || 1);

  let products: Product[] = [];
  let categoryOptions: { id: number; name: string }[] = [];
  let totalProducts = 0;
  let lastPage = 1;
  let catalogUnavailable = false;

  try {
    const [result, categoryResult] = await Promise.all([
      getProducts({
        search,
        category,
        min_price: minPrice,
        max_price: maxPrice,
        sort_by: sortBy,
        sort_dir: sortDir,
        per_page: 12,
        page: currentPage,
      }),
      getProducts({ per_page: 100, sort_by: 'name', sort_dir: 'asc' }),
    ]);

    products = result.data ?? [];
    totalProducts = result.meta?.total ?? products.length;
    lastPage = result.meta?.last_page ?? 1;
    categoryOptions = [...new Map(
      (categoryResult.data ?? [])
        .filter((product) => product.category?.id && product.category.name)
        .map((product) => [product.category!.id!, { id: product.category!.id!, name: product.category!.name! }]),
    ).values()].sort((left, right) => left.name.localeCompare(right.name));
  } catch {
    catalogUnavailable = true;
  }

  const resultsQuery = {
    search,
    category,
    min_price: minPrice,
    max_price: maxPrice,
    sort_by: sortBy !== 'created_at' ? sortBy : '',
    sort_dir: sortDir !== 'desc' ? sortDir : '',
  };

  return (
    <main className="min-h-screen bg-[#fbf8f3] text-[#332d28]">
      <div className="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:px-12 lg:py-12">
        <nav aria-label="Breadcrumb" className="mb-6 text-sm text-[#776b5e]">
          <ol className="flex items-center gap-2">
            <li><Link href="/" className="hover:text-[#a64934]">Home</Link></li>
            <li aria-hidden="true">/</li>
            <li aria-current="page" className="font-medium text-[#43392f]">Shop All</li>
          </ol>
        </nav>

        <header className="max-w-2xl">
          <p className="text-xs font-semibold uppercase text-[#77836a]">The Craftora collection</p>
          <h1 className="mt-2 font-serif text-4xl text-[#332d28] sm:text-5xl">Shop All Crafts</h1>
          <p className="mt-4 text-base leading-7 text-[#665b50]">Find the materials, DIY kits, and handmade decor to make your next idea feel like yours.</p>
        </header>

        <section className="mt-10" aria-labelledby="shop-categories-heading">
          <div className="mb-4 flex items-center justify-between">
            <h2 id="shop-categories-heading" className="font-serif text-2xl text-[#332d28]">Browse by category</h2>
          </div>
          <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
            {craftCategories.map((item) => (
              <Link key={item.name} href={`/shop?search=${encodeURIComponent(item.search)}`} className="group min-w-0">
                <div className={`relative aspect-[4/3] overflow-hidden rounded-md ${item.tone}`}>
                  <Image src={item.image} alt={item.alt} fill sizes="(max-width: 640px) 45vw, (max-width: 1024px) 30vw, 16vw" className="object-cover transition duration-500 group-hover:scale-105" />
                </div>
                <p className="mt-2 text-sm font-semibold text-[#43392f] group-hover:text-[#a64934]">{item.name}</p>
              </Link>
            ))}
          </div>
        </section>

        <div className="mt-12">
          <ShopFilters
            key={`${search}|${category}|${minPrice}|${maxPrice}|${sortBy}|${sortDir}`}
            categories={categoryOptions}
            initialSearch={search}
            initialCategory={category}
            initialMinPrice={minPrice}
            initialMaxPrice={maxPrice}
            initialSortBy={sortBy}
            initialSortDir={sortDir}
          >
            <ShopResults
              basePath="/shop"
              products={products}
              total={totalProducts}
              currentPage={currentPage}
              lastPage={lastPage}
              unavailable={catalogUnavailable}
              query={resultsQuery}
            />
          </ShopFilters>
        </div>
      </div>
    </main>
  );
}