export type CartCoupon = {
  code: string;
  type: string;
  value: number;
};

export type CartProductSummary = {
  id: string;
  name: string;
  slug?: string;
  price: number;
  stock_quantity?: number;
};

export type CartItem = {
  id: string;
  cart_id: string;
  product_id: string;
  product_variant_id?: string | null;
  product?: CartProductSummary | null;
  variant?: { id: string; name: string; stock?: number } | null;
  quantity: number;
  unit_price: number;
  total_price: number;
};

export type CartSummary = {
  subtotal: number;
  discount_amount: number;
  tax_total: number;
  shipping_amount: number;
  total: number;
  currency: string;
  item_count: number;
};

export type CartPayload = {
  cart: {
    id: string;
    session_id?: string | null;
    user_id?: number | null;
    status: string;
  };
  items: CartItem[];
  coupon: CartCoupon | null;
  summary: CartSummary;
};

const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL ?? 'http://localhost:8001/api/v1';

export function ensureCartSessionId(): string {
  if (typeof window === 'undefined') {
    return 'guest-server';
  }

  const existing = window.localStorage.getItem('craftora_cart_session');
  if (existing) {
    return existing;
  }

  const nextSessionId = `guest-${crypto.randomUUID()}`;
  window.localStorage.setItem('craftora_cart_session', nextSessionId);
  return nextSessionId;
}

async function request<T>(path: string, init: RequestInit = {}): Promise<T> {
  const sessionId = ensureCartSessionId();

  const response = await fetch(`${API_BASE_URL}${path}`, {
    ...init,
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-Cart-Session': sessionId,
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

export async function getCart(): Promise<CartPayload> {
  return request<CartPayload>('/cart');
}

export async function addCartItem(productId: string, quantity = 1, productVariantId?: string | null) {
  return request<CartPayload>('/cart/items', {
    method: 'POST',
    body: JSON.stringify({ product_id: productId, quantity, product_variant_id: productVariantId ?? null }),
  });
}

export async function updateCartItem(productId: string, quantity: number, productVariantId?: string | null) {
  return request<CartPayload>(`/cart/items/${encodeURIComponent(productId)}`, {
    method: 'PUT',
    body: JSON.stringify({ quantity, product_variant_id: productVariantId ?? null }),
  });
}

export async function removeCartItem(productId: string, productVariantId?: string | null) {
  return request<CartPayload>(`/cart/items/${encodeURIComponent(productId)}`, {
    method: 'DELETE',
    body: JSON.stringify({ product_variant_id: productVariantId ?? null }),
  });
}

export async function applyCartCoupon(code: string) {
  return request<CartPayload>('/cart/coupon', {
    method: 'POST',
    body: JSON.stringify({ code }),
  });
}

export async function removeCartCoupon() {
  return request<CartPayload>('/cart/coupon', { method: 'DELETE' });
}
