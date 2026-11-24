import type { Metadata } from 'next';
import Link from 'next/link';
import { VideoCard } from '@/components/storefront/VideoCard';
import { formatPrice, getProducts } from '@/lib/catalog';
import { productMatchesGroup } from '@/lib/materials';
import { channelVideos, videoCategories, videoThumbnail, youtubeChannelUrl, type ChannelVideo } from '@/lib/videos';
import type { Product } from '@/types/catalog';

export const metadata: Metadata = {
  title: 'DIY Videos',
  description: 'Learn. Create. Inspire. Watch Craftora DIY tutorials, then shop the materials for each project.',
};

type SearchParams = Record<string, string | string[] | undefined>;

function getParam(params: SearchParams, key: string) {
  const value = params[key];
  return typeof value === 'string' ? value : '';
}

const toCardVideo = (video: ChannelVideo) => ({
  id: video.id,
  title: video.title,
  description: video.description,
  thumbnail: videoThumbnail(video),
  search: video.search,
});

export default async function DiyVideosPage({ searchParams }: { searchParams: Promise<SearchParams> }) {
  const params = await searchParams;
  const query = getParam(params, 'q').trim();
  const category = getParam(params, 'category');

  let products: Product[] = [];
  try {
    products = (await getProducts({ per_page: 100, sort_by: 'name', sort_dir: 'asc' })).data ?? [];
  } catch {
    products = [];
  }

  const videos = channelVideos.filter((video) => {
    const text = `${video.title} ${video.description} ${video.category}`.toLowerCase();
    return (!query || text.includes(query.toLowerCase())) && (!category || video.category === category);
  });

  const hasFilters = Boolean(query || category);
  const featured = hasFilters ? undefined : channelVideos.find((video) => video.featured);
  const relatedFor = (video: ChannelVideo) => products.filter((product) => productMatchesGroup(product, { keywords: video.productKeywords })).slice(0, 3);
  const mapped = videos.map((video) => ({ video, related: relatedFor(video) })).filter((entry) => entry.related.length > 0);

  const chipHref = (name: string) => {
    const next = new URLSearchParams();
    if (query) next.set('q', query);
    if (name) next.set('category', name);
    const queryString = next.toString();
    return queryString ? `/diy-videos?${queryString}#all-videos` : '/diy-videos#all-videos';
  };
  const chipClass = (active: boolean) =>
    `inline-flex min-h-10 items-center rounded-full border px-4 text-sm font-medium transition ${active ? 'border-[#a64934] bg-[#a64934] text-white' : 'border-[#d9cbbd] bg-white text-[#51473e] hover:border-[#a64934] hover:text-[#a64934]'}`;
  const shopCta = (video: ChannelVideo) => `/shop?search=${encodeURIComponent(video.materialSearch)}`;

  return (
    <main className="min-h-screen bg-[#fbf8f3] text-[#332d28]">
      <section className="bg-[#f2e9dc]">
        <div className="mx-auto max-w-7xl px-5 py-8 sm:px-8 lg:px-12 lg:py-12">
          <nav aria-label="Breadcrumb" className="mb-6 text-sm text-[#776b5e]">
            <ol className="flex items-center gap-2">
              <li><Link href="/" className="hover:text-[#a64934]">Home</Link></li>
              <li aria-hidden="true">/</li>
              <li aria-current="page" className="font-medium text-[#43392f]">DIY Videos</li>
            </ol>
          </nav>
          <p className="text-xs font-semibold uppercase text-[#a64934]">From our channel</p>
          <h1 className="mt-3 max-w-2xl font-serif text-4xl leading-tight text-[#332d28] sm:text-5xl">Learn. Create. Inspire.</h1>
          <p className="mt-4 max-w-xl text-base leading-7 text-[#665b50]">Watch our DIY tutorials, then pick up the materials for each project.</p>
          <div className="mt-6 flex flex-wrap gap-3">
            <a href={youtubeChannelUrl} target="_blank" rel="noreferrer" className="inline-flex min-h-12 items-center rounded-md bg-[#d9694f] px-6 text-sm font-semibold text-white hover:bg-[#bd543d] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#a64934]">
              Subscribe on YouTube<span className="sr-only"> (opens in a new tab)</span><span aria-hidden="true" className="ml-2">↗</span>
            </a>
            <Link href="#all-videos" className="inline-flex min-h-12 items-center rounded-md border border-[#b8a894] px-6 text-sm font-semibold text-[#43392f] hover:bg-white/60">Browse videos</Link>
          </div>
        </div>
      </section>

      {featured ? (
        <section className="bg-[#34433a] text-white" aria-labelledby="featured-video-heading">
          <div className="mx-auto grid max-w-7xl items-center gap-6 px-5 py-12 sm:px-8 lg:grid-cols-[1.2fr_0.8fr] lg:px-12">
            <VideoCard video={toCardVideo(featured)} titleAsHeading={false} />
            <div>
              <p className="text-xs font-semibold uppercase text-[#d7bd88]">Featured video</p>
              <h2 id="featured-video-heading" className="mt-2 font-serif text-3xl text-white">{featured.title}</h2>
              <p className="mt-3 text-sm leading-6 text-[#e1e2d9]">{featured.description}</p>
              <Link href={shopCta(featured)} className="mt-6 inline-flex min-h-11 items-center rounded-md bg-[#d9694f] px-5 text-sm font-semibold text-white hover:bg-[#bd543d]">Shop Materials for This Project</Link>
            </div>
          </div>
        </section>
      ) : null}

      <section id="all-videos" className="mx-auto max-w-7xl scroll-mt-32 px-5 py-12 sm:px-8 lg:px-12 lg:py-14" aria-labelledby="all-videos-heading">
        <h2 id="all-videos-heading" className="font-serif text-3xl text-[#332d28]">All videos</h2>

        <form method="get" action="/diy-videos#all-videos" role="search" aria-label="Search videos" className="mt-5 flex flex-col gap-3 sm:flex-row">
          <label htmlFor="video-search" className="sr-only">Search videos</label>
          <input id="video-search" name="q" type="search" defaultValue={query} placeholder="Search tutorials" className="min-w-0 flex-1 rounded-md border border-[#e2d8cc] bg-white px-4 py-3 text-sm text-[#332d28] outline-none placeholder:text-[#978b7f] focus:border-[#a64934] focus:ring-2 focus:ring-[#a64934]/15" />
          {category ? <input type="hidden" name="category" value={category} /> : null}
          <button type="submit" className="min-h-11 rounded-md bg-[#a64934] px-5 text-sm font-semibold text-white hover:bg-[#873b2a]">Search</button>
        </form>

        <nav aria-label="Video categories" className="mt-4 flex flex-wrap gap-2">
          <Link href={chipHref('')} aria-current={category ? undefined : 'page'} className={chipClass(!category)}>All</Link>
          {videoCategories.map((name) => (
            <Link key={name} href={chipHref(name)} aria-current={category === name ? 'page' : undefined} className={chipClass(category === name)}>{name}</Link>
          ))}
        </nav>

        <p aria-live="polite" className="mt-4 text-sm text-[#776b5e]">{videos.length} {videos.length === 1 ? 'video' : 'videos'}</p>

        {videos.length === 0 ? (
          <div className="mt-6 rounded-md border border-dashed border-[#d8cabb] bg-white px-6 py-14 text-center">
            <h3 className="font-serif text-2xl text-[#43392f]">No videos found</h3>
            <p className="mt-2 text-sm text-[#776b5e]">Try a different search or category.</p>
            <Link href="/diy-videos#all-videos" className="mt-5 inline-flex min-h-10 items-center rounded-md bg-[#a64934] px-4 text-sm font-semibold text-white hover:bg-[#873b2a]">Show all videos</Link>
          </div>
        ) : (
          <div className="mt-6 grid gap-5 rounded-lg bg-[#34433a] p-4 sm:grid-cols-2 sm:p-6 lg:grid-cols-3">
            {videos.map((video) => (
              <div key={video.title} className="flex flex-col">
                <VideoCard video={toCardVideo(video)} showWatchButton />
                <Link href={shopCta(video)} className="mt-3 text-sm font-semibold text-[#d7bd88] hover:underline">Shop Materials for This Project <span aria-hidden="true">→</span></Link>
              </div>
            ))}
          </div>
        )}

        <div className="mt-8 text-center">
          <a href={`${youtubeChannelUrl}/videos`} target="_blank" rel="noreferrer" className="inline-flex min-h-11 items-center rounded-md border border-[#b8a894] px-5 text-sm font-semibold text-[#43392f] hover:bg-[#f2e9dc]">
            View All Videos on YouTube<span className="sr-only"> (opens in a new tab)</span><span aria-hidden="true" className="ml-2">↗</span>
          </a>
        </div>
      </section>

      {mapped.length > 0 ? (
        <section className="border-t border-[#ece3d8] bg-white" aria-labelledby="video-products-heading">
          <div className="mx-auto max-w-7xl px-5 py-12 sm:px-8 lg:px-12 lg:py-14">
            <h2 id="video-products-heading" className="font-serif text-3xl text-[#332d28]">Materials used in our videos</h2>
            <div className="mt-6 grid gap-5 lg:grid-cols-2">
              {mapped.map(({ video, related }) => (
                <article key={video.title} className="rounded-lg border border-[#e9dfd3] bg-[#fbf8f3] p-5 sm:p-6">
                  <p className="text-xs font-semibold uppercase text-[#77836a]">{video.category}</p>
                  <h3 className="mt-1 font-serif text-2xl text-[#332d28]">{video.title}</h3>
                  <ul className="mt-4 flex flex-wrap gap-2">
                    {related.map((product) => (
                      <li key={product.id}>
                        <Link href={`/products/${product.slug}`} className="inline-flex items-center gap-2 rounded-full border border-[#e3d8cb] bg-white px-3 py-1.5 text-xs text-[#554a40] hover:border-[#a64934] hover:text-[#a64934]">
                          <span>{product.name}</span>
                          <span className="font-semibold">{formatPrice(product.discount_price ?? product.price)}</span>
                        </Link>
                      </li>
                    ))}
                  </ul>
                  <Link href={shopCta(video)} className="mt-5 inline-flex min-h-10 items-center rounded-md bg-[#a64934] px-4 text-sm font-semibold text-white hover:bg-[#873b2a]">Shop Materials for This Project</Link>
                </article>
              ))}
            </div>
          </div>
        </section>
      ) : null}
    </main>
  );
}
