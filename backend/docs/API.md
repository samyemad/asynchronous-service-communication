
# API Documentation

## Recipe

### 1. Get Recipes

**Endpoint:**

```
GET /api/recipes
```

**Description:**

Retrieve a list of recipes with pagination support.

**Request:**

```bash
curl --location --request GET 'http://localhost/api/recipes?limit=10&offset=1' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <YOUR_JWT_TOKEN>' \
--data-raw ''
```

**Parameters:**

- `limit`: The number of recipes to retrieve (e.g., 10).
- `offset`: The starting point for the list of recipes (e.g., 1).

**Headers:**

- `Content-Type: application/json`
- `Authorization: Bearer <YOUR_JWT_TOKEN>`

### 2. Get Recipe by ID

**Endpoint:**

```
GET /api/recipes/{id}
```

**Description:**

Retrieve details of a specific recipe by its ID.

**Request:**

```bash
curl --location --request GET 'http://localhost/api/recipes/1' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <YOUR_JWT_TOKEN>' \
--data-raw ''
```

**Path Parameters:**

- `id`: The ID of the recipe to retrieve (e.g., 1).

**Headers:**

- `Content-Type: application/json`
- `Authorization: Bearer <YOUR_JWT_TOKEN>`

### 3. Update Recipe Ingredients

**Endpoint:**

```
PATCH /api/recipes/{id}/ingredients
```

**Description:**

Update the ingredients of a specific recipe by its ID.

**Request:**

```bash
curl --location --request PATCH 'http://localhost/api/recipes/1/ingredients' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <YOUR_JWT_TOKEN>' \
--data-raw '{
  "operations": [
    {"action": "update", "ingredient": 1, "quantity": 10},
    {"action": "add", "ingredient": 2, "quantity": 30},
    {"action": "remove", "ingredient": 2}
  ]
}'
```

**Path Parameters:**

- `id`: The ID of the recipe to update (e.g., 1).

**Headers:**

- `Content-Type: application/json`
- `Authorization: Bearer <YOUR_JWT_TOKEN>`

**Request Body:**

```json
{
  "operations": [
    {"action": "update", "ingredient": 1, "quantity": 10},
    {"action": "add", "ingredient": 2, "quantity": 30},
    {"action": "remove", "ingredient": 2}
  ]
}
```

### 4. Delete Recipe

**Endpoint:**

```
DELETE /api/recipes/{id}
```

**Description:**

Delete a recipe by its ID.

**Request:**

```bash
curl --location --request DELETE 'http://localhost/api/recipes/1' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <YOUR_JWT_TOKEN>' \
--data-raw ''
```

**Path Parameters:**

- `id`: The ID of the recipe to delete (e.g., 1).

**Headers:**

- `Content-Type: application/json`
- `Authorization: Bearer <YOUR_JWT_TOKEN>`

## Ingredient

### 1. Get Ingredient by ID

**Endpoint:**

```
GET /api/ingredients/{id}
```

**Description:**

Retrieve details of a specific ingredient by its ID.

**Request:**

```bash
curl --location --request GET 'http://localhost/api/ingredients/1' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <YOUR_JWT_TOKEN>' \
--data-raw ''
```

**Path Parameters:**

- `id`: The ID of the ingredient to retrieve (e.g., 1).

**Headers:**

- `Content-Type: application/json`
- `Authorization: Bearer <YOUR_JWT_TOKEN>`

### 2. Create Ingredient

**Endpoint:**

```
POST /api/ingredients
```

**Description:**

Create a new ingredient with the provided name and nutritional information.

**Request:**

```bash
curl --location --request POST 'http://localhost/api/ingredients' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <YOUR_JWT_TOKEN>' \
--data-raw '{
    "name": "Tomato",
    "nutritionalInformation": {
        "calories": 200,
        "protein": 7,
        "carbohydrates": 40
    }
}'
```

**Headers:**

- `Content-Type: application/json`
- `Authorization: Bearer <YOUR_JWT_TOKEN>`

**Request Body:**

```json
{
    "name": "Tomato",
    "nutritionalInformation": {
        "calories": 200,
        "protein": 7,
        "carbohydrates": 40
    }
}
```

### 3. Update Ingredient

**Endpoint:**

```
PUT /api/ingredients/{id}
```

**Description:**

Update an existing ingredient with the provided details.

**Request:**

```bash
curl --location --request PUT 'http://localhost/api/ingredients/3' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <YOUR_JWT_TOKEN>' \
--data-raw '{
    "name": "Tomato",
    "nutritionalInformation": {
        "calories": 250,
        "protein": 7,
        "carbohydrates": 40
    }
}'
```

**Path Parameters:**

- `id`: The ID of the ingredient to update (e.g., 3).

**Headers:**

- `Content-Type: application/json`
- `Authorization: Bearer <YOUR_JWT_TOKEN>`

**Request Body:**

```json
{
    "name": "Tomato",
    "nutritionalInformation": {
        "calories": 250,
        "protein": 7,
        "carbohydrates": 40
    }
}
```

### 4. Delete Ingredient

**Endpoint:**

```
DELETE /api/ingredients/{id}
```

**Description:**

Delete an ingredient by its ID.

**Request:**

```bash
curl --location --request DELETE 'http://localhost/api/ingredients/1' \
--header 'Content-Type: application/json' \
--header 'Authorization: Bearer <YOUR_JWT_TOKEN>' \
--data-raw ''
```

**Path Parameters:**

- `id`: The ID of the ingredient to delete (e.g., 1).

**Headers:**

- `Content-Type: application/json`
- `Authorization: Bearer <YOUR_JWT_TOKEN>`

## User

### 1. Create User

**Endpoint:**

```
POST /api/user/create
```

**Description:**

Create a new user with the provided email and password.

**Request:**

```bash
curl --location --request POST 'http://localhost/api/user/create' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "samyemad4@gmail.com",
    "password": "123456789"
}'
```

**Headers:**

- `Content-Type: application/json`

**Request Body:**

```json
{
    "email": "samyemad4@gmail.com",
    "password": "123456789"
}
```

### 2. User Login

**Endpoint:**

```
POST /api/user/login
```

**Description:**

Authenticate a user with their email and password to receive a JWT token.

**Request:**

```bash
curl --location --request POST 'http://localhost/api/user/login' \
--header 'Content-Type: application/json' \
--data-raw '{
    "email": "samyemad4@gmail.com",
    "password": "123456789"
}'
```

**Headers:**

- `Content-Type: application/json`

**Request Body:**

```json
{
    "email": "samyemad4@gmail.com",
    "password": "123456789"
}
```
