<?php
require_once "database.php";
$db = new Database();
$conn = $db->getConnection();

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data and sanitize inputs
    $contractId = mysqli_real_escape_string($conn, $_POST['contractId']);
    $serialNo = mysqli_real_escape_string($conn, $_POST['serialNo']);
    $facId = mysqli_real_escape_string($conn, $_POST['facId']);
    $fsrNo = mysqli_real_escape_string($conn, $_POST['fsrNo']);
    $serviceNote = mysqli_real_escape_string($conn, $_POST['serviceNote']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    $servicePersonId = mysqli_real_escape_string($conn, $_POST['servicePersonId']);
    $date = mysqli_real_escape_string($conn, $_POST['date']);
    
    // Update maintenance record
    $sql = "UPDATE maintainanceColumns 
            SET SerialNo = ?, 
                FacId = ?, 
                FSRno = ?, 
                intServiceNote = ?, 
                Status = ?, 
                ServicePersonId = ?, 
                Date = ? 
            WHERE ContractId = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sissssss", $serialNo, $facId, $fsrNo, $serviceNote, $status, $servicePersonId, $date, $contractId);
    
    if ($stmt->execute()) {
        // Redirect back to maintenance page with success message
        header("Location: maintenance_update.php?updated=1");
        exit();
    } else {
        // Redirect back with error message
        header("Location: maintenance_update.php?error=2");
        exit();
    }
} else {
    // If not POST request, redirect to maintenance page
    header("Location: maintenance_update.php");
    exit();
}
?>