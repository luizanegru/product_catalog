<?php
class Product
{
    //database connection
    private $conn;
    //table name
    private $tableName = "products";

    //properties
    public $id;
    public $name;
    public $price;
    public $category;
    public $create_date;
    public $update_date;

    //constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }

    //get products from data base
    public function read()
    {
        $query = "SELECT p.id, p.name, p.price, p.category, p.create_date, p.update_date
                  FROM " . $this->tableName . " p 
                  ORDER BY p.update_date";

        $stmt = $this->conn->prepare($query);
        //execute query
        $stmt->execute();

        return $stmt;
    }

    //function for creat product
    public function create()
    {

        $query = "INSERT INTO
                " . $this->tableName . "
            (name, price, category, create_date, update_date)
            VALUES(:name, :price, :category, :create_date, :update_date)";

        $stmt = $this->conn->prepare($query);

        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->create_date = htmlspecialchars(strip_tags($this->create_date));
        $this->update_date = htmlspecialchars(strip_tags($this->update_date));

        //prepare the statement
        $stmt->bindParam(":name", $this->name);
        $stmt->bindParam(":price", $this->price);
        $stmt->bindParam(":category", $this->category);
        $stmt->bindParam(":create_date", $this->create_date);
        $stmt->bindParam(":update_date", $this->update_date);

        if ($stmt->execute()) {

            return true;
        }
        return false;
    }

    //update product
    public function update()
    {
        // update query
        $query = "UPDATE
                " . $this->tableName . "
                SET
                    name= :name, 
                    price=:price, 
                    category=:category,  
                    update_date=:update_date
                 WHERE
                    id = :id";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->name = htmlspecialchars(strip_tags($this->name));
        $this->price = htmlspecialchars(strip_tags($this->price));
        $this->category = htmlspecialchars(strip_tags($this->category));
        $this->update_date = htmlspecialchars(strip_tags($this->update_date));
        $this->id = htmlspecialchars(strip_tags($this->id));


        // prepare the statement
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':price', $this->price);
        $stmt->bindParam(':category', $this->category);
        $stmt->bindParam(':update_date', $this->update_date);
        $stmt->bindParam(':id', $this->id);


        // execute the query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    //delete a product by id
    public function delete()
    {
        $query = "DELETE FROM " . $this->tableName . " WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->id = htmlspecialchars(strip_tags($this->id));
        //prepare the statement
        $stmt->bindParam(':id', $this->id);

        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
