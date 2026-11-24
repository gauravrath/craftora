# API Architecture

## 1. API design principles

The backend exposes a REST API for all client interactions. The frontend must not bypass Laravel business logic or perform critical state changes directly.

Design rules:
- all client-facing actions go through Laravel API endpoints
- authentication and authorization are enforced server-side
- requests are validated with Laravel Form Requests or equivalent schema validation
- high-risk workflows are protected by policies, rate limits, and event-driven validation
- response shapes are explicit and versionable

## 2. API layers

### Public endpoints
- auth/register
- auth/login
- auth/forgot-password
- auth/reset-password
- public product catalog
- public pricing and plan information

### Authenticated customer endpoints
- user profile and preferences
- device registration and pairing
- emergency contact management
- location consent and device status
- order history and payment status
- subscription management
- event history and alert acknowledgements

### Admin endpoints
- admin dashboard summary
- user and device management
- review and triage of emergency events
- subscription and payment oversight
- support workflow operations

### Internal/system endpoints
- webhook callbacks from Razorpay
- FCM token sync callbacks
- event callbacks or ingestion endpoints
- queue health and operational hooks

## 3. API versioning

Recommended pattern:
- /api/v1/
- keep v1 stable across the planning and early implementation phases
- version major changes explicitly if contract changes are required

## 4. Module-based API groups

### Authentication module
- POST /api/v1/auth/register
- POST /api/v1/auth/login
- POST /api/v1/auth/logout
- POST /api/v1/auth/refresh
- POST /api/v1/auth/forgot-password
- POST /api/v1/auth/reset-password

### User module
- GET /api/v1/users/me
- PUT /api/v1/users/me
- GET /api/v1/users/me/preferences
- PUT /api/v1/users/me/preferences

### Device module
- GET /api/v1/devices
- POST /api/v1/devices
- GET /api/v1/devices/{id}
- PUT /api/v1/devices/{id}
- DELETE /api/v1/devices/{id}
- POST /api/v1/devices/{id}/pair
- POST /api/v1/devices/{id}/status

### Contact module
- GET /api/v1/emergency-contacts
- POST /api/v1/emergency-contacts
- PUT /api/v1/emergency-contacts/{id}
- DELETE /api/v1/emergency-contacts/{id}

### Location module
- GET /api/v1/locations
- POST /api/v1/locations
- GET /api/v1/locations/{id}
- POST /api/v1/locations/{id}/sync

### Event module
- GET /api/v1/events
- GET /api/v1/events/{id}
- POST /api/v1/events/{id}/acknowledge
- POST /api/v1/events/{id}/escalate
- GET /api/v1/events/{id}/timeline

### Notification module
- GET /api/v1/notifications
- PUT /api/v1/notifications/{id}/read
- POST /api/v1/notifications/preferences

### Commerce module
- GET /api/v1/categories — active categories in name order; S3 media paths are returned as temporary URLs valid for 30 minutes.
- GET /api/v1/products
- GET /api/v1/products/{id}
- POST /api/v1/cart
- GET /api/v1/cart
- POST /api/v1/orders
- GET /api/v1/orders/{id}
- POST /api/v1/payments/checkout
- POST /api/v1/payments/webhook/razorpay

### Subscription module
- GET /api/v1/subscriptions
- POST /api/v1/subscriptions
- PUT /api/v1/subscriptions/{id}
- POST /api/v1/subscriptions/{id}/cancel

### Admin module
- GET /api/v1/admin/dashboard
- GET /api/v1/admin/users
- GET /api/v1/admin/devices
- GET /api/v1/admin/events
- POST /api/v1/admin/events/{id}/review

## 5. API response conventions

Use consistent JSON structure:
- data
- message
- errors
- meta
- pagination for list endpoints

Example:
{
  "data": { ... },
  "message": "Success",
  "meta": {
    "request_id": "..."
  }
}

## 6. Error handling

- standardized error envelope
- proper HTTP status codes
- sanitized messages for unauthorized or forbidden actions
- no stack traces in production responses

## 7. Rate limiting and abuse prevention

Apply rate limits to:
- login attempts
- password reset requests
- order creation
- event submissions
- notification preferences changes
- webhook verification endpoints

## 8. Webhook and callback processing

- validate signatures before trust
- process asynchronously with queue workers
- persist raw payloads for auditability
- idempotently reconcile final status

## 9. API security requirements

- strict validation on every request
- role and policy checks for each route
- CSRF is not applicable for token-based API architecture in the same way as browser forms, but request integrity and origin checks are still required where applicable
- sanitize all outputs before sending to the frontend

## 10. Deployment note

REST APIs should be deployed with environment separation and observability. Response times, queue delays, and webhook success rates should be tracked continuously.
