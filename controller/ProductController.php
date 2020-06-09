<?php

class ProductController
{
    //objects
    private $product;
    private $auth_Token;

    private $headers;

    public function __construct(Product $product, Auth_Token $auth_Token)
    {
        $this->product = $product;
        $this->auth_Token = $auth_Token;
    }

    //the method that verifies that it is authenticated
    public function isAuthenticated()
    {
        //get headers from postMan
        $this->headers = getallheaders();
        //cut the first word from the header, ex:Bearer, the space before and after the token
        $token = !empty($this->headers['Authorization']) ? trim(substr($this->headers['Authorization'], 6)) : null;

        //request in the database to take the token
        $stmt = $this->auth_Token->get_token();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $token_database =  array_values($result)[0];

        //the token in the header must exist and be the same as in the base date in order to make the request
        if (!$token || $token !== $token_database) {
            http_response_code(401);
            echo json_encode(array("message" => "Unauthorized"));
            exit;
        }
    }

    //create a product
    public function create()
    {
        $this->isAuthenticated();
        $this->auth_Token->isRequestLimitExceeded();
        $this->auth_Token->logRequest(1);

        //get the data from user
        $data = $_POST;
        //make sure name, price and category is not empty
        if (!empty(array_values($data)[0]) && !empty(array_values($data)[1]) && !empty(array_values($data)[2])) {
            $this->product->name = array_values($data)[0];
            $this->product->price = array_values($data)[1];
            $this->product->category = array_values($data)[2];
            $this->product->create_date = date('Y-m-d H:i:s');
            $this->product->update_date = date('Y-m-d H:i:s');

            if ($this->product->create()) {
                // set response code - 201 created
                http_response_code(201);
                echo json_encode(array("message" => "Product was created."));
            }

            // if unable to create the product
            else {
                // set response code - 503 service unavailable
                http_response_code(503);
                echo json_encode(array("message" => "Unable to create product."));
            }
        }

        //data is incomplete
        else {
            // set response code - 400 bad request
            http_response_code(400);
            echo json_encode(array("message" => "Unable to create product. Data is incomplete."));
        }
    }

    //get ol products
    public function read()
    {
        $this->isAuthenticated();
        $this->auth_Token->isRequestLimitExceeded();
        $this->auth_Token->logRequest(1);

        $stmt = $this->product->read();
        $num = $stmt->rowCount();

        //check if there are products in the database
        if ($num > 0) {
            $products_arr = array();
            $products_arr["products"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                //returns an array indexed by column name
                extract($row);

                $product_item = array(
                    "id" => $id,
                    "name" => $name,
                    "price" => $price,
                    "category" => $category,
                    "create_date" => $create_date,
                    "update_date" => $update_date
                );

                array_push($products_arr["products"], $product_item);
            }
            //200 - OK
            http_response_code(200);
            //products in json format
            echo json_encode($products_arr);
        } else {
            // set response code - 404 Not found
            http_response_code(404);

            //no products found
            echo json_encode(
                array("message" => "No products found.")
            );
        }
    }

    //update a product
    public function update()
    {
        $this->isAuthenticated();
        $this->auth_Token->isRequestLimitExceeded();
        $this->auth_Token->logRequest(1);

        //get the data from user
        $data = $_POST;
        //set the data from user
        $this->product->id = array_values($data)[0];
        $this->product->name = array_values($data)[1];
        $this->product->price = array_values($data)[2];
        $this->product->category = array_values($data)[3];
        $this->product->update_date = date('Y-m-d H:i:s');

        var_dump($this->product->update_date);
        if ($this->product->update()) {
            // set response code - 200 ok
            http_response_code(200);
            echo json_encode(array("message" => "Product was updated."));
        }

        // if unable to update the product
        else {
            // set response code - 503 service unavailable
            http_response_code(503);
            echo json_encode(array("message" => "Unable to update product."));
        }
    }

    //delete a product by id
    public function delete()
    {
        $this->isAuthenticated();
        $this->auth_Token->isRequestLimitExceeded();
        $this->auth_Token->logRequest(1);

        //get the id from the user
        $data = $_POST;
        //set the id
        $this->product->id = array_values($data)[0];

        if ($this->product->delete()) {
            // set response code - 200 ok
            http_response_code(200);
            echo json_encode(array("message" => "Product was deleted."));
        }

        // if unable to update the product
        else {

            // set response code - 503 service unavailable
            http_response_code(503);
            echo json_encode(array("message" => "Unable to delete."));
        }
    }
}
