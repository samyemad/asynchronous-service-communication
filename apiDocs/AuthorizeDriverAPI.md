# API Documentation

## Endpoint: `POST /api/authorize-driver`

### Request Body

## Request Body:

The request body should be sent as a JSON object with the following fields:

| Key            | Type    | Description                                                                 |
| -------------- | ------- | --------------------------------------------------------------------------- |
| `stationId`    | String  | A unique identifier for the charging station.                              |
| `driverId`     | String  | A unique identifier for the driver requesting the session.                |


#### Example Request Body:
```json
{
  "stationId": "9673200c-05a1-4b2d-aa4c-9deb130981a2",
  "driverId": "a1H_G6H8j1AAR9ZW.0vDJXKtTq2FyL",
}
```

## Response:

The response will be a JSON object with the following fields:

| Key          | Type    | Description                                          |
|--------------| ------- |------------------------------------------------------|
| `status`     | String  | The status of the authorization. Can be `"allowed"`. |
| `station_id` | String  | A unique identifier for the charging station.        |
| `driver_token` | String  | A randomly token for driver.                         |

#### Example Response:
```json
{
  "status": "allowed" | "not_allowed" | "invalid" | "unknown",
  "station_id": "string",
  "driver_token": "string"
}
```

## API explaination
- **status (string)**: The authorization status of the driver for the station.

- **station_id (string)**: The ID of the station the driver is trying to access.

- **driver_token (string)**: The token generated for the driver. This field is only populated when the status is allowed. It will contain a random token, or unknown in the case of other statuses.

