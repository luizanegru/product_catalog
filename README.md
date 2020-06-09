# Product Catalog

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

* ***Authentication:***
To obtain authentication token you need to make a request to:
```bash
[POST] /login
    Params:
        - email
        - password
 ```

* Note: You can find the email & password in the database in users table

* After you have the auth token, you have to put it in the header of the request like this:

* Authorization: Bearer token 

<br>

* ***Get a list with all products:***
```bash
[GET] /products/read
```
<br>

* ***Create a new product:***
```bash
[POST] /products/create
    Params:
        - name (string)
        - price (double)
        - category (string)
 ```
 <br>

* ***Delete a product:***
```bash
[POST] /products/delete
    Params:
        - productId (integer)
```
<br>

* ***Update a product:***
```bash
[POST] /products/update
    Params:
        - productId
        - name
        - price
        - category
 ```

* Request rate limit is set to 5 seconds. 
