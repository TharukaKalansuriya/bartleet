<?php
require_once "database.php";
$db = new Database();
$conn = $db->getConnection();

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $serialNo = mysqli_real_escape_string($conn, $_POST['serialNo']);
    $facId = mysqli_real_escape_string($conn, $_POST['facId']);
    $fsrNo = mysqli_real_escape_string($conn, $_POST['fsrNo']);
    $serviceNote = mysqli_real_escape_string($conn, $_POST['serviceNote']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $servicePersonId = mysqli_real_escape_string($conn, $_POST['servicePersonId']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    

    $contractId = "MAINT-" . date("Ymd") . "-" . uniqid();
    
    // Insert new maintenance record
    $sql = "INSERT INTO maintainanceColumns 
            (ContractId, FacId, SerialNo, FSRno, intServiceNote, Status, ServicePersonId, Date, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissssss", $contractId, $facId, $serialNo, $fsrNo, $serviceNote, $status, $servicePersonId, $date);
    
    if ($stmt->execute()) {
        // Redirect back to maintenance page with success message
        header("Location: maintenance_update.php?success=1");
        exit();
    } else {
        // Redirect back with error message
        header("Location: maintenance_update.php?error=1");
        exit();
    }
} else {
    
    header("Location: maintenance_update.php");
    exit();
}
?>