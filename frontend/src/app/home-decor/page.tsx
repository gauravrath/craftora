import type { Metadata } from 'next';
import Link from 'next/link';
import Image from 'next/image';
import { HomeProductCard } from '@/components/storefront/HomeProductCard';
import { ShopFilters } from '@/components/storefront/ShopFilters';
import { ShopResults } from '@/components/storefront/ShopResults';
import { VideoCard } from '@/components/storefront/VideoCard';
import { getProducts } from '@/lib/catalog';
import { decorGroups, decorKeywords, shopTheLook } from '@/lib/home-decor';
import { diyVideos, youtubeChannelUrl, youtubeVideoUrl } from '@/lib/home-content';
import { productMatchesGroup } from '@/lib/materials';
import type { Product } from '@/types/catalog';

export const metadata: Metadata = {
  title: 'Home Decor',
  description: 'Handmade home decoration, torans, wall and table decor, festive pieces, and gifts from Craftora.',
};

type SearchParams = Record<string, string | string[] | undefined>;

const PAGE_SIZE = 12;

function getParam(params: SearchParams, key: string, fallback = '') {
  const value = params[key];
  return typeof value === 'string' ? value : fallback;
}

export default async function HomeDecorPage({ searchParams }: { searchParams: Promise<SearchParams> }) {
  const params = await searchParams;
  const search = getParam(params, 'search');
  const category = getParam(params, 'category');
  const group = getParam(params, 'group');
  const minPrice = getParam(params, 'min_price');
  const maxPrice = getParam(params, 'max_price');
  const sortBy = getParam(params, 'sort_by', 'created_at');
  const sortDir = getParam(params, 'sort_dir', 'desc');
  const requestedPage = Math.max(1, Number(getParam(params, 'page', '1')) || 1);

  let inventory: Product[] = [];
  let filtered: Product[] = [];
  let unavailable = false;

  try {
    const [allProducts, matching] = await Promise.all([
      getProducts({ per_page: 100, sort_by: 'created_at', sort_dir: 'desc' }),
      getProducts({ search, category, min_price: minPrice, max_price: maxPrice, sort_by: sortBy, sort_dir: sortDir, per_page: 100 }),
    ]);
    inventory = allProducts.data ?? [];
    filtered = matching.data ?? [];
  } catch {
    unavailable = true;
  }

  const isDecor = (product: Product) =>
    productMatchesGroup(product, { keywords: decorKeywords }) || decorGroups.some((item) => productMatchesGroup(product, item));
  const availableGroups = decorGroups.filter((item) => inventory.some((product) => productMatchesGroup(product, item)));
  const activeGroup = availableGroups.find((item) => item.key === group);

  const decor = filtered.filter((product) => (activeGroup ? productMatchesGroup(product, activeGroup) : isDecor(product)));
  const lastPage = Math.max(1, Math.ceil(decor.length / PAGE_SIZE));
  const currentPage = Math.min(requestedPage, lastPage);
  const pageProducts = decor.slice((currentPage - 1) * PAGE_SIZE, currentPage * PAGE_SIZE);

  const decorInventory = inventory.filter(isDecor);
  const featured = decorInventory.filter((product) => product.is_featured).slice(0, 3);
  const newArrivals = decorInventory.slice(0, 3);

  const categoryOptions = [...new Map(
    decorInventory
      .filter((product) => product.category?.id && product.category.name)
      .map((product) => [product.category!.id!, { id: product.category!.id!, name: product.category!.name! }]),
  ).values()].sort((left, right) => left.name.localeCompare(right.name));

  const looks = shopTheLook.map((look) => ({
    ...look,
    video: diyVideos[look.videoIndex],
    related: decorInventory.filter((product) => productMatchesGroup(product, look)).slice(0, 4),
  }));

  const resultsQuery = {
    search,
    category,
    group,
    min_price: minPrice,
    max_price: maxPrice,
    sort_by: sortBy !== 'created_at' ? sortBy : '',
    sort_dir: sortDir !== 'desc' ? sortDir : '',
  };

  const tileHref = (key: string) => {
    const next = new URLSearchParams();
    Object.entries({ ...resultsQuery, group: key }).forEach(([name, value]) => {
      if (value) next.set(name, value);
    });
    return `/home-decor?${next.toString()}#all-decor`;
  };

  return (
    <main className="min-h-screen bg-[#fbf8f3] text-[#332d28]">
      <section className="bg-[#f2e9dc]">
        <div className="mx-auto grid max-w-7xl items-center gap-8 px-5 py-8 sm:px-8 lg:grid-cols-[1fr_0.9fr] lg:px-12 lg:py-12">
          <div>
            <nav aria-label="Breadcrumb" className="mb-6 text-sm text-[#776b5e]">
              <ol className="flex items-center gap-2">
                <li><Link href="/" className="hover:text-[#a64934]">Home</Link></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page" className="font-medium text-[#43392f]">Home Decor</li>
              </ol>
            </nav>
            <p className="text-xs font-semibold uppercase text-[#a64934]">Handmade, for your home</p>
            <h1 className="mt-3 max-w-xl font-serif text-4xl leading-tight text-[#332d28] sm:text-5xl">Make Your Space Beautiful</h1>
            <p className="mt-4 max-w-lg text-base leading-7 text-[#665b50]">Torans, wall and table pieces, festive details, and gifts with a handmade touch.</p>
            <div className="mt-6 flex flex-wrap gap-3">
              <Link href="#all-decor" className="inline-flex min-h-11 items-center rounded-md bg-[#a64934] px-5 text-sm font-semibold text-white hover:bg-[#873b2a]">Shop home decor</Link>
              <Link href="#inspiration" className="inline-flex min-h-11 items-center rounded-md border border-[#b8a894] px-5 text-sm font-semibold text-[#43392f] hover:bg-white/60">Get inspired</Link>
            </div>
          </div>
          <div className="relative min-h-60 overflow-hidden rounded-lg bg-[#d6c4aa] sm:min-h-80">
            <Image src="https://images.unsplash.com/photo-1600210492493-0946911123ea?auto=format&fit=crop&w=1400&q=85" alt="A warm living space with handmade decoration" fill priority sizes="(max-width: 1024px) 100vw, 45vw" className="object-cover" />
          </div>
        </div>
      </section>

      <div className="mx-auto max-w-7xl px-5 py-12 sm:px-8 lg:px-12 lg:py-16">
        {availableGroups.length > 0 ? (
          <section aria-labelledby="decor-groups-heading">
            <div className="mb-4 flex items-center justify-between gap-3">
              <h2 id="decor-groups-heading" className="font-serif text-2xl text-[#332d28]">Shop by style</h2>
              {activeGroup ? <Link href="/home-decor#all-decor" className="text-sm font-semibold text-[#a64934] hover:underline">Show all decor</Link> : null}
            </div>
            <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-5">
              {availableGroups.map((item) => (
                <Link key={item.key} href={tileHref(item.key)} aria-current={item.key === group ? 'page' : undefined} className="group min-w-0">
                  <div className={`relative aspect-[4/3] overflow-hidden rounded-md ${item.tone} ${item.key === group ? 'ring-2 ring-[#a64934] ring-offset-2 ring-offset-[#fbf8f3]' : ''}`}>
                    <Image src={item.image} alt={item.alt} fill sizes="(max-width: 640px) 45vw, (max-width: 1024px) 30vw, 20vw" className="object-cover transition duration-500 group-hover:scale-105" />
                  </div>
                  <p className={`mt-2 text-sm font-semibold group-hover:text-[#a64934] ${item.key === group ? 'text-[#a64934]' : 'text-[#43392f]'}`}>{item.name}</p>
                </Link>
              ))}
            </div>
          </section>
        ) : null}

        {featured.length > 0 ? (
          <section className="mt-12" aria-labelledby="featured-decor-heading">
            <h2 id="featured-decor-heading" className="mb-4 font-serif text-2xl text-[#332d28]">Featured home decor</h2>
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              {featured.map((product) => <HomeProductCard key={product.id} product={product} />)}
            </div>
          </section>
        ) : null}

        {newArrivals.length > 0 ? (
          <section className="mt-12" aria-labelledby="new-decor-heading">
            <h2 id="new-decor-heading" className="mb-4 font-serif text-2xl text-[#332d28]">New arrivals</h2>
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              {newArrivals.map((product) => <HomeProductCard key={product.id} product={product} />)}
            </div>
          </section>
        ) : null}

        <div className="mt-12 scroll-mt-32" id="all-decor">
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
              basePath="/home-decor"
              products={pageProducts}
              total={decor.length}
              currentPage={currentPage}
              lastPage={lastPage}
              unavailable={unavailable}
              query={resultsQuery}
              noun="piece"
            />
          </ShopFilters>
        </div>
      </div>

      <section id="inspiration" className="scroll-mt-32 bg-[#34433a] text-white" aria-labelledby="decor-video-heading">
        <div className="mx-auto max-w-7xl px-5 py-14 sm:px-8 lg:px-12">
          <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
              <p className="text-xs font-semibold uppercase text-[#d7bd88]">Home decoration tutorials</p>
              <h2 id="decor-video-heading" className="mt-2 font-serif text-3xl text-white">Get Inspired</h2>
              <p className="mt-3 max-w-xl text-sm leading-6 text-[#e1e2d9]">Watch how handmade decor comes together, then make it your own.</p>
            </div>
            <a href={youtubeChannelUrl} target="_blank" rel="noreferrer" className="inline-flex min-h-11 items-center rounded-md bg-[#d9694f] px-5 text-sm font-semibold text-white hover:bg-[#bd543d]">Visit our YouTube channel <span aria-hidden="true" className="ml-2">↗</span></a>
          </div>
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {[diyVideos[0], diyVideos[2], diyVideos[1]].map((video) => <VideoCard key={video.title} video={video} />)}
          </div>
        </div>
      </section>

      <section className="mx-auto max-w-7xl px-5 py-14 sm:px-8 lg:px-12 lg:py-16" aria-labelledby="look-heading">
        <h2 id="look-heading" className="font-serif text-3xl text-[#332d28]">Shop This Look</h2>
        <p className="mt-2 max-w-xl text-sm leading-6 text-[#665b50]">See a finished decoration, then find the products that can help you create it.</p>
        <div className="mt-6 grid gap-5 lg:grid-cols-2">
          {looks.map((look) => (
            <article key={look.title} className="grid overflow-hidden rounded-lg border border-[#e9dfd3] bg-white sm:grid-cols-[0.8fr_1.2fr]">
              <div className="relative min-h-48 bg-[#eee5da]">
                <Image src={look.video.thumbnail} alt="" fill sizes="(max-width: 640px) 100vw, 25vw" className="object-cover" />
              </div>
              <div className="flex flex-col p-5 sm:p-6">
                <h3 className="font-serif text-2xl text-[#332d28]">{look.title}</h3>
                <p className="mt-2 text-sm leading-6 text-[#665b50]">{look.description}</p>
                <p className="mt-4 text-xs font-semibold uppercase text-[#77836a]">Products for this look</p>
                {look.related.length > 0 ? (
                  <ul className="mt-2 flex flex-wrap gap-2">
                    {look.related.map((product) => (
                      <li key={product.id}><Link href={`/products/${product.slug}`} className="inline-flex rounded-full border border-[#e3d8cb] px-3 py-1.5 text-xs text-[#554a40] hover:border-[#a64934] hover:text-[#a64934]">{product.name}</Link></li>
                    ))}
                  </ul>
                ) : (
                  <p className="mt-2 text-sm text-[#776b5e]">No matching products in the shop yet.</p>
                )}
                <div className="mt-auto flex flex-wrap gap-4 pt-5 text-sm font-semibold">
                  <a href={youtubeVideoUrl(look.video)} target="_blank" rel="noreferrer" className="text-[#a64934] hover:underline">Watch tutorial <span aria-hidden="true">↗</span></a>
                  <Link href="#all-decor" className="text-[#596a4e] hover:underline">Browse decor <span aria-hidden="true">→</span></Link>
                </div>
              </div>
            </article>
          ))}
        </div>
      </section>
    </main>
  );
}
