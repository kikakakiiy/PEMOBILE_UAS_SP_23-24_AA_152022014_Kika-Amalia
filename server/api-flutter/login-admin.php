<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
    include 'config.php';

    $params = json_decode(trim(file_get_contents("php://input")), true) ? json_decode(trim(file_get_contents("php://input")), true) : $_POST;

    if (
        empty($params['email'])
    ) {
        $status = 400;
        $data = null;
    } else {
        $input = [
            "email" => $conn->real_escape_string($params['email']),
            "password" => $conn->real_escape_string($params['password']),
        ];

        $sql = "SELECT * FROM `login` WHERE email = '" . $input['email'] . "' AND password = '" . $input['password'] . "' AND role = 'admin' ";

        $result = $conn->query($sql);
        $userObj = [];
        while ($row = $result->fetch_assoc()) {
            $userObj = $row;
        }

        if (!empty($userObj)) {
            $sql_old_token = "DELETE FROM token WHERE idUser = '" . $userObj['id'] . "'";
            $remove_old_token = $conn->query($sql_old_token);

            $access_token = base64_encode(sha1(uniqid()) . md5(uniqid()));
            $refresh_token = base64_encode(sha1($userObj['username']) . md5($userObj['username']));

            $token_input = [
                "idUser" => $userObj['id'],
                "refreshToken" => $refresh_token,
                "token" => $access_token,
                "expired" => strtotime('+1 hour')
            ];
            $no = 1;
            $key = "";
            $total_string = "";
            $total_query = "";
            $values = [];
            foreach ($token_input as $k => $v) {
                if (count($token_input) == $no) {
                    $key .= '`' . $k . '` ';
                    $total_query .= "? ";
                } else {
                    $key .= '`' . $k . '`, ';
                    $total_query .= "?, ";
                }
                $total_string .= "s";
                $values[] = $v;
                $no++;
            }

            $sql = "INSERT INTO token (" . $key . ") VALUES (" . $total_query . ")";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($total_string, ...$values);
            $stmt->execute();

            $userObj['access_token'] = $access_token;
            $userObj['refresh_token'] = $refresh_token;
            $userObj['token_expired'] = 3600;

            $status = 200;
            $data = [
                "status" => "success",
                "message" => "Login success",
                "data" => $userObj
            ];
        } else {
            $status = 400;
            $data = [
                "status" => "failed",
                "message" => "Email or Password invalid",
                "data" => (object) []
            ];
        }
    }
} else {
    $data = null;
    $status = 405;
}

http_response_code($status);
echo json_encode($data);

include 'return.php';
