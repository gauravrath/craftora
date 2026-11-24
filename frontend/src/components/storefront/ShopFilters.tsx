'use client';

import { FormEvent, useState } from 'react';
import { usePathname, useRouter, useSearchParams } from 'next/navigation';
import type { ReactNode } from 'react';

type ShopCategory = { id: number; name: string };

function FilterFields({
  prefix,
  categories,
  category,
  minPrice,
  maxPrice,
  onCategoryChange,
  onMinPriceChange,
  onMaxPriceChange,
}: {
  prefix: string;
  categories: ShopCategory[];
  category: string;
  minPrice: string;
  maxPrice: string;
  onCategoryChange: (value: string) => void;
  onMinPriceChange: (value: string) => void;
  onMaxPriceChange: (value: string) => void;
}) {
  const fieldClass = 'mt-2 w-full rounded-md border border-[#e2d8cc] bg-white px-3 py-2.5 text-sm text-[#43392f] outline-none focus:border-[#a64934] focus:ring-2 focus:ring-[#a64934]/15';

  return (
    <>
      <label htmlFor={`${prefix}-category`} className="block text-sm font-medium text-[#51473e]">
        Category
        <select id={`${prefix}-category`} value={category} onChange={(event) => onCategoryChange(event.target.value)} className={fieldClass}>
          <option value="">All categories</option>
          {categories.map((item) => <option key={item.id} value={item.id}>{item.name}</option>)}
        </select>
      </label>
      <fieldset>
        <legend className="text-sm font-medium text-[#51473e]">Price range</legend>
        <div className="mt-2 grid grid-cols-2 gap-3">
          <label htmlFor={`${prefix}-min-price`} className="text-xs text-[#776b5e]">
            Min
            <input id={`${prefix}-min-price`} type="number" min="0" inputMode="decimal" value={minPrice} onChange={(event) => onMinPriceChange(event.target.value)} placeholder="0" className={fieldClass} />
          </label>
          <label htmlFor={`${prefix}-max-price`} className="text-xs text-[#776b5e]">
            Max
            <input id={`${prefix}-max-price`} type="number" min="0" inputMode="decimal" value={maxPrice} onChange={(event) => onMaxPriceChange(event.target.value)} placeholder="Any" className={fieldClass} />
          </label>
        </div>
      </fieldset>
    </>
  );
}

export function ShopFilters({
  categories,
  initialSearch,
  initialCategory,
  initialMinPrice,
  initialMaxPrice,
  initialSortBy,
  initialSortDir,
  children,
}: {
  categories: ShopCategory[];
  initialSearch: string;
  initialCategory: string;
  initialMinPrice: string;
  initialMaxPrice: string;
  initialSortBy: string;
  initialSortDir: string;
  children: ReactNode;
}) {
  const router = useRouter();
  const pathname = usePathname();
  const searchParams = useSearchParams();
  const [search, setSearch] = useState(initialSearch);
  const [category, setCategory] = useState(initialCategory);
  const [minPrice, setMinPrice] = useState(initialMinPrice);
  const [maxPrice, setMaxPrice] = useState(initialMaxPrice);
  const [sort, setSort] = useState(initialSortBy === 'price' ? `price-${initialSortDir}` : 'newest');
  const [filtersOpen, setFiltersOpen] = useState(false);

  function navigate(updates: Record<string, string>) {
    const params = new URLSearchParams(searchParams.toString());
    Object.entries(updates).forEach(([key, value]) => {
      if (value) params.set(key, value);
      else params.delete(key);
    });
    params.delete('page');

    const query = params.toString();
    router.push(query ? `${pathname}?${query}` : pathname, { scroll: false });
  }

  function submitSearch(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    navigate({ search });
  }

  function submitFilters(event: FormEvent<HTMLFormElement>) {
    event.preventDefault();
    navigate({ category, min_price: minPrice, max_price: maxPrice });
    setFiltersOpen(false);
  }

  function changeSort(value: string) {
    setSort(value);
    const [sortBy, sortDir] = value === 'newest' ? ['created_at', 'desc'] : ['price', value === 'price-asc' ? 'asc' : 'desc'];
    navigate({ sort_by: sortBy, sort_dir: sortDir });
  }

  function resetFilters() {
    setCategory('');
    setMinPrice('');
    setMaxPrice('');
    navigate({ category: '', min_price: '', max_price: '' });
  }

  const filterFields = (prefix: string) => (
    <FilterFields
      prefix={prefix}
      categories={categories}
      category={category}
      minPrice={minPrice}
      maxPrice={maxPrice}
      onCategoryChange={setCategory}
      onMinPriceChange={setMinPrice}
      onMaxPriceChange={setMaxPrice}
    />
  );

  return (
    <>
      <section aria-label="Shop products">
        <div className="flex flex-col gap-3 sm:flex-row" aria-label="Product search and sorting">
          <form onSubmit={submitSearch} role="search" className="flex min-w-0 flex-1 items-center rounded-md border border-[#e2d8cc] bg-white focus-within:border-[#a64934]">
            <label htmlFor="shop-search" className="sr-only">Search products by name</label>
            <input id="shop-search" type="search" value={search} onChange={(event) => setSearch(event.target.value)} placeholder="Search crafts and materials" className="min-w-0 flex-1 bg-transparent px-4 py-3 text-sm text-[#332d28] outline-none placeholder:text-[#978b7f]" />
            <button type="submit" aria-label="Search products" className="inline-flex size-11 items-center justify-center text-[#776b5e] hover:text-[#a64934]">
              <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" className="size-5" stroke="currentColor" strokeWidth="1.8"><circle cx="10.8" cy="10.8" r="6.8" /><path strokeLinecap="round" d="m16 16 4.5 4.5" /></svg>
            </button>
          </form>
          <label htmlFor="shop-sort" className="flex shrink-0 items-center gap-2 rounded-md border border-[#e2d8cc] bg-white px-3 text-sm text-[#665b50]">
            <span>Sort</span>
            <select id="shop-sort" value={sort} onChange={(event) => changeSort(event.target.value)} className="max-w-48 bg-transparent py-3 font-medium text-[#43392f] outline-none">
              <option value="newest">Newest</option>
              <option value="price-asc">Price: low to high</option>
              <option value="price-desc">Price: high to low</option>
            </select>
          </label>
          <button type="button" aria-expanded={filtersOpen} aria-controls="mobile-shop-filters" onClick={() => setFiltersOpen(true)} className="inline-flex min-h-11 items-center justify-center gap-2 rounded-md border border-[#d9cbbd] px-4 text-sm font-semibold text-[#51473e] hover:bg-[#f2e9dc] lg:hidden">
            <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" className="size-4" stroke="currentColor" strokeWidth="1.8"><path strokeLinecap="round" d="M4 6h16M7 12h10m-7 6h4" /></svg>
            Filters
          </button>
        </div>

        <div className="mt-6 grid items-start gap-6 lg:grid-cols-[240px_minmax(0,1fr)]">
          <aside className="hidden lg:block">
            <form onSubmit={submitFilters} className="space-y-6 rounded-md border border-[#e9dfd3] bg-white p-5">
              <div className="flex items-center justify-between">
                <h2 className="font-serif text-xl text-[#332d28]">Filters</h2>
                <button type="button" onClick={resetFilters} className="text-xs font-semibold text-[#a64934] hover:underline">Clear all</button>
              </div>
              {filterFields('desktop')}
              <button type="submit" className="min-h-11 w-full rounded-md bg-[#a64934] px-4 text-sm font-semibold text-white hover:bg-[#873b2a]">Apply filters</button>
            </form>
          </aside>
          <div>{children}</div>
        </div>

        {filtersOpen ? (
        <div className="fixed inset-0 z-[70] bg-[#332d28]/45 lg:hidden">
          <section id="mobile-shop-filters" role="dialog" aria-modal="true" aria-labelledby="mobile-filter-heading" className="absolute inset-y-0 right-0 flex w-full max-w-sm flex-col bg-[#fbf8f3] p-5 shadow-2xl">
            <div className="mb-6 flex items-center justify-between">
              <h2 id="mobile-filter-heading" className="font-serif text-2xl text-[#332d28]">Filters</h2>
              <button type="button" aria-label="Close filters" onClick={() => setFiltersOpen(false)} className="inline-flex size-10 items-center justify-center rounded-md text-[#51473e] hover:bg-[#f2e9dc]">
                <svg aria-hidden="true" viewBox="0 0 24 24" fill="none" className="size-5" stroke="currentColor" strokeWidth="1.8"><path strokeLinecap="round" d="m6 6 12 12M18 6 6 18" /></svg>
              </button>
            </div>
            <form onSubmit={submitFilters} className="flex flex-1 flex-col gap-6">
              {filterFields('mobile')}
              <div className="mt-auto flex gap-3">
                <button type="button" onClick={resetFilters} className="min-h-11 flex-1 rounded-md border border-[#d9cbbd] px-4 text-sm font-semibold text-[#51473e]">Clear all</button>
                <button type="submit" className="min-h-11 flex-1 rounded-md bg-[#a64934] px-4 text-sm font-semibold text-white">Apply filters</button>
              </div>
            </form>
          </section>
        </div>
      ) : null}
      </section>
    </>
  );
}