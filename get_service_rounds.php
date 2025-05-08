<?php
// get_service_rounds.php
require_once 'database.php';

header('Content-Type: application/json');

$db = new Database();
$conn = $db->getConnection();

// Add logging for debugging
error_log("Service round request received");

$serialNo = isset($_GET['serialNo']) ? $_GET['serialNo'] : '';
$facId = isset($_GET['facId']) ? intval($_GET['facId']) : 0;
$teamId = isset($_GET['teamId']) ? $_GET['teamId'] : '';

error_log("Params: serialNo=$serialNo, facId=$facId, teamId=$teamId");

if (empty($serialNo)) {
    echo json_encode(['error' => 'Serial number is required']);
    exit;
}

// Use transaction to ensure data consistency
$conn->begin_transaction();

try {
    // Build query conditions based on inputs
    $whereConditions = ["fsr.SerialNo = ?"];
    $paramTypes = "s";
    $paramValues = [$serialNo];
    
    if ($facId > 0) {
        $whereConditions[] = "fsr.FacId = ?";
        $paramTypes .= "i";
        $paramValues[] = $facId;
    } elseif (!empty($teamId)) {
        $whereConditions[] = "f.teamId = ?";
        $paramTypes .= "s";
        $paramValues[] = $teamId;
    }
    
    $whereClause = implode(" AND ", $whereConditions);
    error_log("WHERE clause: $whereClause");
    
    $summarySQL = "
        SELECT 
            fsr.SerialNo,
            f.FacName,
            COUNT(DISTINCT fsr.FSRId) as RoundCount,
            MAX(m.Date) as LastServiceDate,
            MAX(m.Status) as Status
        FROM fsr fsr
        JOIN factories f ON fsr.FacId = f.FacId
        LEFT JOIN maintainance m ON fsr.SerialNo = m.SerialNo AND fsr.FacId = m.FacId
        WHERE $whereClause
        GROUP BY fsr.SerialNo, f.FacName";
    
    error_log("Summary SQL: $summarySQL");
    
    $summaryStmt = $conn->prepare($summarySQL);
    $summaryStmt->bind_param($paramTypes, ...$paramValues);
    $summaryStmt->execute();
    $summaryResult = $summaryStmt->get_result();
    $summary = $summaryResult->fetch_assoc();
    
    error_log("Summary result: " . json_encode($summary));
    
    // If no summary is found, check if the serial exists at all (without other filters)
    if (!$summary) {
        $checkSerialSQL = "SELECT SerialNo FROM fsr WHERE SerialNo = ?";
        $checkStmt = $conn->prepare($checkSerialSQL);
        $checkStmt->bind_param("s", $serialNo);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            error_log("Serial exists but no records match the filters");
            // Serial exists but doesn't match other filters
            echo json_encode([
                'error' => 'Serial exists but no records match current filters',
                'debug' => [
                    'serialExists' => true,
                    'facId' => $facId,
                    'teamId' => $teamId
                ]
            ]);
            $conn->commit();
            exit;
        }
    }
    
    // Get FSR details
    $fsrSQL = "
        SELECT 
            fsr.FSRId,
            fsr.FSRNo,
            fsr.WorkDescription,
            m.Date as ServiceDate
        FROM fsr fsr
        JOIN factories f ON fsr.FacId = f.FacId
        LEFT JOIN maintainance m ON fsr.SerialNo = m.SerialNo AND fsr.FacId = m.FacId
        WHERE $whereClause
        ORDER BY fsr.FSRId DESC";
    
    error_log("FSR SQL: $fsrSQL");
    
    $fsrStmt = $conn->prepare($fsrSQL);
    $fsrStmt->bind_param($paramTypes, ...$paramValues);
    $fsrStmt->execute();
    $fsrResult = $fsrStmt->get_result();
    
    $fsrs = [];
    while ($row = $fsrResult->fetch_assoc()) {
        $fsrs[] = $row;
    }
    
    error_log("FSRs found: " . count($fsrs));
    
    $conn->commit();
    
    echo json_encode([
        'summary' => $summary,
        'fsrs' => $fsrs,
        'debug' => [
            'serialNo' => $serialNo,
            'facId' => $facId,
            'teamId' => $teamId,
            'whereClause' => $whereClause,
            'recordsFound' => !empty($summary) && !empty($fsrs)
        ]
    ]);
    
    $summaryStmt->close();
    $fsrStmt->close();
} catch (Exception $e) {
    $conn->rollback();
    error_log("Error in get_service_rounds: " . $e->getMessage());
    echo json_encode(['error' => $e->getMessage()]);
}

$db->closeConnection();
?>