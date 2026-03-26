/**
 * API Types
 *
 * Single source of truth for every request payload and response shape
 * returned by the Laravel backend.  Import from here, not from stores.
 */

// ─── Categories ──────────────────────────────────────────────────────────────

export interface Category {
  id: number
  name: string
  slug: string
  description: string | null
  icon: string | null
  sort_order: number
}

// ─── Products ─────────────────────────────────────────────────────────────────

export interface Product {
  id: number
  category_id: number
  category: Category
  name: string
  slug: string
  description: string
  price: number
  image_url: string
  stock_quantity: number
  is_featured: boolean
}

export interface GetProductsParams {
  search?: string
  category?: string // category slug
  sort?: 'newest' | 'price-asc' | 'price-desc'
}

// ─── Cart ─────────────────────────────────────────────────────────────────────

export interface CartItem {
  id: number
  session_id: string
  user_id: number | null
  product_id: number
  quantity: number
  product: Pick<Product, 'id' | 'name' | 'price' | 'image_url' | 'stock_quantity'>
}

export interface AddToCartPayload {
  product_id: number
  quantity: number
}

export interface UpdateCartItemPayload {
  quantity: number
}

// ─── Orders ───────────────────────────────────────────────────────────────────

export type OrderStatus = 'pending' | 'processing' | 'shipped' | 'delivered' | 'cancelled'

export interface OrderItem {
  id: number
  order_id: number
  product_id: number
  product_name: string
  product_price: number
  quantity: number
  subtotal: number
}

export interface Order {
  id: number
  user_id: number
  status: OrderStatus
  subtotal: number
  total: number
  notes: string | null
  created_at: string
  updated_at: string
  items: OrderItem[]
}

export interface PlaceOrderPayload {
  notes?: string
}

export interface UpdateOrderStatusPayload {
  status: OrderStatus
}

// ─── Auth ─────────────────────────────────────────────────────────────────────

export interface User {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  is_admin: boolean
  created_at: string
}

// ─── Admin Payloads ───────────────────────────────────────────────────────────

export interface AdminProductPayload {
  category_id: number
  name: string
  description: string
  price: number | string
  stock_quantity: number | string
  is_featured?: boolean
  image?: File | null
}

export interface AdminCategoryPayload {
  name: string
  description?: string | null
  sort_order?: number
}

export interface AuthResponse {
  user: User
  token: string
}

export interface LoginPayload {
  email: string
  password: string
}

export interface RegisterPayload {
  name: string
  email: string
  password: string
  password_confirmation: string
}
