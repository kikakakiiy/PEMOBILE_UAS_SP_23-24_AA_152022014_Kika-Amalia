<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
    include 'config.php';

    $params = json_decode(trim(file_get_contents("php://input")), true) ? json_decode(trim(file_get_contents("php://input")), true) : $_POST;

    if (
        empty($params['nama'])
    ) {
        $status = 400;
        $data = null;
    } else {
        $input = [
            "nama" => $conn->real_escape_string($params['nama']),
            "deskripsi" => $conn->real_escape_string($params['deskripsi']),
            "kategori" => $conn->real_escape_string($params['kategori']),
            "harga" => $conn->real_escape_string($params['harga']),
            "status" => $conn->real_escape_string($params['status']),
        ];

        $userObj = [];

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

            $sql = "INSERT INTO `produk` (" . $key . ") VALUES (" . $total_query . ")";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($total_string, ...$values);
            if ($stmt->execute()) {
                $sql = "SELECT * FROM `produk` ORDER BY id DESC";
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
