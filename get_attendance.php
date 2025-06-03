<?php
require_once 'database.php';

$db = new Database();
$conn = $db->getConnection();

$personId = $_GET['id'] ?? '';
$fromDate = $_GET['from'] ?? '';
$toDate = $_GET['to'] ?? '';

if (!$personId || !$fromDate || !$toDate) {
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

// Updated SQL query to handle three attendance states
$sql = "SELECT 
            COUNT(*) AS total_days,
            SUM(CASE WHEN attend = '1' AND NotAttend = '0' THEN 1 ELSE 0 END) AS working,
            SUM(CASE WHEN attend = '0' AND NotAttend = '1' THEN 1 ELSE 0 END) AS leaves,
            SUM(CASE WHEN attend = '0' AND NotAttend = '0' THEN 1 ELSE 0 END) AS oncall
        FROM attendance
        WHERE ServicePersonId = ?
          AND Date BETWEEN ? AND ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $personId, $fromDate, $toDate);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

// Return all three categories as expected by the UI
echo json_encode([
    'working' => (int)$data['working'],
    'leaves' => (int)$data['leaves'],
    'oncall' => (int)$data['oncall']
]);
?>