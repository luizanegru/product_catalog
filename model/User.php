<?php
class User
{
    //database connection
    private $conn;
    //table name
    private $table_name = "users";

    //constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }

    //properties user
    public $id;
    public $first_name;
    public $last_name;
    public $password;
    public $email;

    public function login()
    {
        $query = "SELECT u.email, u.password, u.id
                  FROM " . $this->table_name . " u";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}
