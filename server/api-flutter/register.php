<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
    include 'config.php';

    $params = json_decode(trim(file_get_contents("php://input")), true) ? json_decode(trim(file_get_contents("php://input")), true) : $_POST;

    if (
        empty($params['username']) ||
        empty($params['name']) ||
        empty($params['email']) ||
        empty($params['password']) ||
        empty($params['phone_number'])
    ) {
        $status = 400;
        $data = null;
    } else {
        $input = [
            "username" => $conn->real_escape_string($params['username']),
            "nama_lengkap" => $conn->real_escape_string($params['name']),
            "email" => $conn->real_escape_string($params['email']),
            "password" => $conn->real_escape_string($params['password']),
            "phone_number" => $conn->real_escape_string($params['phone_number']),
        ];

        $sql = "SELECT * FROM `login` WHERE email = '" . $input['email'] . "' ";
        $result = $conn->query($sql);
        $userObj = [];
        while ($row = $result->fetch_assoc()) {
            $userObj = $row;
        }

        if (empty($userObj)) {
            $no = 1;
            $key = "";
            $total_string = "";
            $total_query = "";
            $values = [];
            foreach ($input as $k => $v) {
                if (count($input) == $no) {
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

            $sql = "INSERT INTO `login` (" . $key . ") VALUES (" . $total_query . ")";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($total_string, ...$values);
            if ($stmt->execute()) {
                $sql = "SELECT * FROM `login` WHERE email = '" . $input['email'] . "'";
                $result = $conn->query($sql);
                $userObj = (object) [];
                while ($row = $result->fetch_assoc()) {
                    $userObj = $row;
                }

                $status = 200;
                $data = [
                    "status" => "success",
                    "message" => "Data successfully registered",
                    "data" => $userObj
                ];
            } else {
                $data = null;
                $status = 500;
            }
        } else {
            $status = 500;
            $data = [
                "status" => "failed",
                "message" => "Email is already exists",
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
