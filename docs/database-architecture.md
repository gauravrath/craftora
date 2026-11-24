# Database Architecture

## 1. Database strategy

Use MySQL as the transactional source of truth. Use Redis for queue coordination, caching, session acceleration, and low-latency operational data. Use AWS S3 for object storage and media assets.

## 2. Core principles

- business-critical tables live in MySQL
- Redis stores volatile or derived data only
- sensitive files are stored in S3 rather than database blobs
- event and audit records are append-friendly and reviewable
- indexes are created for user lookup, device lookup, and event filtering

## 3. Main MySQL modules

### Users and access
- users
- user_profiles
- roles
- permissions
- user_roles
- user_sessions
- user_preferences

### Device management
- devices
- device_models
- device_status_history
- device_assignments
- device_tokens
- device_telemetry

### Contact and location
- emergency_contacts
- user_contact_groups
- locations
- geofences
- location_history
- location_events

### Emergency workflow
- emergency_events
- event_alerts
- event_acknowledgements
- event_escalations
- event_audit_logs

### Commerce and subscriptions
- products
- product_variants
- carts
- orders
- order_items
- payments
- subscriptions
- invoices
- refund_records

### Notification and communication
- notification_templates
- notification_events
- push_tokens
- notification_preferences
- communication_logs

### Admin and operations
- admin_users
- admin_roles
- support_cases
- operational_logs
- audit_trails

## 4. Relationship patterns

- Users have many devices, contacts, orders, subscriptions, and events.
- Devices belong to users through assignments and can have many telemetry records.
- Emergency events belong to devices and users, and may trigger many notifications.
- Orders belong to users and have many order items and payment records.
- Subscriptions belong to users and map to plan entitlements.
- Audit logs reference the user, admin, device, or event involved.

## 5. Redis usage

Redis should be used for:
- queue management for notification jobs and webhook processing
- rate limiting for API requests
- caching user/device configuration
- temporary event fan-out metadata
- short-lived session and token state
- lightweight presence or active-session tracking

## 6. Storage and retention policy

- raw location snapshots retained only as long as needed
- device telemetry stored with retention windows and summarization
- event records retained for investigations, compliance, and support
- media assets stored in S3 with signed access and lifecycle policies

## 7. Indexing strategy

Essential indexes:
- user email and phone lookup
- device serial number and pairing keys
- active subscription records
- event status + creation date
- user id + device id + event timestamps
- order and payment references
- notification token lookup by user/device

## 8. Data integrity rules

- Device ownership must be enforced at the database and application layer.
- Payment records must be immutable once finalized.
- Event state transitions must be logged.
- Deletion and anonymization should be policy-driven, not ad hoc.

## 9. Database implementation guidance

- Use Laravel migrations for all schema changes.
- Use foreign key constraints where feasible.
- Separate operational tables from user-facing data models when necessary.
- Use transactional patterns for order and payment state coherence.

## 10. Architectural note

The database design must prioritize safety, traceability, and enforcement of business rules. Emergency events and device assignment records must remain authoritative and auditable.
