<?php
class UserController
{
    //objects
    private $user;
    private $auth_Token;

    public function __construct(User $user, Auth_Token $auth_Token)
    {
        $this->user = $user;
        $this->auth_Token = $auth_Token;
    }

    public function login()
    {
        $this->auth_Token->isRequestLimitExceeded();
        $this->auth_Token->logRequest(1);

        $stmt = $this->user->login();

        //get the email and password from user
        $data = $_POST;
        //make sure email and password is not empty
        if (!empty(array_values($data)[0]) && !empty(array_values($data)[1])) {
            $this->user->email = array_values($data)[0];
            $this->user->password = array_values($data)[1];

            //get data from the data base
            $data_user = $stmt->fetch(PDO::FETCH_ASSOC);
            $user_id = array_values($data_user)[2];

            //checking if the data entered by the user is the same as in the database
            if ($this->user->email === array_values($data_user)[0] && $this->user->password === array_values($data_user)[1]) {
                $stmt = $this->auth_Token->verification();
                $num = $stmt->rowCount();

                //first login
                if ($num === 0) {
                    if ($this->auth_Token->create_token($user_id)) {
                        // set response code - 201 created
                        http_response_code(201);
                        echo json_encode(array("message" => "You are login."));
                    }
                } else {

                    if ($this->auth_Token->update_token($user_id)) {
                        // set response code - 200 ok
                        http_response_code(200);
                        echo json_encode(array("message" => "You are login."));
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
