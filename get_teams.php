<?php
// get_teams.php
require_once 'database.php';

header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

// Since your database doesn't have a separate teams table,
// we'll extract unique team IDs from the factories table
$sql = "SELECT DISTINCT teamId as teamId, teamId as teamName FROM factories WHERE teamId IS NOT NULL ORDER BY teamId";
$result = $conn->query($sql);

$teams = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $teams[] = $row;
    }
}

echo json_encode($teams);
$db->closeConnection();
?>