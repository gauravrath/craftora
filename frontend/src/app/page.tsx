import Link from 'next/link';
import Image from 'next/image';
import { connection } from 'next/server';
import { formatPrice, getCategories, getProducts } from '@/lib/catalog';
import { HomeProductCard } from '@/components/storefront/HomeProductCard';
import { VideoCard } from '@/components/storefront/VideoCard';
import { diyIdeas, diyVideos, videoProjects, youtubeChannelUrl, youtubeVideoUrl } from '@/lib/home-content';
import type { Product, StoreCategory } from '@/types/catalog';

export const metadata = {
  title: 'Craftora | Handmade Finds & DIY Inspiration',
  description: 'Discover handmade decor, thoughtful DIY kits, and craft materials to bring your creative ideas to life. Shop the store and learn with Craftora tutorials.',
  openGraph: {
    title: 'Craftora | Handmade Finds & DIY Inspiration',
    description: 'Shop handmade decor and craft materials, then make something lovely with Craftora DIY tutorials.',
    type: 'website' as const,
  },
};

export default async function HomePage() {
  await connection();

  let products: Product[] = [];
  let catalogUnavailable = false;
  let categories: StoreCategory[] = [];
  let categoriesUnavailable = false;

  try {
    const response = await getCategories();
    categories = response.data;
  } catch (error) {
    console.error('Failed to load categories', error);
    categoriesUnavailable = true;
  }

  try {
    const response = await getProducts({ per_page: 6, sort_by: 'created_at', sort_dir: 'desc' });
    products = response.data ?? [];
  } catch {
    catalogUnavailable = true;
  }

  const projectMaterials = videoProjects.map((project) => {
    const words = project.search.toLowerCase().split(' ').filter((word) => word.length > 3);
    const related = products.filter((product) => {
      const productText = `${product.name} ${product.category?.name ?? ''} ${product.short_description ?? ''}`.toLowerCase();
      return words.some((word) => productText.includes(word));
    }).slice(0, 3);

    return { ...project, related };
  });

  return (
    <main className="min-h-screen bg-[#fbf8f3] text-[#332d28]">
      <section className="overflow-hidden bg-[#f2e9dc]">
        <div className="mx-auto grid max-w-7xl items-center gap-10 px-5 py-12 sm:px-8 sm:py-16 lg:grid-cols-[0.9fr_1.1fr] lg:px-12 lg:py-20">
          <div className="relative z-10 py-2">
            <p className="text-xs font-semibold uppercase text-[#a64934]">A little more handmade, every day</p>
            <h1 className="mt-5 max-w-xl font-serif text-5xl leading-[1.08] text-[#332d28] sm:text-6xl">
              Create Something Beautiful, Every Day!
            </h1>
            <p className="mt-6 max-w-lg text-base leading-7 text-[#665b50] sm:text-lg">
              Discover handmade decor, DIY craft supplies, and everything you need to bring your creative ideas to life.
            </p>
            <div className="mt-8 flex flex-wrap gap-3">
              <Link href="/shop" className="inline-flex min-h-12 items-center justify-center rounded-md bg-[#a64934] px-6 py-3 text-sm font-semibold text-white transition hover:bg-[#873b2a] focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#a64934]">
                Shop crafts <span aria-hidden="true" className="ml-2">→</span>
              </Link>
              <Link href="/diy-videos" className="inline-flex min-h-12 items-center justify-center rounded-md border border-[#b8a894] bg-transparent px-6 py-3 text-sm font-semibold text-[#43392f] transition hover:bg-white/60 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-[#a64934]">
                Watch DIY videos <span aria-hidden="true" className="ml-2">↗</span>
              </Link>
            </div>
            <p className="mt-8 text-sm text-[#776b5e]">Find your next project, then make it yours.</p>
          </div>
          <div className="relative min-h-[340px] overflow-hidden rounded-lg bg-[#d6c4aa] sm:min-h-[470px]">
            <Image
              src="https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=1800&q=90"
              alt="A bright, welcoming table ready for a creative craft project"
              fill
              priority
              sizes="(max-width: 1024px) 100vw, 55vw"
              className="object-cover"
            />
            <div className="absolute bottom-4 left-4 max-w-xs rounded-md bg-[#fbf8f3]/95 px-4 py-3 shadow-lg sm:bottom-6 sm:left-6">
              <p className="text-xs font-semibold uppercase text-[#77836a]">Make room for making</p>
              <p className="mt-1 font-serif text-lg text-[#332d28]">Supplies, ideas, and a little inspiration.</p>
            </div>
          </div>
        </div>
      </section>

      <section className="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:px-12 lg:py-20" aria-labelledby="categories-heading">
        <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
          <div>
            <p className="text-xs font-semibold uppercase text-[#77836a]">A good place to begin</p>
            <h2 id="categories-heading" className="mt-2 font-serif text-3xl text-[#332d28] sm:text-4xl">Shop by category</h2>
          </div>
          <Link href="/shop" className="text-sm font-semibold text-[#a64934] hover:underline">Explore the shop <span aria-hidden="true">→</span></Link>
        </div>
        {categoriesUnavailable ? (
          <p role="status" className="text-sm text-[#776b5e]">Categories are currently unavailable.</p>
        ) : categories.length === 0 ? (
          <p className="text-sm text-[#776b5e]">No categories are available yet.</p>
        ) : (
          <div className="grid grid-cols-2 gap-3 sm:grid-cols-3 sm:gap-5 lg:grid-cols-6">
            {categories.map((category) => (
              <Link key={category.id} href={`/shop?category=${category.id}`} className="group min-w-0">
                <div className="relative aspect-[4/5] overflow-hidden rounded-md bg-[#f4e4d6]">
                  {category.image_url ? (
                    /\.(mp4|mov|webm)(?:[?#]|$)/i.test(category.image_url) ? (
                      <video src={category.image_url} muted autoPlay loop playsInline aria-label={category.name} className="h-full w-full object-cover" />
                    ) : (
                      <Image src={category.image_url} alt={category.name} fill unoptimized sizes="(max-width: 640px) 45vw, (max-width: 1024px) 30vw, 16vw" className="object-cover transition duration-500 group-hover:scale-105" />
                    )
                  ) : null}
                </div>
                <h3 className="mt-3 text-sm font-semibold leading-5 text-[#43392f] group-hover:text-[#a64934]">{category.name}</h3>
              </Link>
            ))}
          </div>
        )}
      </section>

      <section className="border-y border-[#ece3d8] bg-white" aria-labelledby="new-arrivals-heading">
        <div className="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:px-12 lg:py-20">
          <div className="mb-8 flex flex-wrap items-end justify-between gap-4">
            <div>
              <p className="text-xs font-semibold uppercase text-[#bc7657]">Fresh from the shelves</p>
              <h2 id="new-arrivals-heading" className="mt-2 font-serif text-3xl text-[#332d28] sm:text-4xl">New arrivals</h2>
            </div>
            <Link href="/shop" className="text-sm font-semibold text-[#a64934] hover:underline">Shop all <span aria-hidden="true">→</span></Link>
          </div>
          {products.length ? (
            <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
              {products.slice(0, 3).map((product) => <HomeProductCard key={product.id} product={product} />)}
            </div>
          ) : (
            <div className="rounded-md border border-dashed border-[#d8cabb] bg-[#fbf8f3] px-6 py-10 text-center">
              <p className="font-serif text-xl text-[#43392f]">{catalogUnavailable ? 'The shelves are taking a little pause.' : 'A fresh batch is on its way.'}</p>
              <p className="mt-2 text-sm text-[#776b5e]">{catalogUnavailable ? 'Please check back soon to browse the latest craft finds.' : 'New products will appear here as they are added to the shop.'}</p>
              <Link href="/shop" className="mt-5 inline-flex text-sm font-semibold text-[#a64934] hover:underline">Browse the shop <span aria-hidden="true" className="ml-1">→</span></Link>
            </div>
          )}
        </div>
      </section>

      <section className="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:px-12 lg:py-20" aria-labelledby="best-sellers-heading">
        <div className="flex flex-col justify-between gap-6 rounded-lg bg-[#e8eddf] p-6 sm:p-9 lg:flex-row lg:items-center">
          <div>
            <p className="text-xs font-semibold uppercase text-[#68775c]">The community shelf</p>
            <h2 id="best-sellers-heading" className="mt-2 font-serif text-3xl text-[#332d28] sm:text-4xl">Best sellers</h2>
            <p className="mt-3 max-w-xl text-sm leading-6 text-[#5f6254]">This space will highlight customer favorites once verified sales data is connected.</p>
          </div>
          <Link href="/shop" className="inline-flex min-h-11 items-center justify-center self-start rounded-md border border-[#8e9c7e] px-5 py-2 text-sm font-semibold text-[#46533c] transition hover:bg-white/70 lg:self-auto">Explore all crafts <span aria-hidden="true" className="ml-2">→</span></Link>
        </div>
      </section>

      <section id="videos" className="scroll-mt-32 bg-[#34433a] text-white" aria-labelledby="videos-heading">
        <div className="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:px-12 lg:py-20">
          <div className="mb-8 flex flex-wrap items-end justify-between gap-5">
            <div>
              <p className="text-xs font-semibold uppercase text-[#d7bd88]">From our worktable to yours</p>
              <h2 id="videos-heading" className="mt-2 font-serif text-3xl text-white sm:text-4xl">Watch It. Learn It. Create It!</h2>
              <p className="mt-3 max-w-2xl text-sm leading-6 text-[#e1e2d9]">Explore creative DIY tutorials, handmade crafts, and inspiring home decoration ideas.</p>
            </div>
            <div className="flex flex-wrap gap-3">
              <a href={youtubeChannelUrl} target="_blank" rel="noreferrer" className="inline-flex min-h-11 items-center justify-center rounded-md bg-[#d9694f] px-5 py-2 text-sm font-semibold text-white transition hover:bg-[#bd543d]">Subscribe on YouTube <span aria-hidden="true" className="ml-2">↗</span></a>
              <a href={`${youtubeChannelUrl}/videos`} target="_blank" rel="noreferrer" className="inline-flex min-h-11 items-center justify-center rounded-md border border-white/40 px-5 py-2 text-sm font-semibold text-white transition hover:bg-white/10">View all videos</a>
            </div>
          </div>
          <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {diyVideos.map((video) => <VideoCard key={video.title} video={video} />)}
          </div>
        </div>
      </section>

      <section className="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:px-12 lg:py-20" aria-labelledby="shop-video-heading">
        <div className="mb-8 max-w-2xl">
          <p className="text-xs font-semibold uppercase text-[#bc7657]">Gather what you need</p>
          <h2 id="shop-video-heading" className="mt-2 font-serif text-3xl text-[#332d28] sm:text-4xl">Shop the video</h2>
          <p className="mt-3 text-sm leading-6 text-[#665b50]">Follow a tutorial from the first idea to the finished piece, with materials from the shop close at hand.</p>
        </div>
        <div className="grid gap-5 lg:grid-cols-2">
          {projectMaterials.map((project) => {
            const video = diyVideos[project.videoIndex];
            return (
              <article key={project.title} className="grid overflow-hidden rounded-lg border border-[#e9dfd3] bg-white sm:grid-cols-[0.9fr_1.1fr]">
                <div className="relative min-h-52 bg-[#eee5da]">
                  <Image src={video.thumbnail} alt="" fill sizes="(max-width: 640px) 100vw, 40vw" className="object-cover" />
                </div>
                <div className="flex flex-col p-5 sm:p-6">
                  <h3 className="font-serif text-2xl text-[#332d28]">{project.title}</h3>
                  <p className="mt-2 text-sm leading-6 text-[#665b50]">{project.description}</p>
                  <p className="mt-5 text-xs font-semibold uppercase text-[#77836a]">Materials to explore</p>
                  <div className="mt-2 flex flex-wrap gap-2">
                    {project.related.length ? project.related.map((product) => (
                      <Link key={product.id} href={`/products/${product.slug}`} className="inline-flex items-center gap-2 rounded-full border border-[#e3d8cb] px-3 py-1.5 text-xs text-[#554a40] hover:border-[#a64934] hover:text-[#a64934]">
                        <span>{product.name}</span><span className="font-semibold">{formatPrice(product.discount_price ?? product.price)}</span>
                      </Link>
                    )) : project.materials.map((material) => (
                      <Link key={material} href={`/shop?search=${encodeURIComponent(material)}`} className="rounded-full border border-[#e3d8cb] px-3 py-1.5 text-xs text-[#554a40] hover:border-[#a64934] hover:text-[#a64934]">{material}</Link>
                    ))}
                  </div>
                  <div className="mt-auto flex flex-wrap gap-4 pt-6 text-sm font-semibold">
                    <a href={youtubeVideoUrl(video)} target="_blank" rel="noreferrer" className="text-[#a64934] hover:underline">Watch tutorial <span aria-hidden="true">↗</span></a>
                    <Link href="/craft-materials" className="text-[#596a4e] hover:underline">Shop materials <span aria-hidden="true">→</span></Link>
                  </div>
                </div>
              </article>
            );
          })}
        </div>
      </section>

      <section className="border-y border-[#ece3d8] bg-[#f2e9dc]" id="inspiration" aria-labelledby="inspiration-heading">
        <div className="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:px-12 lg:py-20">
          <div className="mb-8 max-w-xl">
            <p className="text-xs font-semibold uppercase text-[#a64934]">A spark for your next project</p>
            <h2 id="inspiration-heading" className="mt-2 font-serif text-3xl text-[#332d28] sm:text-4xl">DIY inspiration & guides</h2>
          </div>
          <div className="grid gap-px overflow-hidden rounded-lg border border-[#e1d5c7] bg-[#e1d5c7] sm:grid-cols-2 lg:grid-cols-4">
            {diyIdeas.map((idea, index) => (
              <a key={idea.title} href={`https://www.youtube.com/results?search_query=${encodeURIComponent(idea.search)}`} target="_blank" rel="noreferrer" className="group bg-[#fbf8f3] p-5 transition hover:bg-white sm:p-6">
                <span className="font-serif text-3xl text-[#bc7657]">0{index + 1}</span>
                <p className="mt-5 text-xs font-semibold uppercase text-[#77836a]">{idea.eyebrow}</p>
                <h3 className="mt-2 font-serif text-xl text-[#332d28] group-hover:text-[#a64934]">{idea.title}</h3>
                <span className="mt-5 inline-flex text-sm font-semibold text-[#a64934]">Find an idea <span aria-hidden="true" className="ml-2">↗</span></span>
              </a>
            ))}
          </div>
        </div>
      </section>

      <section className="mx-auto grid max-w-7xl gap-4 px-5 py-12 sm:grid-cols-2 sm:px-8 lg:grid-cols-4 lg:px-12" aria-label="Shopping information">
        <Link href="/shipping-policy" className="border-l-2 border-[#bc7657] py-2 pl-4 hover:bg-white/60">
          <h2 className="font-semibold text-[#43392f]">Shipping information</h2>
          <p className="mt-1 text-sm text-[#776b5e]">Delivery details and timelines</p>
        </Link>
        <Link href="/refund-policy" className="border-l-2 border-[#77836a] py-2 pl-4 hover:bg-white/60">
          <h2 className="font-semibold text-[#43392f]">Returns & refunds</h2>
          <p className="mt-1 text-sm text-[#776b5e]">Read our returns policy</p>
        </Link>
        <Link href="/contact" className="border-l-2 border-[#c3a35f] py-2 pl-4 hover:bg-white/60">
          <h2 className="font-semibold text-[#43392f]">Need a hand?</h2>
          <p className="mt-1 text-sm text-[#776b5e]">Contact the Craftora team</p>
        </Link>
        <Link href="/checkout" className="border-l-2 border-[#68775c] py-2 pl-4 hover:bg-white/60">
          <h2 className="font-semibold text-[#43392f]">Payment details</h2>
          <p className="mt-1 text-sm text-[#776b5e]">Available payment options at checkout</p>
        </Link>
      </section>
    </main>
  );
}
