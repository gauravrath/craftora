export type CheckoutAddress = {
  id?: number;
  label?: string;
  full_name: string;
  phone: string;
  address_line_1: string;
  address_line_2?: string | null;
  city: string;
  state: string;
  postal_code: string;
  country: string;
  is_default?: boolean;
};

export type CheckoutSummary = {
  cart?: {
    id: string;
    status: string;
    item_count: number;
    subtotal: number;
    total: number;
  };
  items?: Array<{
    product_id: string;
    product_name: string;
    variant?: string | null;
    quantity: number;
    unit_price: number;
    line_total: number;
  }>;
  subtotal?: number;
  discount?: number;
  tax?: number;
  shipping?: number;
  grand_total?: number;
  shipping_address?: CheckoutAddress | null;
  billing_address?: CheckoutAddress | null;
  shipping_method?: { code: string; label: string; cost: number; delivery_days: number };
  payment_method?: { code: string; label: string };
  coupon?: { code: string; type: string; value: number } | null;
};

const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL ?? 'http://localhost:8001/api/v1';

async function request<T>(path: string, init: RequestInit = {}, requireAuth = false): Promise<T> {
  const token = typeof window !== 'undefined' ? window.sessionStorage.getItem('craftora_auth_token') : null;
  const sessionId = typeof window !== 'undefined' ? window.localStorage.getItem('craftora_cart_session') : null;

  const response = await fetch(`${API_BASE_URL}${path}`, {
    ...init,
    headers: {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      ...(sessionId ? { 'X-Cart-Session': sessionId } : {}),
      ...(requireAuth && token ? { Authorization: `Bearer ${token}` } : {}),
      ...(init.headers ?? {}),
    },
    cache: 'no-store',
  });

  const payload = (await response.json().catch(() => ({}))) as { message?: string; data?: T; errors?: Record<string, string[]> };

  if (!response.ok) {
    const details = payload.errors ?? {};
    const error = new Error(payload.message ?? 'Request failed');
    Object.assign(error, { details });
    throw error;
  }

  return (payload.data ?? payload) as T;
}

export async function getCheckoutSummary() {
  return request<CheckoutSummary>('/checkout');
}

export async function validateCheckout(payload: any) {
  return request<CheckoutSummary>('/checkout/validate', { method: 'POST', body: JSON.stringify(payload) });
}

export async function saveCheckoutAddress(payload: CheckoutAddress) {
  return request<{ address: CheckoutAddress }>('/checkout/address', { method: 'POST', body: JSON.stringify(payload) });
}

export async function updateCheckoutAddress(payload: CheckoutAddress) {
  return request<{ address: CheckoutAddress }>('/checkout/address', { method: 'PUT', body: JSON.stringify(payload) });
}

export async function setShippingMethod(method: 'standard' | 'express') {
  return request<{ shipping_method: { code: string; label: string; cost: number; delivery_days: number } }>('/checkout/shipping-method', {
    method: 'POST',
    body: JSON.stringify({ shipping_method: method }),
  });
}

export async function applyCheckoutCoupon(code: string) {
  return request<{ coupon: { code: string; type: string; value: number }; summary: { subtotal: number; discount: number; tax: number; shipping: number; grand_total: number } }>('/checkout/coupon', {
    method: 'POST',
    body: JSON.stringify({ code }),
  });
}

export async function removeCheckoutCoupon() {
  return request<{ coupon: null; summary: { subtotal: number; discount: number; tax: number; shipping: number; grand_total: number } }>('/checkout/coupon', {
    method: 'DELETE',
  });
}

export async function setPaymentMethod(method: 'cod' | 'razorpay') {
  return request<{ payment_method: { code: string; label: string } }>('/checkout/payment-method', {
    method: 'POST',
    body: JSON.stringify({ payment_method: method }),
  });
}

export async function reviewCheckout(payload: any) {
  return request<CheckoutSummary>('/checkout/review', { method: 'POST', body: JSON.stringify(payload) });
}

export async function createRazorpayOrder(payload: any) {
  return request<{
    order_id: string;
    order_number: string;
    payment_method: string;
    razorpay_order_id?: string;
    amount: number;
    currency: string;
    key_id: string;
    requires_payment: boolean;
  }>('/payments/checkout', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
}

export async function verifyRazorpayPayment(payload: any) {
  return request<{ success: boolean; order_id: string; payment_id: string }>('/payments/verify', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
}
