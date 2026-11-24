import type { CatalogApiResponse, Product, StoreCategory } from '@/types/catalog';

const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL ?? 'http://localhost:8001/api/v1';

async function request<T>(path: string, init: RequestInit = {}): Promise<T> {
  const response = await fetch(`${API_BASE_URL}${path}`, {
    ...init,
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      ...(init.headers ?? {}),
    },
    cache: 'no-store',
  });

  const payload = (await response.json()) as T & { message?: string };

  if (!response.ok) {
    throw new Error((payload as { message?: string }).message ?? 'Request failed');
  }

  return payload;
}

export type ProductQuery = {
  search?: string;
  category?: string | number;
  sort_by?: string;
  sort_dir?: string;
  per_page?: number | string;
  page?: number | string;
  min_price?: number | string;
  max_price?: number | string;
};

export async function getProducts(params: ProductQuery = {}) {
  const query = new URLSearchParams();

  Object.entries(params).forEach(([key, value]) => {
    if (value !== undefined && value !== null && value !== '') {
      query.set(key, String(value));
    }
  });

  const url = query.size > 0 ? `/products?${query.toString()}` : '/products';
  return request<CatalogApiResponse<Product[]>>(url);
}

export async function getCategories() {
  return request<CatalogApiResponse<StoreCategory[]>>('/categories');
}

export async function getProductBySlug(slug: string) {
  return request<CatalogApiResponse<Product>>(`/products/${encodeURIComponent(slug)}`);
}

export async function getHomeProducts() {
  return getProducts({ per_page: 3, sort_by: 'created_at', sort_dir: 'desc' });
}

export function formatPrice(value: number | string | null | undefined): string {
  const amount = Number(value ?? 0);
  return new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2,
  }).format(Number.isFinite(amount) ? amount : 0);
}
