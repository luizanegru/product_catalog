### Product Catalog

## Requirements

```bash
PHP 7.3
MySQL 8.0.19
Apache
```

## Configuration
```bash
1. Import db.sql
2. Edit config/database.php with your database credentials
```

## Endpoints:

* Authentication:
To obtain authentication token you need to make a request to:
[POST] /login
    Params:
        - email
        - password

* Note: You can find the email & password in the database in users table

After you have the auth token, you have to put it in the header of the request like this:

Authorization: Bearer <token>

*Get a list with all products:
[GET] /products/read

*Create a new product
[POST] /products/create
    Params:
        - name (string)
        - price (double)
        - category (string)

*Delete a product
[POST] /products/delete
    Params:
        - productId (integer)

*Update a product
[POST] /products/update
    Params:
        - productId
        - name
        - price
        - category

Request rate limit is set to 5 seconds. 
