export type AuthUser = {
  id: number;
  name: string;
  email: string;
  phone_number?: string | null;
  address?: string | null;
  profile_photo_url?: string | null;
  email_verified_at?: string | null;
  created_at?: string;
  updated_at?: string;
};

export type OrderItemRecord = {
  id?: number;
  product_id?: number | string;
  product_name: string;
  variant_name?: string | null;
  sku?: string | null;
  quantity: number;
  unit_price: number;
  discount_amount?: number;
  tax_amount?: number;
  total_price: number;
};

export type OrderRecord = {
  id: number;
  order_number: string;
  status: string;
  payment_status: string;
  subtotal: number;
  discount_amount: number;
  tax_amount: number;
  shipping_amount: number;
  total_amount: number;
  currency: string;
  items: OrderItemRecord[];
  shipping_address?: Record<string, unknown> | null;
  billing_address?: Record<string, unknown> | null;
  shipment?: {
    carrier?: string | null;
    tracking_number?: string | null;
    status?: string | null;
  } | null;
  created_at?: string | null;
};

export type AuthSession = {
  token: string;
  user: AuthUser;
};

export type AddressRecord = {
  id: number;
  label: string;
  full_name: string;
  phone: string;
  address_line_1: string;
  address_line_2?: string | null;
  city: string;
  state: string;
  postal_code: string;
  country: string;
  is_default: boolean;
};
