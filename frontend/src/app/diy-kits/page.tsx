import type { Metadata } from 'next';
import Link from 'next/link';
import Image from 'next/image';
import { HomeProductCard } from '@/components/storefront/HomeProductCard';
import { VideoCard } from '@/components/storefront/VideoCard';
import { getProducts } from '@/lib/catalog';
import { diyVideos, videoProjects, youtubeChannelUrl } from '@/lib/home-content';
import type { Product } from '@/types/catalog';

export const metadata: Metadata = {
  title: 'DIY Kits',
  description: 'Complete DIY craft kits for beginners, hobbyists, and craft lovers, with everything you need to create.',
};

type SearchParams = Record<string, string | string[] | undefined>;

function readList(value: unknown): string[] {
  if (Array.isArray(value)) return value.map(String).filter(Boolean);
  if (typeof value === 'string' && value.trim()) return value.split(',').map((item) => item.trim()).filter(Boolean);
  return [];
}

function KitDetails({ product }: { product: Product }) {
  const specs = product.specifications ?? {};
  const difficulty = [specs.difficulty, specs.difficulty_level, specs.skill_level].find((value) => typeof value === 'string' && value) as string | undefined;
  const materials = readList(specs.included_materials ?? specs.materials ?? specs.whats_included);

  if (!difficulty && materials.length === 0) return null;

  return (
    <div className="rounded-b-lg border border-t-0 border-[#e9dfd3] bg-[#fbf8f3] px-5 py-3 text-xs text-[#665b50]">
      {difficulty ? <p><span className="font-semibold text-[#43392f]">Difficulty:</span> {difficulty}</p> : null}
      {materials.length ? <p className={difficulty ? 'mt-1' : ''}><span className="font-semibold text-[#43392f]">Includes:</span> {materials.join(', ')}</p> : null}
    </div>
  );
}

function KitGrid({ products }: { products: Product[] }) {
  return (
    <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
      {products.map((product) => (
        <div key={product.id}>
          <HomeProductCard product={product} />
          <KitDetails product={product} />
        </div>
      ))}
    </div>
  );
}

export default async function DiyKitsPage({ searchParams }: { searchParams: Promise<SearchParams> }) {
  const params = await searchParams;
  const selectedCategory = typeof params.category === 'string' ? params.category : '';

  let kits: Product[] = [];
  let unavailable = false;

  try {
    const response = await getProducts({ search: 'kit', per_page: 100, sort_by: 'created_at', sort_dir: 'desc' });
    kits = response.data ?? [];
  } catch {
    unavailable = true;
  }

  const categories = [...new Map(
    kits
      .filter((kit) => kit.category?.id && kit.category.name)
      .map((kit) => [kit.category!.id!, { id: kit.category!.id!, name: kit.category!.name! }]),
  ).values()].sort((left, right) => left.name.localeCompare(right.name));

  const visibleKits = selectedCategory ? kits.filter((kit) => String(kit.category?.id) === selectedCategory) : kits;
  const featuredKits = visibleKits.filter((kit) => kit.is_featured).slice(0, 3);
  const tutorialKits = videoProjects.map((project) => {
    const words = project.search.toLowerCase().split(' ').filter((word) => word.length > 3);
    const related = kits.filter((kit) => {
      const text = `${kit.name} ${kit.category?.name ?? ''} ${kit.short_description ?? ''}`.toLowerCase();
      return words.some((word) => text.includes(word));
    }).slice(0, 3);

    return { ...project, related, video: diyVideos[project.videoIndex] };
  });

  const chipClass = (active: boolean) =>
    `inline-flex min-h-10 items-center rounded-full border px-4 text-sm font-medium transition ${active ? 'border-[#a64934] bg-[#a64934] text-white' : 'border-[#d9cbbd] bg-white text-[#51473e] hover:border-[#a64934] hover:text-[#a64934]'}`;

  return (
    <main className="min-h-screen bg-[#fbf8f3] text-[#332d28]">
      <section className="bg-[#f2e9dc]">
        <div className="mx-auto grid max-w-7xl items-center gap-8 px-5 py-8 sm:px-8 lg:grid-cols-[1fr_0.9fr] lg:px-12 lg:py-12">
          <div>
            <nav aria-label="Breadcrumb" className="mb-6 text-sm text-[#776b5e]">
              <ol className="flex items-center gap-2">
                <li><Link href="/" className="hover:text-[#a64934]">Home</Link></li>
                <li aria-hidden="true">/</li>
                <li aria-current="page" className="font-medium text-[#43392f]">DIY Kits</li>
              </ol>
            </nav>
            <p className="text-xs font-semibold uppercase text-[#a64934]">Ready when you are</p>
            <h1 className="mt-3 max-w-xl font-serif text-4xl leading-tight text-[#332d28] sm:text-5xl">DIY Kits – Everything You Need to Create</h1>
            <p className="mt-4 max-w-lg text-base leading-7 text-[#665b50]">Complete craft kits for beginners, hobbyists, and craft lovers. Pick a project, follow along, and make something of your own.</p>
            <div className="mt-6 flex flex-wrap gap-3">
              <Link href="#kits" className="inline-flex min-h-11 items-center rounded-md bg-[#a64934] px-5 text-sm font-semibold text-white hover:bg-[#873b2a]">Shop kits</Link>
              <Link href="#watch" className="inline-flex min-h-11 items-center rounded-md border border-[#b8a894] px-5 text-sm font-semibold text-[#43392f] hover:bg-white/60">Watch before you create</Link>
            </div>
          </div>
          <div className="relative min-h-60 overflow-hidden rounded-lg bg-[#d6c4aa] sm:min-h-80">
            <Image src="https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=1400&q=85" alt="Craft supplies arranged for a DIY project" fill priority sizes="(max-width: 1024px) 100vw, 45vw" className="object-cover" />
          </div>
        </div>
      </section>

      <div className="mx-auto max-w-7xl px-5 py-12 sm:px-8 lg:px-12 lg:py-16">
        {categories.length > 0 ? (
          <nav aria-label="Kit categories" className="mb-10 flex flex-wrap gap-2">
            <Link href="/diy-kits" aria-current={selectedCategory ? undefined : 'page'} className={chipClass(!selectedCategory)}>All kits</Link>
            {categories.map((category) => (
              <Link key={category.id} href={`/diy-kits?category=${category.id}`} aria-current={selectedCategory === String(category.id) ? 'page' : undefined} className={chipClass(selectedCategory === String(category.id))}>
                {category.name}
              </Link>
            ))}
          </nav>
        ) : null}

        {featuredKits.length > 0 ? (
          <section className="mb-14" aria-labelledby="featured-kits-heading">
            <h2 id="featured-kits-heading" className="mb-6 font-serif text-3xl text-[#332d28]">Featured kits</h2>
            <KitGrid products={featuredKits} />
          </section>
        ) : null}

        <section id="kits" className="scroll-mt-32" aria-labelledby="all-kits-heading">
          <div className="mb-6 flex flex-wrap items-end justify-between gap-3">
            <h2 id="all-kits-heading" className="font-serif text-3xl text-[#332d28]">{featuredKits.length > 0 ? 'All kits' : 'Our kits'}</h2>
            {!unavailable ? <p aria-live="polite" className="text-sm text-[#776b5e]">{visibleKits.length} {visibleKits.length === 1 ? 'kit' : 'kits'}</p> : null}
          </div>
          {unavailable ? (
            <div className="rounded-md border border-dashed border-[#d8cabb] bg-white px-6 py-14 text-center">
              <h3 className="font-serif text-2xl text-[#43392f]">Kits are taking a little pause.</h3>
              <p className="mt-2 text-sm text-[#776b5e]">Please check back shortly, or browse the rest of the shop.</p>
              <Link href="/shop" className="mt-5 inline-flex min-h-10 items-center rounded-md bg-[#a64934] px-4 text-sm font-semibold text-white hover:bg-[#873b2a]">Shop all crafts</Link>
            </div>
          ) : visibleKits.length === 0 ? (
            <div className="rounded-md border border-dashed border-[#d8cabb] bg-white px-6 py-14 text-center">
              <h3 className="font-serif text-2xl text-[#43392f]">No kits here yet</h3>
              <p className="mt-2 text-sm text-[#776b5e]">New kits will appear as they are added to the shop.</p>
              <Link href={selectedCategory ? '/diy-kits' : '/shop'} className="mt-5 inline-flex min-h-10 items-center rounded-md bg-[#a64934] px-4 text-sm font-semibold text-white hover:bg-[#873b2a]">{selectedCategory ? 'View all kits' : 'Shop all crafts'}</Link>
            </div>
          ) : (
            <KitGrid products={visibleKits} />
          )}
        </section>
      </div>

      <section id="watch" className="scroll-mt-32 bg-[#34433a] text-white" aria-labelledby="watch-heading">
        <div className="mx-auto max-w-7xl px-5 py-14 sm:px-8 lg:px-12">
          <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
              <p className="text-xs font-semibold uppercase text-[#d7bd88]">See it first</p>
              <h2 id="watch-heading" className="mt-2 font-serif text-3xl text-white">Watch Before You Create</h2>
              <p className="mt-3 max-w-xl text-sm leading-6 text-[#e1e2d9]">Follow along with a tutorial from our channel before you start your kit.</p>
            </div>
            <a href={youtubeChannelUrl} target="_blank" rel="noreferrer" className="inline-flex min-h-11 items-center rounded-md bg-[#d9694f] px-5 text-sm font-semibold text-white hover:bg-[#bd543d]">Visit our YouTube channel <span aria-hidden="true" className="ml-2">↗</span></a>
          </div>
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            {diyVideos.slice(0, 3).map((video) => <VideoCard key={video.title} video={video} />)}
          </div>
        </div>
      </section>

      <section className="mx-auto max-w-7xl px-5 py-14 sm:px-8 lg:px-12 lg:py-16" aria-labelledby="shop-tutorial-heading">
        <h2 id="shop-tutorial-heading" className="font-serif text-3xl text-[#332d28]">Shop the Tutorial</h2>
        <p className="mt-2 max-w-xl text-sm leading-6 text-[#665b50]">Kits that match the projects in our videos.</p>
        <div className="mt-6 grid gap-5 lg:grid-cols-2">
          {tutorialKits.map((project) => (
            <article key={project.title} className="rounded-lg border border-[#e9dfd3] bg-white p-5 sm:p-6">
              <h3 className="font-serif text-2xl text-[#332d28]">{project.title}</h3>
              <p className="mt-2 text-sm leading-6 text-[#665b50]">{project.description}</p>
              <p className="mt-5 text-xs font-semibold uppercase text-[#77836a]">Related kits</p>
              {project.related.length > 0 ? (
                <ul className="mt-2 flex flex-wrap gap-2">
                  {project.related.map((kit) => (
                    <li key={kit.id}><Link href={`/products/${kit.slug}`} className="inline-flex rounded-full border border-[#e3d8cb] px-3 py-1.5 text-xs text-[#554a40] hover:border-[#a64934] hover:text-[#a64934]">{kit.name}</Link></li>
                  ))}
                </ul>
              ) : (
                <p className="mt-2 text-sm text-[#776b5e]">No matching kits in the shop yet.</p>
              )}
              <div className="mt-5 flex flex-wrap gap-4 text-sm font-semibold">
                <a href={`https://www.youtube.com/results?search_query=${encodeURIComponent(project.video.search)}`} target="_blank" rel="noreferrer" className="text-[#a64934] hover:underline">Watch tutorial <span aria-hidden="true">↗</span></a>
                <Link href="#kits" className="text-[#596a4e] hover:underline">Browse kits <span aria-hidden="true">→</span></Link>
              </div>
            </article>
          ))}
        </div>
      </section>
    </main>
  );
}
