# Phase 3 ERD Overview

## Entity groups

### Access and identity
- users
- roles
- permissions

### Catalog
- categories
- products
- product_images
- product_variants
- inventory

### Commerce
- carts
- cart_items
- addresses
- coupons
- orders
- order_items
- payments
- refunds
- shipments

### Devices and emergency
- device_models
- devices
- device_activations
- device_sensor_data
- device_events
- emergency_contacts
- emergency_events
- emergency_notifications
- locations

### Subscription and notifications
- subscription_plans
- subscriptions
- notifications
- audit_logs

## Key relationships

- roles 1-n users
- users 1-n devices
- users 1-n addresses
- users 1-n subscriptions
- users 1-n orders
- users 1-n emergency_contacts
- users 1-n emergency_events
- users 1-n notifications
- categories 1-n products
- products 1-n product_images
- products 1-n product_variants
- products 1-n inventory
- carts 1-n cart_items
- orders 1-n order_items
- orders 1-n payments
- orders 1-n refunds
- orders 1-n shipments
- device_models 1-n devices
- devices 1-n device_activations
- devices 1-n device_sensor_data
- devices 1-n device_events
- emergency_events 1-n emergency_notifications
- subscription_plans 1-n subscriptions

## Notes

- The schema keeps telemetry in `device_sensor_data` separate from order and user records to minimize write contention and support retention policies.
- Money fields remain decimal-based to avoid floating-point precision bugs.
- Soft delete is used for business records with historical requirement and auditing.
- UUID identifiers are used for aggregate and event-style records where natural keys are useful across services.
