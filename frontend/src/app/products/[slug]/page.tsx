import type { Metadata } from 'next';
import Link from 'next/link';
import { notFound } from 'next/navigation';
import { getProductBySlug } from '@/lib/catalog';
import { formatPrice } from '@/lib/catalog';
import { AddToCartButton } from '@/components/storefront/AddToCartButton';

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }): Promise<Metadata> {
  const { slug } = await params;
  const response = await getProductBySlug(slug).catch(() => null);
  const product = response?.data;

  if (!product) {
    return { title: 'Product not found | Craftora' };
  }

  return {
    title: `${product.name} | Craftora`,
    description: product.seo_description ?? product.description ?? 'Premium product details.',
    alternates: {
      canonical: `https://craftora.example/products/${product.slug}`,
    },
    openGraph: {
      title: product.name,
      description: product.seo_description ?? product.description ?? 'Premium product details.',
      images: product.images?.[0]?.image_url ? [product.images[0].image_url] : undefined,
      type: 'website',
    },
    twitter: {
      card: 'summary_large_image',
      title: product.name,
      description: product.seo_description ?? product.description ?? '',
      images: product.images?.[0]?.image_url ? [product.images[0].image_url] : undefined,
    },
  };
}

export default async function ProductDetailPage({ params }: { params: Promise<{ slug: string }> }) {
  const { slug } = await params;
  const response = await getProductBySlug(slug).catch(() => null);
  const product = response?.data;

  if (!product) {
    notFound();
  }

  const primaryImage = product.images?.find((image) => image.is_primary)?.image_url ?? product.images?.[0]?.image_url ?? 'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?auto=format&fit=crop&w=1200&q=80';
  const gallery = (product.images && product.images.length > 0 ? product.images : [{ image_url: primaryImage, alt_text: product.name }]).slice(0, 4);
  const comparePrice = product.compare_price ?? product.discount_price;

  return (
    <main className="min-h-screen bg-slate-950 text-slate-100">
      <div className="mx-auto max-w-6xl px-4 py-12 sm:px-6 lg:px-8">
        <nav className="mb-8 flex items-center gap-2 text-sm text-slate-400">
          <Link href="/" className="hover:text-emerald-300">Home</Link>
          <span>/</span>
          <Link href="/products" className="hover:text-emerald-300">Products</Link>
          <span>/</span>
          <span className="text-slate-200">{product.name}</span>
        </nav>

        <div className="grid gap-10 lg:grid-cols-[1.1fr_0.9fr]">
          <div className="space-y-4">
            <div className="overflow-hidden rounded-3xl border border-slate-800 bg-slate-900/70">
              <img src={primaryImage} alt={product.name} className="aspect-[4/3] w-full object-cover" />
            </div>
            <div className="grid grid-cols-2 gap-4 md:grid-cols-4">
              {gallery.map((image, index) => (
                <div key={`${image.image_url ?? index}`} className="overflow-hidden rounded-2xl border border-slate-800 bg-slate-900/70">
                  <img src={image.image_url ?? primaryImage} alt={image.alt_text ?? product.name} className="aspect-square w-full object-cover" />
                </div>
              ))}
            </div>
          </div>

          <div className="space-y-6">
            <div>
              <p className="text-xs font-medium uppercase tracking-[0.24em] text-emerald-300">{product.category?.name ?? 'Wellness'}</p>
              <h1 className="mt-3 text-4xl font-bold tracking-tight text-white">{product.name}</h1>
            </div>

            <div className="flex items-end gap-3">
              <p className="text-4xl font-bold text-white">{formatPrice(product.price)}</p>
              {comparePrice ? <p className="text-xl text-slate-400 line-through">{formatPrice(comparePrice)}</p> : null}
            </div>

            <div className="rounded-2xl border border-slate-800 bg-slate-900/70 p-4 text-sm text-slate-300">
              <p>{product.description ?? product.short_description ?? 'High-quality product for everyday wellness support.'}</p>
            </div>

            <div className="flex flex-wrap gap-3">
              <AddToCartButton productId={product.id} label="Add to cart" />
              <Link href="/products" className="rounded-full border border-slate-700 bg-slate-900 px-6 py-3 text-sm font-semibold text-slate-100 transition hover:border-slate-500">
                Continue shopping
              </Link>
            </div>

            <div className="grid gap-3 text-sm text-slate-300 sm:grid-cols-2">
              <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
                <p className="text-slate-400">Availability</p>
                <p className="mt-2 font-semibold text-white">{(product.stock_quantity ?? 0) > 0 ? 'In stock' : 'Currently unavailable'}</p>
              </div>
              <div className="rounded-2xl border border-slate-800 bg-slate-900/60 p-4">
                <p className="text-slate-400">Product type</p>
                <p className="mt-2 font-semibold text-white">{product.type ?? 'Physical'}</p>
              </div>
            </div>
          </div>
        </div>

        <div className="mt-12 grid gap-8 lg:grid-cols-2">
          <section className="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 className="text-2xl font-semibold text-white">Features</h2>
            <ul className="mt-4 space-y-3 text-slate-300">
              {(product.features && product.features.length > 0 ? product.features : ['Premium quality materials.', 'Designed for everyday wellness routines.', 'Responsive customer support.']).map((feature) => (
                <li key={feature} className="flex items-start gap-3">
                  <span className="mt-1 inline-block h-2.5 w-2.5 rounded-full bg-emerald-400" />
                  <span>{feature}</span>
                </li>
              ))}
            </ul>
          </section>

          <section className="rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 className="text-2xl font-semibold text-white">Specifications</h2>
            <div className="mt-4 space-y-3 text-sm text-slate-300">
              {product.specifications && Object.keys(product.specifications as Record<string, unknown>).length > 0 ? (
                Object.entries(product.specifications as Record<string, unknown>).map(([key, value]) => (
                  <div key={key} className="flex justify-between gap-4 border-b border-slate-800 pb-3 last:border-none last:pb-0">
                    <span className="text-slate-400">{key}</span>
                    <span className="text-right font-medium text-white">{String(value)}</span>
                  </div>
                ))
              ) : (
                <div className="space-y-2 text-slate-300">
                  <p>SKU: {product.sku ?? 'N/A'}</p>
                  <p>Brand: {product.brand ?? 'Craftora'}</p>
                  <p>Status: {product.status ?? 'Published'}</p>
                </div>
              )}
            </div>
          </section>
        </div>

        {product.variants && product.variants.length > 0 ? (
          <section className="mt-12 rounded-3xl border border-slate-800 bg-slate-900/70 p-6">
            <h2 className="text-2xl font-semibold text-white">Available variants</h2>
            <div className="mt-5 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
              {product.variants.map((variant) => (
                <div key={variant.id ?? variant.name} className="rounded-2xl border border-slate-800 bg-slate-950/60 p-4">
                  <p className="text-lg font-semibold text-white">{variant.name}</p>
                  <p className="mt-2 text-sm text-slate-400">{variant.sku ?? 'No SKU'}</p>
                  <p className="mt-4 text-xl font-bold text-emerald-300">{formatPrice(variant.price ?? product.price)}</p>
                </div>
              ))}
            </div>
          </section>
        ) : null}
      </div>
    </main>
  );
}
