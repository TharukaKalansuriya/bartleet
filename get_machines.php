<?php
require_once "database.php";
$db = new Database();
$conn = $db->getConnection();

// Check if factory ID is provided
if (isset($_GET['facId'])) {
    $facId = mysqli_real_escape_string($conn, $_GET['facId']);
    
    // Fetch machines for the specified factory
    $sql = "SELECT SerialNo, Model FROM machines WHERE FacId = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $facId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $machines = array();
    while ($row = $result->fetch_assoc()) {
        $machines[] = $row;
    }
    
    // Return machines as JSON
    header('Content-Type: application/json');
    echo json_encode($machines);
} else {
    // Return error if factory ID is not provided
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'Factory ID is required']);
}
?>