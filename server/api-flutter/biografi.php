<?php

include 'config.php';

$sql = "SELECT * FROM biografi_dokter";

$result = $conn->query($sql);
$tables = [];
while ($row = $result->fetch_assoc()) {
    $tables[] = $row;
}

$data = $tables[0];
$status = 200;

http_response_code($status);
echo json_encode($data);

include 'return.php';
