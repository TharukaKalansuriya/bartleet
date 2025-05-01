<?php
require_once 'database.php';

// Validate input
$fsrNo = isset($_GET['fsrNo']) ? $_GET['fsrNo'] : '';
if (empty($fsrNo)) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid FSR number']);
    exit;
}

$db = new Database();
$conn = $db->getConnection();

// Get main FSR details
$stmt = $conn->prepare("SELECT * FROM fsr WHERE FSRNo = ?");
$stmt->bind_param("s", $fsrNo);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'FSR not found']);
    exit;
}

$fsrData = $result->fetch_assoc();
$stmt->close();

// Get the FSRId
$fsrId = $fsrData['FSRId'];

// Get service persons
$servicePersonsQuery = $conn->prepare("
    SELECT GROUP_CONCAT(ServicePersonId) as ServicePersonIds 
    FROM fsr_service_persons 
    WHERE FSRId = ?
");
$servicePersonsQuery->bind_param("i", $fsrId);
$servicePersonsQuery->execute();
$servicePersonsResult = $servicePersonsQuery->get_result();
$servicePersonsRow = $servicePersonsResult->fetch_assoc();
$fsrData['ServicePersonIds'] = $servicePersonsRow['ServicePersonIds'] ?? '';
$servicePersonsQuery->close();

// Get replacements
$replacementsQuery = $conn->prepare("
    SELECT Description, Date, Remarks 
    FROM fsr_replacements 
    WHERE FSRId = ?
");
$replacementsQuery->bind_param("i", $fsrId);
$replacementsQuery->execute();
$replacementsResult = $replacementsQuery->get_result();

$replacements = [];
while ($row = $replacementsResult->fetch_assoc()) {
    $replacements[] = $row;
}
$fsrData['replacements'] = $replacements;
$replacementsQuery->close();

$db->closeConnection();

// Return JSON response
header('Content-Type: application/json');
echo json_encode($fsrData);
?>