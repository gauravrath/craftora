import Link from 'next/link';
import Image from 'next/image';
import type { Product } from '@/types/catalog';
import { formatPrice } from '@/lib/catalog';
import { AddToCartButton } from '@/components/storefront/AddToCartButton';
import { WishlistButton } from '@/components/storefront/WishlistButton';

export function HomeProductCard({ product }: { product: Product }) {
  const salePrice = product.discount_price;
  const comparePrice = product.compare_price ?? (salePrice ? product.price : null);
  const imageUrl =
    product.images?.find((image) => image.is_primary)?.image_url ??
    product.images?.[0]?.image_url ??
    'https://images.unsplash.com/photo-1452860606245-08befc0ff44b?auto=format&fit=crop&w=900&q=85';

  return (
    <article className="group overflow-hidden rounded-lg border border-[#e9dfd3] bg-white transition hover:-translate-y-1 hover:shadow-xl hover:shadow-[#714d3214]">
      <div className="relative aspect-[4/3] overflow-hidden bg-[#f4eee7]">
        <Link href={`/products/${product.slug}`} aria-label={`View ${product.name}`}>
          <Image src={imageUrl} alt={product.name} fill unoptimized sizes="(max-width: 768px) 90vw, (max-width: 1024px) 45vw, 30vw" className="object-cover transition duration-500 group-hover:scale-[1.04]" />
        </Link>
        {salePrice ? <span className="absolute left-3 top-3 rounded bg-[#a64934] px-2.5 py-1 text-xs font-semibold text-white">Sale</span> : null}
        <WishlistButton />
      </div>
      <div className="p-5">
        <p className="text-xs font-medium uppercase text-[#77836a]">{product.category?.name ?? 'Craftora finds'}</p>
        <Link href={`/products/${product.slug}`} className="mt-2 block text-lg font-semibold text-[#332d28] hover:text-[#a64934]">
          {product.name}
        </Link>
        <div className="mt-4 flex items-center justify-between gap-3">
          <div className="flex items-baseline gap-2">
            <p className="font-semibold text-[#332d28]">{formatPrice(salePrice ?? product.price)}</p>
            {comparePrice ? <p className="text-sm text-[#978b7f] line-through">{formatPrice(comparePrice)}</p> : null}
          </div>
          <AddToCartButton productId={product.id} label="Add to bag" />
        </div>
      </div>
    </article>
  );
}