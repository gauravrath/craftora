import Image from 'next/image';
import Link from 'next/link';
import { formatPrice } from '@/lib/catalog';
import type { DiyIdea } from '@/lib/diy-ideas';
import { productMatchesGroup } from '@/lib/materials';
import type { Product } from '@/types/catalog';

export function IdeaCard({ idea, products }: { idea: DiyIdea; products: Product[] }) {
  const related = products.filter((product) => productMatchesGroup(product, { keywords: idea.productKeywords })).slice(0, 3);
  const watchUrl = idea.youtubeId
    ? `https://www.youtube.com/watch?v=${idea.youtubeId}`
    : `https://www.youtube.com/results?search_query=${encodeURIComponent(idea.youtubeSearch)}`;

  return (
    <article className="flex flex-col overflow-hidden rounded-lg border border-[#e9dfd3] bg-white">
      <div className="relative aspect-[4/3] bg-[#eee5da]">
        <Image src={idea.image} alt={idea.imageAlt} fill sizes="(max-width: 640px) 100vw, (max-width: 1024px) 45vw, 30vw" className="object-cover" />
      </div>
      <div className="flex flex-1 flex-col p-5">
        {idea.occasion || idea.difficulty || idea.timeEstimate ? (
          <ul className="mb-3 flex flex-wrap gap-2 text-xs text-[#665b50]">
            {[idea.occasion, idea.difficulty, idea.timeEstimate].filter(Boolean).map((label) => (
              <li key={label} className="rounded-full bg-[#f2e9dc] px-2.5 py-1">{label}</li>
            ))}
          </ul>
        ) : null}
        <h3 className="font-serif text-xl text-[#332d28]">{idea.title}</h3>
        <p className="mt-2 text-sm leading-6 text-[#665b50]">{idea.description}</p>

        <p className="mt-4 text-xs font-semibold uppercase text-[#77836a]">Materials from the shop</p>
        {related.length > 0 ? (
          <ul className="mt-2 flex flex-wrap gap-2">
            {related.map((product) => (
              <li key={product.id}>
                <Link href={`/products/${product.slug}`} className="inline-flex items-center gap-2 rounded-full border border-[#e3d8cb] px-3 py-1.5 text-xs text-[#554a40] hover:border-[#a64934] hover:text-[#a64934]">
                  <span>{product.name}</span>
                  <span className="font-semibold">{formatPrice(product.discount_price ?? product.price)}</span>
                </Link>
              </li>
            ))}
          </ul>
        ) : (
          <p className="mt-2 text-sm text-[#776b5e]">No matching products listed yet.</p>
        )}

        <div className="mt-auto flex flex-wrap gap-3 pt-5">
          <a href={watchUrl} target="_blank" rel="noreferrer" className="inline-flex min-h-10 items-center rounded-md bg-[#a64934] px-4 text-sm font-semibold text-white hover:bg-[#873b2a]">
            Watch Tutorial <span aria-hidden="true" className="ml-2">↗</span>
          </a>
          <Link href={`/shop?search=${encodeURIComponent(idea.materialSearch)}`} className="inline-flex min-h-10 items-center rounded-md border border-[#b8a894] px-4 text-sm font-semibold text-[#43392f] hover:bg-[#f2e9dc]">
            Shop Materials
          </Link>
        </div>
      </div>
    </article>
  );
}
