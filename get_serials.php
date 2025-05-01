<?php
// get_serials.php - Enhanced with debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';

// Log incoming request
$logFile = 'serials_debug.txt';
file_put_contents($logFile, date('Y-m-d H:i:s') . " - Request params: " . print_r($_GET, true) . "\n", FILE_APPEND);

header('Content-Type: application/json');

try {
    $db = new Database();
    $conn = $db->getConnection();
    
    if (!$conn) {
        throw new Exception("Database connection failed");
    }
    
    $facId = isset($_GET['facId']) ? intval($_GET['facId']) : 0;
    
    if ($facId <= 0) {
        echo json_encode([]);
        file_put_contents($logFile, date('Y-m-d H:i:s') . " - Invalid facId: $facId\n", FILE_APPEND);
        exit;
    }
    
    // Get unique serial numbers for this factory from the FSRId table
    $sql = "SELECT DISTINCT SerialNo FROM fsr WHERE FacId = ? ORDER BY SerialNo";
    
    // Log the SQL query
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - SQL: $sql with facId=$facId\n", FILE_APPEND);
    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("SQL preparation failed: " . $conn->error);
    }
    
    $stmt->bind_param("i", $facId);
    $result = $stmt->execute();
    
    if (!$result) {
        throw new Exception("Query execution failed: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    $serials = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $serials[] = $row;
        }
    }
    
    // Log the result count
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - Found " . count($serials) . " serial numbers\n", FILE_APPEND);
    
    echo json_encode($serials);
    $stmt->close();
    $db->closeConnection();
    
} catch (Exception $e) {
    // Log the error
    file_put_contents($logFile, date('Y-m-d H:i:s') . " - ERROR: " . $e->getMessage() . "\n", FILE_APPEND);
    
    // Return error as JSON
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>