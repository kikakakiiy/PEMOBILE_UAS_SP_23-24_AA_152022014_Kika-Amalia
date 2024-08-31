<?php

include 'config.php';

$id = (int) $_REQUEST['id'];
if (empty($id)) {
    $data = null;
    $status = 400;
} else {
    $sql = "SELECT * FROM transaksi WHERE id = " . $id;
    $result = $conn->query($sql);
    $payTableObj = [];
    while ($row = $result->fetch_assoc()) {
        $payTableObj = $row;
    }

    if (empty($payTableObj)) {
        $status = 500;
        $data = [
            "status" => "failed",
            "message" => "Data not found",
            "data" => (object) []
        ];
    } else {
        $sql = "DELETE FROM transaksi WHERE id = '" . $id . "'";
        $result = $conn->query($sql);

        if ($result) {
            $status = 200;
            $data = [
                "status" => "success",
                "message" => "Data successfully deleted",
                "data" => $payTableObj
            ];
        } else {
            $data = null;
            $status = 500;
        }
    }
}

http_response_code($status);
echo json_encode($data);

include 'return.php';
