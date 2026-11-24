# Architecture Overview

## 1. Project purpose and constraints

This platform is an emergency-alert and monitoring system for wearable devices and connected users. It is not a medical diagnosis system and must not be positioned as one. The product supports emergency detection, user safety monitoring, alert escalation, device management, e-commerce checkout, subscriptions, notification flows, admin operations, and privacy-safe operational monitoring.

Core constraints:
- Next.js must communicate with Laravel only through REST APIs.
- Business logic must remain in Laravel.
- Sensitive location, alert, device, and subscription data must be protected.
- AI features may assist operations such as risk prioritization, event summarization, anomaly detection, contact escalation suggestions, and user safety support, but not diagnosis.
- Production architecture must be designed for reliability, observability, privacy, and scale.

## 2. System goals

- Enable users to register and manage emergency wearable devices.
- Send alert signals and escalation events in real time.
- Process subscriptions and store purchases safely.
- Notify users and emergency contacts through push notifications and other channels.
- Provide admin monitoring and operational controls.
- Maintain location and event history in a privacy-aware structure.
- Keep all critical business rules in the backend.

## 3. High-level architecture

The platform is split into four primary layers:

1. Presentation layer: Next.js frontend for customer, admin, and operational experiences.
2. API layer: Laravel REST API that provides all backend business capabilities.
3. Data layer: MySQL for transactional data, Redis for cache/queues/pub-sub, S3 for file storage.
4. Integration layer: Razorpay, FCM, AWS S3, optional WebSockets/Broadcasting, admin telemetry, and audit systems.

Interaction model:
- Frontend calls Laravel REST endpoints only.
- Laravel handles request validation, authorization, workflows, notifications, and persistence.
- Laravel emits queued jobs and events for notifications, escalations, and background processing.
- Redis handles queue workers, throttling, rate limiting tokens, and fast lookups.
- MySQL stores canonical records.
- S3 stores uploaded media and device-related files.

## 4. Frontend architecture

The frontend will be a Next.js + React + TypeScript application using Tailwind CSS.

Recommended frontend architecture:
- App Router structure for route-based UI and layout separation.
- Domain-oriented feature folders under src/features.
- Shared UI components under src/components.
- API integration layer under src/lib/api and src/services.
- Auth state under src/store or auth providers.
- Environment-specific settings under src/config.
- No business logic in components; all business operations occur through API calls.

Frontend responsibilities:
- Authentication flows and protected route handling.
- Product and subscription browsing.
- Device registration and pairing screens.
- User profile and emergency contact management.
- Alert dashboard and event timelines.
- Admin portal modules for operations and support.
- Notifications and account settings.

Frontend must never:
- Calculate pricing, rules, refunds, or subscription status.
- Decide emergency escalation logic.
- Validate device ownership or billing authorization alone.
- Mutate critical business state without backend confirmation.

## 5. Backend architecture

Laravel is the system of record for business logic and workflows.

Core backend responsibilities:
- Authentication and authorization with Sanctum.
- Device lifecycle workflows.
- Order and subscription management.
- Alert and escalation orchestration.
- Notification dispatching via queues and notifications.
- Webhook processing and external integration handling.
- Reporting, logs, and admin audits.

Recommended backend structure:
- app/Http/Controllers for API endpoints.
- app/Services for business logic orchestrations.
- app/Actions or domain services for domain-specific logic.
- app/Jobs for asynchronous processing.
- app/Events and app/Listeners for decoupled workflows.
- app/Notifications for user and admin notifications.
- app/Models for core entities.
- app/Policies for authorization checks.
- app/Providers for integration bootstrapping.
- routes/api.php for REST APIs.
- config for service credentials and policies.

## 6. Domain modules

The major modules are:
- User and account management
- Authentication and authorization
- Device management
- Location services
- Emergency event engine
- Alert escalation and response
- Notifications and communication
- E-commerce and checkout
- Subscription management
- Admin operations and support
- Audit, logging, and privacy controls

## 7. Database architecture

Use MySQL as the transactional source of truth. Use Redis for ephemeral data, caching, queue management, and event fan-out. Use S3 for asset storage and media attachments.

The database must support:
- user accounts and roles
- device inventory and assignment
- emergency contacts and relationships
- device telemetry and signal history
- location events and coordinate snapshots
- event records and alert states
- orders, invoices, payments, and subscriptions
- admin tasks and audit trails

## 8. Authentication and authorization

Authentication is handled by Laravel Sanctum. For customer and admin dashboards, the frontend will use secure session-based or token-based API auth depending on deployment model.

Authorization rules:
- Users access only their own devices, contacts, profiles, and alerts.
- Admins get limited operational permissions via policy checks.
- Subscription and payment actions require stricter verification.
- Device actions must be validated against ownership and assignment status.

## 9. Emergency workflow model

The emergency workflow is a backend orchestrated state machine:
1. Device emits a trigger or location anomaly.
2. Laravel validates the event and device status.
3. Event is classified or prioritized.
4. Notification fan-out occurs to user, contacts, and configured responders.
5. Safety workflow determines escalation and response steps.
6. Event remains auditable and reviewable by admins.

Important guardrail:
- The system should support last-mile safety response and monitoring, not provide medical diagnosis.
- Human escalation and emergency procedures remain outside system automation.

## 10. Privacy and security principles

- Minimal data retention for raw location events.
- Confidentiality for emergency contacts and device ownership data.
- Encrypted storage for sensitive fields where required.
- Explicit consent for communication and data handling.
- Audit logs for all sensitive operations.
- Redaction or masking of sensitive values in admin dashboards.

## 11. Integration architecture

Key external systems:
- Razorpay for payment and subscription processing.
- FCM for push notification delivery to devices and app clients.
- AWS S3 for media, backups, and asset hosting.
- WebSockets/Broadcasting for live status updates when needed.

Webhook handling:
- Payment webhook validation must be strict.
- Notification tokens must be refreshed and validated.
- Incoming data must be verified and mapped to internal records only after validation.

## 12. Testing strategy

Testing must be layered:
- Unit tests for core business rules.
- Feature tests for API workflows.
- Integration tests for queues, notifications, and webhooks.
- Contract tests for external provider compatibility.
- Frontend component tests for UI and error states.
- Security tests for auth, role checks, and rate limits.

## 13. Deployment architecture

Recommended deployment model:
- Next.js frontend deployed on a modern frontend platform with CDN support.
- Laravel API deployed on managed PHP application infrastructure.
- MySQL in a managed database tier.
- Redis in a managed cache/queue tier.
- S3 for object storage.
- Background workers running Laravel queue consumers.
- WAF and TLS termination at the edge layer.

## 14. Key risks and scalability concerns

- Real-time alert bursts can cause queue pressure if not scaled.
- Location data volume may grow quickly; retention levels and indexing require planning.
- Notification fatigue can reduce user trust if alerts are noisy.
- External webhook reliability and retries must be handled carefully.
- Privacy and compliance issues increase if location and contact data are over-collected.
- Fraud and abuse risk in subscriptions and order flows.

## 15. Recommendation

The system should be implemented in clearly separated modules with a strong backend-first design. The backend is the system of record; the frontend is an interface. This reduces the chance of policy bypasses and keeps critical decision paths auditable and reliable.

---

## Proposed module order

1. Authentication and user management
2. Device management
3. Location tracking and geofencing
4. Emergency event engine
5. Notification system
6. Subscription and billing
7. Admin and support modules
8. Reporting, privacy, and audit tooling

## Summary

This architecture treats the product as a safety, monitoring, and commerce platform. It uses Laravel as the authoritative business layer and Next.js as a thin client. Data protection, event rigor, and proper escalations are central to the design.
