import Link from 'next/link';
import { Product } from '@/types/catalog';
import { formatPrice } from '@/lib/catalog';
import { AddToCartButton } from '@/components/storefront/AddToCartButton';

export function ProductCard({ product }: { product: Product }) {
  const imageUrl =
    product.images?.find((image) => image.is_primary)?.image_url ??
    product.images?.[0]?.image_url ??
    'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80';

  const comparePrice = product.compare_price ?? product.discount_price;

  return (
    <article className="group overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/70 shadow-lg shadow-slate-950/20 transition hover:-translate-y-1 hover:border-emerald-500/40 hover:shadow-emerald-950/20">
      <Link href={`/products/${product.slug}`} className="block">
        <div className="relative aspect-[4/3] overflow-hidden bg-slate-950">
          <img
            src={imageUrl}
            alt={product.name}
            className="h-full w-full object-cover transition duration-500 group-hover:scale-105"
          />
          {product.is_featured ? (
            <span className="absolute left-4 top-4 rounded-full bg-emerald-500/90 px-2.5 py-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-slate-950">
              Featured
            </span>
          ) : null}
        </div>

        <div className="space-y-4 p-5">
          <div className="flex items-center justify-between gap-3">
            <p className="text-xs font-medium uppercase tracking-[0.2em] text-emerald-300">
              {product.category?.name ?? 'Shop'}
            </p>
            <span className="rounded-full border border-slate-700 px-2 py-1 text-[10px] uppercase tracking-[0.18em] text-slate-300">
              {product.stock_quantity && product.stock_quantity > 0 ? 'In stock' : 'Out of stock'}
            </span>
          </div>

          <div>
            <h3 className="text-xl font-semibold text-white">{product.name}</h3>
            <p className="mt-2 line-clamp-3 text-sm leading-6 text-slate-300">
              {product.short_description ?? product.description ?? 'Premium everyday protection essentials.'}
            </p>
          </div>

          <div className="flex items-end gap-3">
            <p className="text-2xl font-bold text-white">{formatPrice(product.price)}</p>
            {comparePrice ? (
              <p className="text-sm text-slate-400 line-through">{formatPrice(comparePrice)}</p>
            ) : null}
          </div>

          <div className="flex items-center justify-between gap-3 pt-2">
            <span className="text-sm text-slate-400">{product.variants?.length ?? 0} variants</span>
            <AddToCartButton productId={product.id} label="Add to cart" />
          </div>
        </div>
      </Link>
    </article>
  );
}
