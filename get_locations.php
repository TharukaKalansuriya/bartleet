<?php
header('Content-Type: application/json');

// Database connection - adjust these parameters according to your setup
try {
    // Replace with your database connection details
    $pdo = new PDO("mysql:host=localhost;dbname=bartleet", "root", "1234");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Query to get locations with factory count
    $stmt = $pdo->prepare("
        SELECT 
            Location,
            COUNT(*) as FactoryCount
        FROM factories 
        WHERE Location IS NOT NULL AND Location != ''
        GROUP BY Location 
        ORDER BY Location ASC
    ");
    
    $stmt->execute();
    $locations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($locations);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>