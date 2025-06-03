<?php
require_once 'database.php';

header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

$fsrId = isset($_GET['fsrId']) ? intval($_GET['fsrId']) : 0;

if ($fsrId <= 0) {
    echo json_encode(['error' => 'Invalid FSR ID']);
    exit;
}

// Use transaction to ensure data consistency
$conn->begin_transaction();

try {
    // Get FSR details
    $fsrSQL = "
        SELECT 
            fsr.*,
            f.FacName
        FROM fsr fsr
        JOIN factories f ON fsr.FacId = f.FacId
        WHERE fsr.FSRId = ?";
    
    $fsrStmt = $conn->prepare($fsrSQL);
    $fsrStmt->bind_param("i", $fsrId);
    $fsrStmt->execute();
    $fsrResult = $fsrStmt->get_result();
    $fsrDetails = $fsrResult->fetch_assoc();
    
    if (!$fsrDetails) {
        echo json_encode(['error' => 'FSR not found']);
        exit;
    }
    
    // Get service persons assigned to this FSR
    $personSQL = "
        SELECT 
            sp.ServicePersonId,
            sp.ServicePersonId as Name  -- Assuming ServicePersonId contains the name or can be joined with a persons table
        FROM fsr_service_persons sp
        WHERE sp.FSRId = ?";
    
    $personStmt = $conn->prepare($personSQL);
    $personStmt->bind_param("i", $fsrId);
    $personStmt->execute();
    $personResult = $personStmt->get_result();
    
    $servicePersons = [];
    while ($row = $personResult->fetch_assoc()) {
        $servicePersons[] = $row;
    }
    
    $fsrDetails['servicePersons'] = $servicePersons;
    
    $conn->commit();
    
    echo json_encode($fsrDetails);
    
    $fsrStmt->close();
    $personStmt->close();
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['error' => $e->getMessage()]);
}

$db->closeConnection();
?>