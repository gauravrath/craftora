import type { Metadata } from 'next';
import Link from 'next/link';
import Image from 'next/image';
import { HomeProductCard } from '@/components/storefront/HomeProductCard';
import { ShopFilters } from '@/components/storefront/ShopFilters';
import { ShopResults } from '@/components/storefront/ShopResults';
import { VideoCard } from '@/components/storefront/VideoCard';
import { getProducts } from '@/lib/catalog';
import { diyVideos, videoProjects, youtubeChannelUrl } from '@/lib/home-content';
import { materialGroups, productMatchesGroup } from '@/lib/materials';
import type { Product } from '@/types/catalog';

export const metadata: Metadata = {
  title: 'Craft Materials',
  description: 'Beads, threads, MDF bases, embellishments, tools, and adhesives for your next DIY craft project.',
};

type SearchParams = Record<string, string | string[] | undefined>;

const PAGE_SIZE = 12;

function getParam(params: SearchParams, key: string, fallback = '') {
  const value = params[key];
  return typeof value === 'string' ? value : fallback;
}

export default async function CraftMaterialsPage({ searchParams }: { searchParams: Promise<SearchParams> }) {
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
      getProducts({ per_page: 100, sort_by: 'name', sort_dir: 'asc' }),
      getProducts({ search, category, min_price: minPrice, max_price: maxPrice, sort_by: sortBy, sort_dir: sortDir, per_page: 100 }),
    ]);
    inventory = allProducts.data ?? [];
    filtered = matching.data ?? [];
  } catch {
    unavailable = true;
  }

  const isMaterial = (product: Product) => materialGroups.some((item) => productMatchesGroup(product, item));
  const availableGroups = materialGroups.filter((item) => inventory.some((product) => productMatchesGroup(product, item)));
  const activeGroup = availableGroups.find((item) => item.key === group);

  const materials = filtered.filter((product) => (activeGroup ? productMatchesGroup(product, activeGroup) : isMaterial(product)));
  const lastPage = Math.max(1, Math.ceil(materials.length / PAGE_SIZE));
  const currentPage = Math.min(requestedPage, lastPage);
  const pageProducts = materials.slice((currentPage - 1) * PAGE_SIZE, currentPage * PAGE_SIZE);
  const featured = inventory.filter((product) => product.is_featured && isMaterial(product)).slice(0, 3);

  const categoryOptions = [...new Map(
    inventory
      .filter((product) => isMaterial(product) && product.category?.id && product.category.name)
      .map((product) => [product.category!.id!, { id: product.category!.id!, name: product.category!.name! }]),
  ).values()].sort((left, right) => left.name.localeCompare(right.name));

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
    return `/craft-materials?${next.toString()}`;
  };

  return (
    <main className="min-h-screen bg-[#fbf8f3] text-[#332d28]">
      <div className="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:px-12 lg:py-12">
        <nav aria-label="Breadcrumb" className="mb-6 text-sm text-[#776b5e]">
          <ol className="flex items-center gap-2">
            <li><Link href="/" className="hover:text-[#a64934]">Home</Link></li>
            <li aria-hidden="true">/</li>
            <li aria-current="page" className="font-medium text-[#43392f]">Craft Materials</li>
          </ol>
        </nav>

        <header className="max-w-2xl">
          <p className="text-xs font-semibold uppercase text-[#77836a]">For makers, by makers</p>
          <h1 className="mt-2 font-serif text-4xl text-[#332d28] sm:text-5xl">Craft Materials</h1>
          <p className="mt-4 text-base leading-7 text-[#665b50]">Beads, threads, bases, embellishments, and tools to build your own projects from scratch. Pick what you need and start making.</p>
        </header>

        {availableGroups.length > 0 ? (
          <section className="mt-10" aria-labelledby="material-groups-heading">
            <div className="mb-4 flex items-center justify-between gap-3">
              <h2 id="material-groups-heading" className="font-serif text-2xl text-[#332d28]">Browse materials</h2>
              {activeGroup ? <Link href="/craft-materials" className="text-sm font-semibold text-[#a64934] hover:underline">Show all materials</Link> : null}
            </div>
            <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
              {availableGroups.map((item) => (
                <Link key={item.key} href={tileHref(item.key)} aria-current={item.key === group ? 'page' : undefined} className="group min-w-0">
                  <div className={`relative aspect-[4/3] overflow-hidden rounded-md ${item.tone} ${item.key === group ? 'ring-2 ring-[#a64934] ring-offset-2 ring-offset-[#fbf8f3]' : ''}`}>
                    <Image src={item.image} alt={item.alt} fill sizes="(max-width: 640px) 45vw, (max-width: 1024px) 30vw, 16vw" className="object-cover transition duration-500 group-hover:scale-105" />
                  </div>
                  <p className={`mt-2 text-sm font-semibold group-hover:text-[#a64934] ${item.key === group ? 'text-[#a64934]' : 'text-[#43392f]'}`}>{item.name}</p>
                </Link>
              ))}
            </div>
          </section>
        ) : null}

        {featured.length > 0 ? (
          <section className="mt-12" aria-labelledby="featured-materials-heading">
            <h2 id="featured-materials-heading" className="mb-4 font-serif text-2xl text-[#332d28]">Featured materials</h2>
            <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
              {featured.map((product) => <HomeProductCard key={product.id} product={product} />)}
            </div>
          </section>
        ) : null}

        <div className="mt-12" id="all-materials">
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
              basePath="/craft-materials"
              products={pageProducts}
              total={materials.length}
              currentPage={currentPage}
              lastPage={lastPage}
              unavailable={unavailable}
              query={resultsQuery}
              noun="material"
            />
          </ShopFilters>
        </div>
      </div>

      <section className="bg-[#34433a] text-white" aria-labelledby="materials-video-heading">
        <div className="mx-auto max-w-7xl px-5 py-14 sm:px-8 lg:px-12">
          <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
              <p className="text-xs font-semibold uppercase text-[#d7bd88]">Start with a tutorial</p>
              <h2 id="materials-video-heading" className="mt-2 font-serif text-3xl text-white">Find Materials for Your Next DIY</h2>
              <p className="mt-3 max-w-xl text-sm leading-6 text-[#e1e2d9]">Watch a project first, then pick up the materials it uses.</p>
            </div>
            <a href={youtubeChannelUrl} target="_blank" rel="noreferrer" className="inline-flex min-h-11 items-center rounded-md bg-[#d9694f] px-5 text-sm font-semibold text-white hover:bg-[#bd543d]">Visit our YouTube channel <span aria-hidden="true" className="ml-2">↗</span></a>
          </div>
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {diyVideos.slice(0, 3).map((video) => <VideoCard key={video.title} video={video} />)}
          </div>
          <div className="mt-8">
            <p className="text-xs font-semibold uppercase text-[#d7bd88]">Common materials in our tutorials</p>
            <ul className="mt-3 flex flex-wrap gap-2">
              {[...new Set(videoProjects.flatMap((project) => project.materials))].map((material) => (
                <li key={material}>
                  <Link href={`/craft-materials?search=${encodeURIComponent(material)}#all-materials`} className="inline-flex rounded-full border border-white/30 px-3 py-1.5 text-xs text-white hover:bg-white/10">{material}</Link>
                </li>
              ))}
            </ul>
          </div>
        </div>
      </section>
    </main>
  );
}
