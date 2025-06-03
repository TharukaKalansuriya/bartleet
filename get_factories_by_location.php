<?php
header('Content-Type: application/json');

// Check if location parameter is provided
if (!isset($_GET['location']) || empty($_GET['location'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Location parameter is required']);
    exit();
}

$location = $_GET['location'];

try {
    // Separate Database connection
    $pdo = new PDO("mysql:host=localhost;dbname=bartleet", "root", "1234");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query to get factories by location
    $stmt = $pdo->prepare("
        SELECT 
            FacId,
            FacName,
            Location,
            teamId
        FROM factories 
        WHERE Location = :location
        ORDER BY FacName ASC
    ");
    
    $stmt->bindParam(':location', $location, PDO::PARAM_STR);
    $stmt->execute();
    $factories = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($factories);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>