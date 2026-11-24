# Phase 7 — Checkout System

## Overview

The checkout system is implemented as an API-first flow between the Next.js storefront and Laravel. All pricing, validation, tax, shipping, coupon enforcement, and final totals are calculated on the backend using the authoritative cart, product, variant, inventory, and address data.

## Core architecture

- Cart remains the source of truth for the current cart state.
- Checkout requests are validated in Laravel before proceeding.
- Shipping, coupon, and payment choices are re-validated server-side.
- Final totals are recalculated on every review/validation step.
- The frontend cannot trust any client-submitted pricing data.

## Checkout API

### GET /api/v1/checkout
Returns the current checkout summary for the active cart.

### POST /api/v1/checkout/validate
Validates the checkout payload and returns the authoritative summary.

### POST /api/v1/checkout/address
Creates a guest or user address for checkout.

### PUT /api/v1/checkout/address
Updates an address used by the checkout flow.

### POST /api/v1/checkout/shipping-method
Sets the shipping method and recalculates associated totals.

### POST /api/v1/checkout/coupon
Applies a coupon and revalidates it against the current cart totals.

### DELETE /api/v1/checkout/coupon
Removes the coupon and recalculates totals.

### POST /api/v1/checkout/payment-method
Stores the selected payment method in the current checkout state.

### POST /api/v1/checkout/review
Generates the final checkout review payload, including order totals and selected details.

## Guest checkout

Guests can proceed through checkout using a session-scoped cart. Requirements include:

- valid shipping address
- valid billing address or shipping-as-billing flag
- valid shipping method
- valid payment method
- active cart with valid products
- sufficient inventory

## Authenticated checkout

Authenticated users can reuse their saved addresses and can add new ones. Address ownership is enforced server-side.

## Shipping

Supported methods for this phase:

- standard
- express

Shipping cost is configured in the backend and never trusted from the frontend.

## Taxes and totals

The backend recalculates:

- subtotal
- discount
- tax
- shipping
- grand total

Tax is calculated using the backend tax rate and is returned in the final checkout summary.

## Coupon behavior

Coupons are revalidated at checkout time.

Validation includes:

- active status
- start date and expiry date
- cart minimum total
- discount type
- max discount handling

## Payment-method abstraction

This phase supports a payment-method abstraction but does not implement payment processing.

Supported methods:

- cod
- razorpay

Razorpay API calls and verification are intentionally deferred to Phase 8.

## Security notes

- Price and inventory are reloaded from the backend before checkout completion.
- Guest cart/session state is respected.
- Address ownership and cart ownership are validated on the server.
- Final totals are derived from backend state, not frontend values.
- No payment credentials are stored or collected in this phase.

## Phase 8 boundary

Phase 8 will integrate the selected payment method, likely Razorpay, into the validated checkout summary produced by this phase.
