<?php
// get_factories.php
require_once 'database.php';

header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$sql = "SELECT FacId, FacName, Location FROM factories ORDER BY FacName";
$result = $conn->query($sql);

$factories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $factories[] = $row;
    }
}

echo json_encode($factories);
$db->closeConnection();
?>