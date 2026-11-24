# Emergency Workflow Architecture

## 1. Purpose

This workflow defines how emergency events are created, evaluated, escalated, and resolved in the platform without turning the system into a medical diagnosis engine.

The system supports safety monitoring, alert dispatch, escalation coordination, and response workflow management. It does not diagnose a medical condition.

## 2. Core workflow stages

### Stage 1: Device registration and activation
- User registers account.
- Device is assigned to a user or family account.
- Device pairing and activation status is confirmed.
- Emergency contacts and alert preferences are configured.

### Stage 2: Monitoring readiness
- Device health and connectivity checks are recorded.
- Location sharing and alert permissions are validated.
- Notification channels are linked to user and contacts.

### Stage 3: Event trigger
- Device or user sends a trigger signal.
- Location, signal quality, and time data are captured.
- The event is validated against the user’s active device status.

### Stage 4: Event classification and prioritization
- Laravel evaluates device state, event metadata, and escalation rules.
- Event is categorized as low, medium, high, or critical priority.
- AI-assisted features may support prioritization or summarization, but not diagnosis.

### Stage 5: Alert dispatch
- User receives alert confirmation and safe-check prompts.
- Emergency contacts receive notifications based on policy.
- Admin or support team is notified for high-priority incidents.

### Stage 6: Human confirmation and resolution
- User can acknowledge or verify safety state.
- Escalation may be paused, escalated, or reviewed by a support workflow.
- Human responders can act according to local emergency procedures.

### Stage 7: Event closure and audit
- Event status changes to resolved, acknowledged, escalated, or false alarm.
- Audit logs capture who received alerts, who acknowledged them, and what actions happened.
- Post-incident review is available to admin teams.

## 3. Alert state machine

Possible alert states:
- pending
- validated
- acknowledged
- escalated
- resolved
- rejected
- false-positive review

State changes must be enforced in Laravel, not the frontend.

## 4. Escalation rules

Escalation should follow policy-based logic:
- device inactive or offline
- repeated triggers
- location anomaly or no movement after trigger
- emergency contact not reachable
- user has not acknowledged within policy window

## 5. Safety guardrails

- No medical diagnosis or treatment recommendation.
- Do not present a trigger as a diagnosis.
- Emergency workflows must keep the human response path explicit and clear.
- Alert content should be concise, actionable, and privacy-conscious.

## 6. Operational requirements

- User alerts must be idempotent to prevent duplicates.
- Notifications must be retried safely through queue workers.
- Critical events need priority processing.
- Event history must be retained according to policy and legal requirements.

## 7. Event data model

The event model should capture:
- event id and type
- device id and user id
- trigger source
- timestamp
- geolocation snapshot
- severity
- status
- acknowledgment details
- resolution info
- audit trail

## 8. Workflow summary

This emergency event engine is designed to support fast safety response and operational oversight while respecting the strict limitation that the platform is not a medical diagnosis tool.
