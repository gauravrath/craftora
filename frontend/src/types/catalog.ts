export type CatalogCategory = {
  id?: number;
  name?: string;
  slug?: string;
};

export type CatalogImage = {
  id?: string;
  image_path?: string | null;
  image_url?: string | null;
  alt_text?: string | null;
  is_primary?: boolean;
  sort_order?: number;
};

export type CatalogVariant = {
  id?: string;
  name?: string;
  sku?: string | null;
  option_values?: Record<string, string | number | boolean> | null;
  price?: number | string | null;
  compare_price?: number | string | null;
  stock?: number | null;
  is_active?: boolean;
};

export type Product = {
  id: string;
  category?: CatalogCategory | null;
  name: string;
  slug: string;
  brand?: string | null;
  sku?: string | null;
  description?: string | null;
  short_description?: string | null;
  price: number | string;
  discount_price?: number | string | null;
  compare_price?: number | string | null;
  stock_quantity?: number;
  status?: string;
  type?: string;
  is_featured?: boolean;
  is_active?: boolean;
  images?: CatalogImage[];
  variants?: CatalogVariant[];
  specifications?: Record<string, unknown> | null;
  features?: string[] | null;
  seo_title?: string | null;
  seo_description?: string | null;
  seo_keywords?: string | null;
};

export type CatalogMeta = {
  current_page?: number;
  last_page?: number;
  per_page?: number;
  total?: number;
};

export type CatalogApiResponse<T = Product[]> = {
  success: boolean;
  message?: string;
  data: T;
  meta?: CatalogMeta;
  errors?: Record<string, string[]>;
};

export type StoreCategory = {
  id: number;
  name: string;
  slug: string;
  image_url: string | null;
};
