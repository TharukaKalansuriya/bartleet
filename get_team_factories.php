<?php
// get_team_factories.php
require_once 'database.php';

header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$teamId = isset($_GET['teamId']) ? $_GET['teamId'] : '';

if (empty($teamId)) {
    echo json_encode([]);
    exit;
}

// Get factories for this team
$sql = "SELECT FacId, FacName, Location FROM factories WHERE teamId = ? ORDER BY FacName";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $teamId);
$stmt->execute();
$result = $stmt->get_result();

$factories = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $factories[] = $row;
    }
}

echo json_encode($factories);
$stmt->close();
$db->closeConnection();
?>