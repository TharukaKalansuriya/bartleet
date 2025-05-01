<?php
// get_team_serials.php
require_once 'database.php';

header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$teamId = isset($_GET['teamId']) ? $_GET['teamId'] : '';

if (empty($teamId)) {
    echo json_encode([]);
    exit;
}

// Get serial numbers for factories assigned to this team
$sql = "SELECT DISTINCT m.SerialNo 
        FROM maintainance m
        JOIN factories f ON m.FacId = f.FacId
        WHERE f.teamId = ?
        ORDER BY m.SerialNo";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $teamId);
$stmt->execute();
$result = $stmt->get_result();

$serials = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $serials[] = $row;
    }
}

echo json_encode($serials);
$stmt->close();
$db->closeConnection();
?>