<?php
class Auth_Token
{
    //database connection
    private $conn;
    //table name
    private $table_name = "auth_token";


    //properties
    public $id;
    public $user_id;
    public $token;
    public $expiration;
    public $last_request_at;

    //constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function verification()
    {
        $query = "SELECT *
                  FROM " . $this->table_name . " ";


        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function generateToken()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < 128; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    //for the fist login
    public function create_token($user_id)
    {
        $query = "INSERT INTO
                " . $this->table_name . "
            (user_id, token, expiration, last_request_at)
            VALUES(:user_id, :token, :expiration, :last_request_at)";

        $stmt = $this->conn->prepare($query);

        //set data
        $this->user_id = $user_id;
        $this->token = $this->generateToken();
        $now = date("Y-m-d H:i:s");
        //the token is valid for 24 hours
        $this->expiration = date("Y-m-d H:i:s", strtotime('+24 hours', strtotime($now)));
        $this->last_request_at = date("Y-m-d H:i:s");

        $this->user_id = htmlspecialchars(strip_tags($this->user_id));
        $this->token = htmlspecialchars(strip_tags($this->token));
        $this->expiration = htmlspecialchars(strip_tags($this->expiration));
        $this->last_request_at = htmlspecialchars(strip_tags($this->last_request_at));

        //prepare the statement
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->bindParam(":token", $this->token);
        $stmt->bindParam(":expiration", $this->expiration);
        $stmt->bindParam(":last_request_at", $this->last_request_at);

        if ($stmt->execute()) {

            return true;
        }

        return false;
    }

    public function update_token($user_id)
    {
        // update query
        $query = "UPDATE
                    " . $this->table_name . "
                 SET
                    token=:token,
                    expiration=:expiration,
                    last_request_at=:last_request_at
                WHERE
                    user_id = " . $user_id . "";

        // prepare query statement
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->last_request_at = date('Y-m-d H:i:s');
        $now = date("Y-m-d H:i:s");
        //the token is valid for 24 hours
        $this->expiration = date("Y-m-d H:i:s", strtotime('+24 hours', strtotime($now)));
        $this->token = $this->generateToken();

        $this->last_request_at = htmlspecialchars(strip_tags($this->last_request_at));
        $this->expiration = htmlspecialchars(strip_tags($this->expiration));
        $this->token = htmlspecialchars(strip_tags($this->token));

        //prepare the statement
        $stmt->bindParam(':last_request_at', $this->last_request_at);
        $stmt->bindParam(':expiration', $this->expiration);
        $stmt->bindParam(':token', $this->token);

        // execute the query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function get_token()
    {
        $query = "SELECT auth.token
                  FROM " . $this->table_name . " auth";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function getLastRequest()
    {
        $query = "SELECT auth.last_request_at
                  FROM " . $this->table_name . " auth";

        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt;
    }

    public function logRequest($user_id)
    {
        $query = "UPDATE
                " . $this->table_name . "
                 SET
                    last_request_at=:last_request_at
                 WHERE
                    user_id = " . $user_id . "";

        // prepare query statement
        $stmt = $this->conn->prepare($query);
        //get date from the current query
        $this->last_request_at = date('Y-m-d H:i:s');

        $this->last_request_at = htmlspecialchars(strip_tags($this->last_request_at));
        //prepare the statement
        $stmt->bindParam(':last_request_at', $this->last_request_at);

        // execute the query
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }

    public function isRequestLimitExceeded()
    {
        $currentDate = date('Y-m-d H:i:s');

        //the date of the user's last request
        $stmt = $this->getLastRequest();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $last_request =  array_values($result)[0];

        //the difference between the last and the current request
        $seconds = strtotime($currentDate) - strtotime($last_request);

        //if the time between the two is less than 30 s then the request cannot be made
        if ($seconds < 30) {
            http_response_code(401);
            echo json_encode(array("message" => "Number of requests is exceeded"));
            exit;
        }
    }
}
