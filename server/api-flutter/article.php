<?php

include 'config.php';

$params = $_REQUEST;

if ($params['kategori']) {
    $sql = "SELECT * FROM artikel WHERE kategori = '" . $params['kategori'] . "'";
} elseif ($params['id']) {
    $sql = "SELECT * FROM artikel WHERE id = '" . $params['id'] . "'";
} else {
    $sql = "SELECT * FROM artikel ORDER BY id DESC LIMIT 5";
}

$result = $conn->query($sql);
$tables = [];
while ($row = $result->fetch_assoc()) {
    $row['thumbnail'] = 'http://localhost/api-flutter/uploads/' . $row['thumbnail'];
    $tables[] = $row;
}

$data = $tables;
$status = 200;

http_response_code($status);
echo json_encode($data);

include 'return.php';
