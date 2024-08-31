<?php

if ($_SERVER["REQUEST_METHOD"] == "POST" || $_SERVER["REQUEST_METHOD"] == "GET") {
    include 'config.php';

    $params = json_decode(trim(file_get_contents("php://input")), true) ? json_decode(trim(file_get_contents("php://input")), true) : $_POST;

    if (
        empty($params['name'])
    ) {
        $status = 400;
        $data = null;
    } else {
        $input = [
            "nama" => $conn->real_escape_string($params['name']),
            "phone_number" => $conn->real_escape_string($params['phone']),
            "deskripsi" => $conn->real_escape_string($params['consultation']),
            "tanggal" => $conn->real_escape_string($params['date']),
            "jam" => $conn->real_escape_string($params['time']),
            "waktu" => $conn->real_escape_string($params['waktu']),
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

            $sql = "INSERT INTO `temu_janji` (" . $key . ") VALUES (" . $total_query . ")";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param($total_string, ...$values);
            if ($stmt->execute()) {
                $sql = "SELECT * FROM `temu_janji` ORDER BY id DESC";
                $result = $conn->query($sql);
                $userObj = (object) [];
                while ($row = $result->fetch_assoc()) {
                    $userObj = $row;
                }

                $status = 200;
                $data = [
                    "status" => "success",
                    "message" => "Berhasil",
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
                "message" => "Gagal",
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
