<?php
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

$query = "SELECT ServicePersonId AS ID, NAME FROM members";
$result = $conn->query($query);

$employees = [];
while ($row = $result->fetch_assoc()) {
    $employees[] = $row;
}

header('Content-Type: application/json');
echo json_encode($employees);
?>
