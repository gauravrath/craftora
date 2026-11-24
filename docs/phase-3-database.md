# Phase 3 - Database Migrations and Models

## Scope

This phase introduces the core transactional and operational model layer for the Craftora platform.

### Included modules
- Users, roles, permissions
- Catalog and pricing (categories, products, variants, inventory)
- Commerce (carts, addresses, coupons, orders, order items, payments, refunds, shipments)
- Device management (device models, devices, activations, sensor data, events)
- Emergency workflow (contacts, events, notifications)
- Location and subscription management
- Notification and audit tracking

## Design principles

- Use decimal values for money; never float or double for pricing or totals.
- Use UUIDs for high-level aggregate records and event data where natural identity is needed.
- Separate sensor/event telemetry from transactional records.
- Prefer soft deletes for business data that may need historical auditability.
- Add indexes on high-lookup fields and composite filters.
- Protect sensitive fields with careful serialization and restricted access patterns.

## Critical database rules

- `products.price`, `product_variants.price`, `orders.total_amount`, `payments.amount`, and other monetary values are stored as decimal columns.
- `device_sensor_data` is treated as high-frequency telemetry and kept separate from ordinary transactional tables.
- `audit_logs` stores operational changes and supports investigation workflows.
- `emergency_events` and `notifications` support alerting and incident tracking.

## Tables

- roles
- permissions
- users
- categories
- products
- product_images
- product_variants
- inventory
- carts
- cart_items
- addresses
- coupons
- orders
- order_items
- payments
- refunds
- shipments
- device_models
- devices
- device_activations
- device_sensor_data
- device_events
- emergency_contacts
- emergency_events
- emergency_notifications
- locations
- subscription_plans
- subscriptions
- notifications
- audit_logs

## Relationship overview

- User has many roles, devices, addresses, subscriptions, orders, carts, notifications, and audit logs.
- Role has many users and permissions.
- Category has many products; product belongs to one category.
- Product has many images, variants, inventory, cart items, and order items.
- Cart belongs to a user and has many cart items.
- Order belongs to a user and has many items, payments, refunds, and one shipment.
- Device belongs to a user and a model; it has many activations, sensor readings, and events.
- Emergency event belongs to a user and optional device; it owns notifications.
- Subscription belongs to a user and a plan.
- Notification belongs to a user.
- Audit log references users and entities for traceability.

## Migration files

- [backend/database/migrations/2026_08_24_120000_create_roles_and_permissions_tables.php](backend/database/migrations/2026_08_24_120000_create_roles_and_permissions_tables.php)
- [backend/database/migrations/2026_08_24_120100_create_catalog_tables.php](backend/database/migrations/2026_08_24_120100_create_catalog_tables.php)
- [backend/database/migrations/2026_08_24_120200_create_commerce_tables.php](backend/database/migrations/2026_08_24_120200_create_commerce_tables.php)
- [backend/database/migrations/2026_08_24_120300_create_device_tables.php](backend/database/migrations/2026_08_24_120300_create_device_tables.php)
- [backend/database/migrations/2026_08_24_120400_create_emergency_and_subscription_tables.php](backend/database/migrations/2026_08_24_120400_create_emergency_and_subscription_tables.php)
- [backend/database/migrations/2026_08_24_120500_create_notification_and_audit_tables.php](backend/database/migrations/2026_08_24_120500_create_notification_and_audit_tables.php)

## Model files

- [backend/app/Models/User.php](backend/app/Models/User.php)
- [backend/app/Models/Role.php](backend/app/Models/Role.php)
- [backend/app/Models/Permission.php](backend/app/Models/Permission.php)
- [backend/app/Models/Category.php](backend/app/Models/Category.php)
- [backend/app/Models/Product.php](backend/app/Models/Product.php)
- [backend/app/Models/ProductImage.php](backend/app/Models/ProductImage.php)
- [backend/app/Models/ProductVariant.php](backend/app/Models/ProductVariant.php)
- [backend/app/Models/Inventory.php](backend/app/Models/Inventory.php)
- [backend/app/Models/Cart.php](backend/app/Models/Cart.php)
- [backend/app/Models/CartItem.php](backend/app/Models/CartItem.php)
- [backend/app/Models/Address.php](backend/app/Models/Address.php)
- [backend/app/Models/Coupon.php](backend/app/Models/Coupon.php)
- [backend/app/Models/Order.php](backend/app/Models/Order.php)
- [backend/app/Models/OrderItem.php](backend/app/Models/OrderItem.php)
- [backend/app/Models/Payment.php](backend/app/Models/Payment.php)
- [backend/app/Models/Refund.php](backend/app/Models/Refund.php)
- [backend/app/Models/Shipment.php](backend/app/Models/Shipment.php)
- [backend/app/Models/DeviceModel.php](backend/app/Models/DeviceModel.php)
- [backend/app/Models/Device.php](backend/app/Models/Device.php)
- [backend/app/Models/DeviceActivation.php](backend/app/Models/DeviceActivation.php)
- [backend/app/Models/DeviceSensorData.php](backend/app/Models/DeviceSensorData.php)
- [backend/app/Models/DeviceEvent.php](backend/app/Models/DeviceEvent.php)
- [backend/app/Models/EmergencyContact.php](backend/app/Models/EmergencyContact.php)
- [backend/app/Models/EmergencyEvent.php](backend/app/Models/EmergencyEvent.php)
- [backend/app/Models/EmergencyNotification.php](backend/app/Models/EmergencyNotification.php)
- [backend/app/Models/Location.php](backend/app/Models/Location.php)
- [backend/app/Models/SubscriptionPlan.php](backend/app/Models/SubscriptionPlan.php)
- [backend/app/Models/Subscription.php](backend/app/Models/Subscription.php)
- [backend/app/Models/Notification.php](backend/app/Models/Notification.php)
- [backend/app/Models/AuditLog.php](backend/app/Models/AuditLog.php)
