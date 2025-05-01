<?php
require_once "database.php";
$db = new Database();
$conn = $db->getConnection();

// Check if contract ID is provided
if (isset($_GET['contractId'])) {
    $contractId = mysqli_real_escape_string($conn, $_GET['contractId']);
    
    // Fetch maintenance record by contract ID
    $sql = "SELECT * FROM maintainanceColumns WHERE ContractId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $contractId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Return maintenance record as JSON
        $record = $result->fetch_assoc();
        header('Content-Type: application/json');
        echo json_encode($record);
    } else {
        // Return error if record not found
        header('HTTP/1.1 404 Not Found');
        echo json_encode(['error' => 'Maintenance record not found']);
    }
} else {
    // Return error if contract ID is not provided
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Contract ID is required']);
}
?>