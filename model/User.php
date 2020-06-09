<?php
class User
{
    //database connection
    private $conn;
    //table name
    private $tableName = "users";

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
                  FROM " . $this->tableName . " u";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($results)) {
            return $results;
        }
        return null;
    }
}
