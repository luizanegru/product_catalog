<?php
class UserController
{
    //objects
    private $user;
    private $authToken;

    public function __construct(User $user, Auth_Token $authToken)
    {
        $this->user = $user;
        $this->authToken = $authToken;
    }

    public function login()
    {
        $this->authToken->isRequestLimitExceeded();
        $this->authToken->logRequest(1);

        $result = $this->user->login();

        //get data from database
        $user_id = $result[0]['id'];
        $email = $result[0]['email'];
        $password = $result[0]['password'];

        //get the email and password from user
        $data = $_POST;
        //make sure email and password is not empty
        if (!empty(array_values($data)[0]) && !empty(array_values($data)[1])) {
            $this->user->email = array_values($data)[0];
            $this->user->password = array_values($data)[1];



            //checking if the data entered by the user is the same as in the database
            if ($this->user->email === $email && $this->user->password === $password) {
                $stmt = $this->authToken->verification();
                $num = $stmt->rowCount();

                //first login
                if ($num === 0) {
                    if ($this->authToken->create_token($user_id)) {
                        // set response code - 201 created
                        http_response_code(201);
                        echo json_encode(array(
                            "message" => "You are logged in.",
                            "token" => $this->authToken->getToken()
                        ));
                    }
                } else {
                    if ($this->authToken->update_token($user_id)) {
                        echo json_encode(array(
                            "message" => "You are logged in.",
                            "token" => $this->authToken->getToken()
                        ));
                    } else {
                        // set response code - 503 service unavailable
                        http_response_code(503);
                        echo json_encode(array("message" => "Unable to update token."));
                    }
                }
            }
        } else {
            // set response code - 400 bad request
            http_response_code(400);
            echo json_encode(array("message" => "Unable to login. Data is incomplete."));
        }
    }
}
