
# Entity Relationships

## Tables and Relationships

### Ingredient

- **Table:** ingredients
- **Columns:**
    - `id` (Primary Key)
    - `name` (Unique)
    - `nutritionalInformation` (JSON)
- **Relationships:**
    - One-to-Many with `recipe_ingredients` (one `Ingredient` can be part of many `RecipeIngredient` records)

### Recipe

- **Table:** recipes
- **Columns:**
    - `id` (Primary Key)
    - `name` (Unique)
    - `description` (Text, nullable)
- **Relationships:**
    - One-to-Many with `recipe_ingredients` (one `Recipe` can have many `RecipeIngredient` records)

### RecipeIngredient

- **Table:** recipe_ingredients
- **Columns:**
    - `id` (Primary Key)
    - `recipe_id` (Foreign Key to `recipes.id`)
    - `ingredient_id` (Foreign Key to `ingredients.id`)
    - `quantity` (Integer)
- **Relationships:**
    - Many-to-One with `recipes` (many `RecipeIngredient` records belong to one `Recipe`)
    - Many-to-One with `ingredients` (many `RecipeIngredient` records belong to one `Ingredient`)
- **Constraints:**
    - Unique constraint on `recipe_id` and `ingredient_id` (each combination of recipe and ingredient must be unique)

### User

- **Table:** users
- **Columns:**
    - `id` (Primary Key)
    - `email` (Unique)
    - `password` (String)

## Entity Relationship Diagram (ERD)


### Relationships

- **Ingredients to RecipeIngredients**
    - One-to-Many: One `Ingredient` can be part of many `RecipeIngredients`.

- **Recipes to RecipeIngredients**
    - One-to-Many: One `Recipe` can have many `RecipeIngredients`.

- **RecipeIngredients to Recipes**
    - Many-to-One: Many `RecipeIngredients` belong to one `Recipe`.

- **RecipeIngredients to Ingredients**
    - Many-to-One: Many `RecipeIngredients` belong to one `Ingredient`.