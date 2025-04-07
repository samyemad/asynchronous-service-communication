
# Charging Session Request API Documentation

## Endpoint:
`POST /api/charging-session-requests`

### Description:
This endpoint allows users to submit a charging session request. It accepts the details of the charging session, including station and driver information, and a callback URL to notify the user once the session is processed.


---

## Request Body:

The request body should be sent as a JSON object with the following fields:

| Key            | Type    | Description                                                                 |
| -------------- | ------- | --------------------------------------------------------------------------- |
| `stationId`    | String  | A unique identifier for the charging station.                              |
| `driverId`     | String  | A unique identifier for the driver requesting the session.                |
| `callbackUrl`  | String  | The URL where the server will send a notification once the session is processed. |

#### Example Request Body:
```json
{
  "stationId": "9673200c-05a1-4b2d-aa4c-9deb130981a2",
  "driverId": "a1H_G6H8j1AAR9ZW.0vDJXKtTq2FyL",
  "callbackUrl": "https://webhook.site/{id}"
}
```

---

## Response:

The response will be a JSON object with the following fields:

| Key       | Type    | Description                                |
| --------- | ------- | ------------------------------------------ |
| `status`  | String  | The status of the request. Can be `"acknowledged"`. |
| `message` | String  | A message that provides additional details about the request. In this case, it acknowledges the receipt of the request. |

#### Example Response:
```json
{
  "status": "acknowledged",
  "message": "Charging Session request received"
}
```

---

## Response Codes:

- **200 OK**: The request was successful, and the charging session request was acknowledged.
- **400 Bad Request**: The request was malformed, or required parameters were missing.
- **500 Internal Server Error**: There was an error on the server while processing the request.

---

## Example Workflow:

1. **Client sends a `POST` request** with a JSON payload to the `charging-session-requests` endpoint.
2. **Server processes the request**, validates the parameters, and returns a response with `status: acknowledged` and a confirmation message.
3. **Client receives the response**, confirming that the charging session request has been received by the server.
4. Optionally, the server can use the `callbackUrl` provided in the request to notify the client once the session is processed.

---

## Summary:

This API endpoint provides a mechanism for submitting a charging session request. The client must provide the `stationId`, `driverId`, and a `callbackUrl` for receiving notifications. Upon successful processing, the server acknowledges the request with a response.
