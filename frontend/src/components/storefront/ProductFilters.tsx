'use client';

import { useRouter, useSearchParams } from 'next/navigation';
import { FormEvent, useMemo, useState } from 'react';

export function ProductFilters() {
  const router = useRouter();
  const searchParams = useSearchParams();

  const initialSearch = searchParams.get('search') ?? '';
  const initialSortBy = searchParams.get('sort_by') ?? 'created_at';
  const initialSortDir = searchParams.get('sort_dir') ?? 'desc';
  const initialMinPrice = searchParams.get('min_price') ?? '';
  const initialMaxPrice = searchParams.get('max_price') ?? '';

  const [search, setSearch] = useState(initialSearch);
  const [sortBy, setSortBy] = useState(initialSortBy);
  const [sortDir, setSortDir] = useState(initialSortDir);
  const [minPrice, setMinPrice] = useState(initialMinPrice);
  const [maxPrice, setMaxPrice] = useState(initialMaxPrice);

  const queryString = useMemo(() => {
    const params = new URLSearchParams();

    if (search) params.set('search', search);
    if (sortBy) params.set('sort_by', sortBy);
    if (sortDir) params.set('sort_dir', sortDir);
    if (minPrice) params.set('min_price', minPrice);
    if (maxPrice) params.set('max_price', maxPrice);

    return params.toString();
  }, [search, sortBy, sortDir, minPrice, maxPrice]);

  const handleSubmit = (event: FormEvent) => {
    event.preventDefault();
    router.push(`/products${queryString ? `?${queryString}` : ''}`);
  };

  const handleReset = () => {
    setSearch('');
    setSortBy('created_at');
    setSortDir('desc');
    setMinPrice('');
    setMaxPrice('');
    router.push('/products');
  };

  return (
    <form onSubmit={handleSubmit} className="rounded-3xl border border-slate-800 bg-slate-900/70 p-4 shadow-lg shadow-slate-950/20">
      <div className="grid gap-4 md:grid-cols-2 xl:grid-cols-[1.5fr_0.8fr_0.8fr_0.8fr_0.8fr]">
        <label className="block text-sm text-slate-300">
          <span className="mb-2 block text-xs font-medium uppercase tracking-[0.18em] text-slate-400">Search</span>
          <input
            value={search}
            onChange={(event) => setSearch(event.target.value)}
            placeholder="Search products"
            className="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 text-white outline-none transition focus:border-emerald-400"
          />
        </label>

        <label className="block text-sm text-slate-300">
          <span className="mb-2 block text-xs font-medium uppercase tracking-[0.18em] text-slate-400">Sort by</span>
          <select
            value={sortBy}
            onChange={(event) => setSortBy(event.target.value)}
            className="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 text-white outline-none transition focus:border-emerald-400"
          >
            <option value="created_at">Newest</option>
            <option value="price">Price</option>
            <option value="name">Name</option>
          </select>
        </label>

        <label className="block text-sm text-slate-300">
          <span className="mb-2 block text-xs font-medium uppercase tracking-[0.18em] text-slate-400">Direction</span>
          <select
            value={sortDir}
            onChange={(event) => setSortDir(event.target.value)}
            className="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 text-white outline-none transition focus:border-emerald-400"
          >
            <option value="desc">Descending</option>
            <option value="asc">Ascending</option>
          </select>
        </label>

        <label className="block text-sm text-slate-300">
          <span className="mb-2 block text-xs font-medium uppercase tracking-[0.18em] text-slate-400">Min price</span>
          <input
            value={minPrice}
            onChange={(event) => setMinPrice(event.target.value)}
            type="number"
            min="0"
            placeholder="0"
            className="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 text-white outline-none transition focus:border-emerald-400"
          />
        </label>

        <div className="flex items-end gap-2">
          <button
            type="submit"
            className="w-full rounded-xl bg-emerald-500 px-3 py-2.5 text-sm font-semibold text-slate-950 transition hover:bg-emerald-400"
          >
            Apply
          </button>
          <button
            type="button"
            onClick={handleReset}
            className="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 text-sm font-semibold text-slate-200 transition hover:border-slate-500"
          >
            Reset
          </button>
        </div>
      </div>

      <label className="mt-4 block text-sm text-slate-300">
        <span className="mb-2 block text-xs font-medium uppercase tracking-[0.18em] text-slate-400">Max price</span>
        <input
          value={maxPrice}
          onChange={(event) => setMaxPrice(event.target.value)}
          type="number"
          min="0"
          placeholder="250"
          className="w-full rounded-xl border border-slate-700 bg-slate-950 px-3 py-2.5 text-white outline-none transition focus:border-emerald-400"
        />
      </label>
    </form>
  );
}
