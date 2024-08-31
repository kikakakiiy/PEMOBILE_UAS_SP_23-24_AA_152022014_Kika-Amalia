<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
    include 'config.php';

    $params = json_decode(trim(file_get_contents("php://input")), true) ? json_decode(trim(file_get_contents("php://input")), true) : $_POST;

    if (
        empty($params['id_user']) ||
        empty($params['nama']) ||
        empty($params['email']) ||
        empty($params['alamat']) ||
        empty($params['no_handphone'])
    ) {
        $status = 400;
        $data = null;
    } else {
        $input = [
            "id_user" => $conn->real_escape_string($params['id_user']),
            "nama" => $conn->real_escape_string($params['nama']),
            "email" => $conn->real_escape_string($params['email']),
            "alamat" => $conn->real_escape_string($params['alamat']),
            "no_handphone" => $conn->real_escape_string($params['no_handphone']),
            "products" => $params['products'],
            "shipping_cost" => "0",
            "total" => $params['total'],
            "tanggal" => date('Y-m-d'),
            "status" => "Menunggu Pembayaran"
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

            $sql = "INSERT INTO `transaksi` (" . $key . ") VALUES (" . $total_query . ")";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($total_string, ...$values);
            if ($stmt->execute()) {
                $sql = "SELECT * FROM `transaksi` ORDER BY id DESC";
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
        }
    }
} else {
    $data = null;
    $status = 405;
}

http_response_code($status);
echo json_encode($data);

include 'return.php';
