<?php

include 'config.php';

$params = $_REQUEST;
$page = $params['page'] ? (int) $conn->real_escape_string($params['page']) : 1;
$items_per_page = $params['items_per_page'] ? (int) $conn->real_escape_string($params['items_per_page']) : 100;
$where = " WHERE 1";

if ($params['id']) {
    $params['id'] = $conn->real_escape_string($params['id']);
    $where .= " AND id = " . (int) $params['id'];
}

$sql = "SELECT * FROM temu_janji";
$result = $conn->query($sql);
$tables = [];
while ($row = $result->fetch_assoc()) {
    $tables[] = $row;
}

$sql = "SELECT count(id) as total FROM temu_janji " . $where;
$result = $conn->query($sql);
$total = $result->fetch_assoc();

$data = $tables;

$status = 200;

http_response_code($status);
echo json_encode($data);

include 'return.php';
