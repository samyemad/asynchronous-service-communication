
# API Request Processing with Notification Context

This document outlines the approach for handling API requests with retry and failure mechanisms for asynchronous processing, including validation, authorization, and callback handling. This approach Include Notification Context if there is business rule on it.

## 1️⃣ API Controller (ChargingSessionRequest context) : Receives Request & Acknowledges Immediately

### Input Validation:
- The API Controller validates the input (station ID, driver token, callback URL).
    - If invalid, respond with a `400 Bad Request` error.
    - If valid, create and save a `StartChargingSessionRequestCommand` in the `ChargingSessionRequestOutbox` table.

### Response to Client:
The API immediately responds to the client with:
```json
{
  "status": "accepted",
  "message": "Request is being processed asynchronously. The result will be sent to the provided callback URL."
}
```

A command or cron job will pick up this `StartChargingSessionRequestCommand` from the `ChargingSessionRequestOutbox` table and dispatch it to Kafka.

## 2️⃣ StartChargingSessionRequestCommand Handler (Authorization Worker)

### Process:
- The `StartChargingSessionRequestCommand` is picked up and handled by the `StartChargingSessionRequestHandler` (Authorization Worker).
- Make an asynchronous HTTP call to the `internal-authorization-service`.

### Internal-Authorization-Service Response:
```json
{
  "station_id": "123e4567-e89b-12d3-a456-426614174000",
  "driver_token": "validDriverToken123",
  "status": "allowed" // other values: not_allowed, unknown, invalid
}
```

- The response is logged for debugging and auditing purposes.
- The event payload is stored in the `AuthorizationDecisionCallbackOutbox` table as `AuthorizationDecisionCallback`.

A cron job or command picks up the `AuthorizationDecisionCallback` entry from the `AuthorizationDecisionCallbackOutbox` table and publishes it to the `authorization_decision_callback` Kafka queue.

### 📌 Failure Handling:
- **Internal-Authorization-Service Timeout**: If the service times out, default to:
  ```json
  {
    "status": "unknown"
  }
  ```
  and store this in the `AuthorizationDecisionCallbackOutbox` table as `AuthorizationDecisionCallback`.

## 3️⃣ Notification Context: Sends Decision to Callback URL

### Notification Context Flow:
- The Notification Context listens for `AuthorizationDecisionCallback` events from the `authorization_decision_callback` Kafka queue.
- For each event, it sends an HTTP POST request to the provided callback URL with the authorization decision:

  ```json
  {
    "station_id": "...",
    "driver_token": "...",
    "status": "allowed"
  }
  ```

### Success:
- If the callback is successful:
    - Publishes `CallbackDeliverySucceeded` event to the `CallbackDeliveryOutbox` table.

### Failure:
- If the callback fails (e.g., invalid URL, network issue):
    - Publishes `CallbackFailed` event to the `CallbackDeliveryOutbox` table.

## 4️⃣ Event Publishing and Retry Mechanism

### Event Handling:
- A cron job or command periodically picks up events from the `CallbackDeliveryOutbox` table and publishes them to Kafka, ensuring reliable delivery of both `CallbackFailed` and `CallbackDeliverySucceeded` events.

### Retries:
- **If a callback fails**:
    - The `CallbackFailed` event is published to Kafka for retry or reprocessing.
    - `ChargingSessionRequest` can listen to this event and take appropriate actions, such as marking the session as failed or sending an alternative decision.

- **If the callback succeeds**:
    - The `CallbackDeliverySucceeded` event is published to Kafka.
    - `ChargingSessionRequest` can listen to this event to mark the request as successfully processed.

## 🗃️ Outbox Tables for Reliable Messaging

### ChargingSessionRequestOutbox Table:
- Stores `StartChargingSessionRequestCommand` events.
- A command or cron job dispatches the event to Kafka.

### AuthorizationDecisionCallbackOutbox Table:
- Stores `AuthorizationDecisionCallback` events (authorization decision results) to be delivered to the callback URL.

### CallbackDeliveryOutbox Table:
- Stores `CallbackDeliverySucceeded` and `CallbackFailed` events.
- These events are used to track the status of the callback and retry when necessary.

## 🧩 Sequence of Events

### API Controller Flow:
1. API receives request.
2. Validates input.
3. If valid, creates and saves `StartChargingSessionRequestCommand` in the `ChargingSessionRequestOutbox` table.
4. Responds to the client with an "accepted" message.
5. A command or cron job picks up the `StartChargingSessionRequestCommand` from the `ChargingSessionRequestOutbox` table and publishes it to Kafka.

### Authorization Worker Flow:
1. `StartChargingSessionRequestCommand` is dispatched by the cron job to Kafka.
2. `StartChargingSessionRequestHandler` (Authorization Worker) picks up the command.
3. Re-validates the request data.
4. Calls `internal-authorization-service` asynchronously.
5. Logs the response.
6. Stores the `AuthorizationDecisionCallback` event in the `AuthorizationDecisionCallbackOutbox` table.
7. A cron job or command picks up the `AuthorizationDecisionCallback` entry from the `AuthorizationDecisionCallbackOutbox` table and publishes it to the `authorization_decision_callback` Kafka queue.

### Notification Context Flow:
1. Notification service listens for the `AuthorizationDecisionCallback` from Kafka.
2. Attempts to deliver the decision to the callback URL.
3. A successful callback is logged in the `CallbackDeliveryOutbox`, and `CallbackDeliverySucceeded` is published to Kafka.
4. A failed callback is stored in the `CallbackDeliveryOutbox` and `CallbackFailed` event.

### Event Publishing (Retry Mechanism):
1. A cron job or command picks up both `CallbackFailed` and `CallbackDeliverySucceeded` events from the `CallbackDeliveryOutbox` and publishes them to Kafka.

## 📌 Failure Scenarios Handled:
- **Invalid Input**: Returns `400 Bad Request` response.
- **Internal-Authorization-Service Timeout**: Defaults to `status: "unknown"`.
- **Callback Delivery Failure**: The `CallbackFailed` event will be published, and the retry mechanism will handle the re-sending process or mark it as failed in `ChargingSessionRequest` context.

## 🗃️ Tables for Events

### ChargingSessionRequestOutbox Table:
- Stores `StartChargingSessionRequestCommand` events.
- A command or cron job dispatches the event to Kafka.

### AuthorizationDecisionCallbackOutbox Table:
- Stores `AuthorizationDecisionCallback` events (authorization decision results) to be delivered to the callback URL.

### CallbackDeliveryOutbox Table:
- Stores `CallbackDeliverySucceeded` and `CallbackFailed` events.
- These events are used to track the status of the callback and retry when necessary.

## Event Flow Summary:
1. **API Controller**: Receives and validates the request, saves `StartChargingSessionRequestCommand` to the `ChargingSessionRequestOutbox` table, and responds to the client with an "accepted" message.
2. **Authorization Worker**: Handles the command, makes a call to `internal-authorization-service` context, and stores the result in the `AuthorizationDecisionCallbackOutbox` table.
3. **Notification Context**: Listens for the `AuthorizationDecisionCallback` from Kafka, attempts to deliver the decision to the callback URL.

---

This approach ensures reliable processing with retries, failure handling, and clear event flow for each stage of the system.