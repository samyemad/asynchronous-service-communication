
# Authorization Decision Callback Handling - Asynchronous Processing

This repository outlines the approach for handling authorization decision callbacks asynchronously. The flow involves an API controller receiving requests, storing commands in an outbox table for processing, and using cron jobs to dispatch commands to Kafka. The process is designed to ensure reliability, proper handling of authorization decisions, and failure management.

## Approach Overview

### 1️⃣ API Controller: Receives Request & Acknowledges Immediately

The API Controller performs the following:

1. Validates the incoming input (station ID, driver token, callback URL).
  - If invalid, responds with a 400 Bad Request.
  - If valid, creates and stores a `StartChargingSessionRequestCommand` in the `ChargingSessionRequestOutbox` table.

**API Response:**

```json
{
  "status": "accepted",
  "message": "Request is being processed asynchronously. The result will be sent to the provided callback URL."
}
```

A cron job or command picks up this `StartChargingSessionRequestCommand` from the `ChargingSessionRequestOutbox` table and publishes it to kafka.

### 2️⃣ StartChargingSessionRequestCommand Handler (Authorization Worker)

The `StartChargingSessionRequestCommand` is handled by the `StartChargingSessionRequestHandler` (Authorization Worker), which performs the following actions:

- Makes an asynchronous HTTP call to the internal-authorization-service to retrieve the authorization decision.

**Example Response from Internal-Authorization-Service:**

```json
{
  "station_id": "123e4567-e89b-12d3-a456-426614174000",
  "driver_token": "validDriverToken123",
  "status": "allowed"  // other values: not_allowed, unknown, invalid
}
```

The handler then stores the authorization decision in the `AuthorizationDecisionCallbackOutbox` table as `SendAuthorizationDecisionCallbackCommand`.

A cron job or command picks up the command from the `AuthorizationDecisionCallbackOutbox` table and publishes it to Kafka.

### 3️⃣ SendAuthorizationDecisionCallbackCommand (Callback Dispatcher)

Once the `SendAuthorizationDecisionCallbackCommand` is processed by the `SendAuthorizationDecisionCallbackHandler`, the flow involves sending the authorization decision to the provided callback URL.

If the callback is successful:
- stores the CallbackDeliverySucceeded event in the CallbackDeliveryOutbox table.

If the callback is failed:
- stores the CallbackDeliveryFailed event in the CallbackDeliveryOutbox table.
- ChargingSessionRequest can listen to this event and take appropriate actions, such as marking the session as failed or sending an alternative decision.

A cron job or command picks up the command from the `CallbackDeliveryOutbox` table and publishes it to Kafka.

**Example of the callback payload:**

```json
{
  "station_id": "...",
  "driver_token": "...",
  "status": "allowed"
}
```

### 4️⃣ Outbox Tables for Reliable Messaging

The outbox tables ensure reliable messaging and asynchronous communication. The following tables are used for storing commands and events:

- **ChargingSessionRequestOutbox Table:**
  - Stores `StartChargingSessionRequestCommand` commands.
  - A cron job or command dispatches the event to Kafka.

- **AuthorizationDecisionCallbackOutbox Table:**
  - Stores `SendAuthorizationDecisionCallbackCommand` commands to be processed.
  - A cron job or command periodically picks up the commands and dispatches them to Kafka.
- **CallbackDeliveryOutbox Table:**
  - Stores `CallbackDeliverySucceeded` and `CallbackDeliveryFailed` events to be processed.
  - A cron job or command periodically picks up the commands and dispatches them to Kafka.

### 5️⃣ Failure Handling and Retry Mechanism

If a callback delivery fails, it is retried automatically using the retry mechanism. For each failed callback:
- The system will retry the callback delivery using the stored commands in the `CallbackDeliveryOutbox` table.

### 6️⃣ Event Flow Summary

- **API Controller Flow:**
  1. Receives and validates the request.
  2. Creates and stores `StartChargingSessionRequestCommand` in the `ChargingSessionRequestOutbox` table.
  3. Responds to the client with a "Request is being processed asynchronously" message.

- **Authorization Worker Flow:**
  1. The `StartChargingSessionRequestCommand` is dispatched by the cron job to Kafka.
  2. The `StartChargingSessionRequestHandler` processes the command, calls the internal-authorization-service, and stores the command `SendAuthorizationDecisionCallbackCommand` in the `AuthorizationDecisionCallbackOutbox` table.
  3. A cron job or command picks up the command from the `AuthorizationDecisionCallbackOutbox` table and publishes it to Kafka.

- **Callback Dispatcher Flow:**
  1. The `SendAuthorizationDecisionCallbackHandler` processes the command `SendAuthorizationDecisionCallbackCommand`.
  2. The authorization decision is sent to the provided callback URL.
  3. If successful, stores the `CallbackDeliverySucceeded` event in the CallbackDeliveryOutbox table..
  4. If the callback fails, the system can retry sending the callback and stores the `CallbackDeliveryFailed` event in the CallbackDeliveryOutbox table.

## Tables Used

- **ChargingSessionRequestOutbox Table:**
  - Stores `StartChargingSessionRequestCommand` commands.
  - A cron job or command dispatches the event to Kafka.

- **AuthorizationDecisionCallbackOutbox Table:**
  - Stores `SendAuthorizationDecisionCallbackCommand` commands to be processed.
  - A cron job or command picks up the command and dispatches it to Kafka.
- **CallbackDeliveryOutbox Table:**
  - Stores `CallbackDeliverySucceeded` and `CallbackDeliveryFailed` events to be processed.
  - A cron job or command picks up the command and dispatches it to Kafka.

## Failure Scenarios Handled

- **Invalid Input:** Responds with a 400 Bad Request if the input is invalid.
- **Authorization Service Timeout:** Defaults to `status: unknown` if the authorization service times out.
- **Command Processing Failure:** If a command cannot be processed, it is logged for investigation, and the system continues processing the next commands.

## Conclusion

This approach ensures reliable and asynchronous processing of authorization decision callbacks, while maintaining flexibility for handling failure scenarios and retrying callbacks. The outbox pattern and cron job-based processing provide a robust mechanism for decoupling the API layer from the backend logic and ensuring reliable message delivery to Kafka.
