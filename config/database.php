<?php
class Database
{

    private $host = "127.0.0.1";
    private $db_name = "test_2checkout";
    private $username = "root";
    private $password = "root";
    public $conn;

    //conection to database
    public function connection()
    {
        $this->conn = null;

        try {
            $this->conn = new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db_name . ';charset=utf8', $this->username, $this->password);
        } catch (PDOException $exception) {
            echo "Connection error:" . $exception->getMessage();
        }

        return $this->conn;
    }
}
