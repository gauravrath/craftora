import type { ApiResponse } from '@/types/api';
import type { AddressRecord, AuthSession, AuthUser, OrderRecord } from '@/types/auth';

const API_BASE_URL = process.env.NEXT_PUBLIC_API_URL ?? 'http://localhost:8001/api/v1';
const API_ROOT_URL = API_BASE_URL.replace(/\/api\/v1$/, '');
const TOKEN_KEY = 'craftora_auth_token';
const USER_KEY = 'craftora_auth_user';

function getCsrfToken(): string {
  if (typeof document === 'undefined') {
    return '';
  }

  const match = document.cookie.match(/(?:^|;\s*)XSRF-TOKEN=([^;]+)/);
  return match ? decodeURIComponent(match[1]) : '';
}

async function ensureCsrfCookie() {
  if (typeof window === 'undefined') {
    return;
  }

  await fetch(`${API_ROOT_URL}/sanctum/csrf-cookie`, {
    method: 'GET',
    credentials: 'include',
    cache: 'no-store',
  });
}

export function readAuthSession(): AuthSession | null {
  if (typeof window === 'undefined') {
    return null;
  }

  const token = window.sessionStorage.getItem(TOKEN_KEY);
  const user = window.sessionStorage.getItem(USER_KEY);

  if (!token || !user) {
    return null;
  }

  try {
    return { token, user: JSON.parse(user) as AuthUser };
  } catch {
    clearAuthSession();
    return null;
  }
}

export function saveAuthSession(session: AuthSession) {
  if (typeof window === 'undefined') {
    return;
  }

  window.sessionStorage.setItem(TOKEN_KEY, session.token);
  window.sessionStorage.setItem(USER_KEY, JSON.stringify(session.user));
}

export function clearAuthSession() {
  if (typeof window === 'undefined') {
    return;
  }

  window.sessionStorage.removeItem(TOKEN_KEY);
  window.sessionStorage.removeItem(USER_KEY);
}

async function request<T>(path: string, init: RequestInit = {}, requireAuth = false): Promise<T> {
  const session = readAuthSession();
  const headers = new Headers(init.headers ?? {});
  const method = (init.method ?? 'GET').toUpperCase();

  headers.set('Accept', 'application/json');

  if (method !== 'GET') {
    await ensureCsrfCookie();
    const csrfToken = getCsrfToken();
    if (csrfToken) {
      headers.set('X-XSRF-TOKEN', csrfToken);
    }
  }

  if (!(init.body instanceof FormData)) {
    headers.set('Content-Type', 'application/json');
  }

  if (requireAuth && session?.token) {
    headers.set('Authorization', `Bearer ${session.token}`);
  }

  const response = await fetch(`${API_BASE_URL}${path}`, {
    ...init,
    headers,
    cache: 'no-store',
    credentials: 'include',
  });

  const payload = (await response.json().catch(() => ({}))) as ApiResponse<T>;

  if (!response.ok) {
    const details = payload.errors ?? {};
    const error = new Error(payload.message ?? 'Request failed');
    Object.assign(error, { details });
    throw error;
  }

  return payload.data as T;
}

export async function registerUser(payload: {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
  phone_number?: string;
}) {
  const response = await request<{ user: AuthUser; token: string; token_type: string }>(
    '/auth/register',
    { method: 'POST', body: JSON.stringify(payload) },
  );

  saveAuthSession({ token: response.token, user: response.user });
  return response;
}

export async function loginUser(payload: { email: string; password: string }) {
  const response = await request<{ user: AuthUser; token: string; token_type: string }>(
    '/auth/login',
    { method: 'POST', body: JSON.stringify(payload) },
  );

  saveAuthSession({ token: response.token, user: response.user });
  return response;
}

export async function logoutUser() {
  await request<{ success: boolean }>(
    '/auth/logout',
    { method: 'POST' },
    true,
  );
  clearAuthSession();
}

export async function requestPasswordReset(email: string) {
  return request<{ success: boolean; message: string }>('/auth/forgot-password', {
    method: 'POST',
    body: JSON.stringify({ email }),
  });
}

export async function resetPassword(payload: {
  token: string;
  email: string;
  password: string;
  password_confirmation: string;
}) {
  return request<{ success: boolean; message: string }>('/auth/reset-password', {
    method: 'POST',
    body: JSON.stringify(payload),
  });
}

export async function getProfile() {
  return request<AuthUser>('/auth/me', { method: 'GET' }, true);
}

export async function updateProfile(payload: {
  name?: string;
  email?: string;
  phone_number?: string | null;
  address?: string | null;
}) {
  const response = await request<AuthUser>('/user/profile', {
    method: 'PUT',
    body: JSON.stringify(payload),
  }, true);

  const session = readAuthSession();
  if (session) {
    saveAuthSession({ ...session, user: response });
  }

  return response;
}

export async function changePassword(payload: {
  current_password: string;
  password: string;
  password_confirmation: string;
}) {
  return request<{ success: boolean; message: string }>('/user/change-password', {
    method: 'POST',
    body: JSON.stringify(payload),
  }, true);
}

export async function uploadProfilePhoto(file: File) {
  const formData = new FormData();
  formData.append('photo', file);

  const response = await request<AuthUser>('/user/profile-photo', {
    method: 'POST',
    body: formData,
  }, true);

  const session = readAuthSession();
  if (session) {
    saveAuthSession({ ...session, user: response });
  }

  return response;
}

export async function getOrders() {
  return request<OrderRecord[]>('/orders', { method: 'GET' }, true);
}

export async function getOrder(orderId: string | number) {
  return request<OrderRecord>(`/orders/${orderId}`, { method: 'GET' }, true);
}

export async function cancelOrder(orderId: string | number) {
  return request<OrderRecord>(`/orders/${orderId}/cancel`, { method: 'POST' }, true);
}

export async function getOrderInvoice(orderId: string | number) {
  return request<Record<string, unknown>>(`/orders/${orderId}/invoice`, { method: 'GET' }, true);
}

export async function getAddresses() {
  return request<AddressRecord[]>('/user/addresses', { method: 'GET' }, true);
}

export async function createAddress(payload: {
  label: string;
  full_name: string;
  phone: string;
  address_line_1: string;
  address_line_2?: string;
  city: string;
  state: string;
  postal_code: string;
  country: string;
  is_default?: boolean;
}) {
  return request<AddressRecord>('/user/addresses', {
    method: 'POST',
    body: JSON.stringify(payload),
  }, true);
}
