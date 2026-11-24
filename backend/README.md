# Craftora Backend

This Laravel application is the API foundation for the emergency wearable e-commerce platform.

## Phase 1 goals

- initialize Laravel API structure
- configure Sanctum for future authentication
- set up API routing under `/api/v1`
- provide response and error foundations
- establish reusable service, action, request, policy, job, notification, and event patterns
- keep all future business logic server-side

## API versioning

All routes use the `/api/v1` prefix.

## Architecture foundation

- `app/Actions` for orchestration logic
- `app/Services` for domain service logic
- `app/Http/Controllers/Api/V1` for REST endpoints
- `app/Http/Requests` for validation contracts
- `app/Policies` for authorization rules
- `app/Jobs`, `app/Events`, `app/Listeners` for asynchronous workflows
- `app/Notifications` for notification channels

## Local development

```bash
php artisan serve
```

## Bulk product import

The admin bulk upload creates new products only. Download the sample CSV from
the bulk upload page and provide `name`, `slug`, `price`, and `stock_quantity`.
Product slugs and any supplied product or variant SKUs must be unique.

Use the optional `image_paths` column for semicolon-separated paths to images
already stored on the server; the first path becomes the primary image. Use
`variants_json` for a JSON array of variant objects, each with a `name` and
optional `sku`, `price`, `compare_price`, `stock`, `is_active`, and
`option_values` (an object or array). CSV quoting is required for JSON.
The entire batch is rejected if any row is invalid, and all writes are
transactional.
