<?php
// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    header("Access-Control-Max-Age: 3600");
    exit(0);
}

// Set response headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../config/database.php';

try {
    // Handle request method
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception('Only GET method is allowed');
    }

    // Validate and sanitize input
    $dbName = filter_input(INPUT_GET, 'dbName', FILTER_UNSAFE_RAW);
    $dbName = htmlspecialchars(trim($dbName), ENT_QUOTES, 'UTF-8');

    // Validate database name
    if (empty($dbName)) {
        throw new Exception('Database name is required');
    }

    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $dbName)) {
        throw new Exception('Invalid database name format');
    }
    
    // Get database connection
    $database = new Database($dbName);
    $db = $database->getConnection();

    // Prepare query
    $query = "SELECT 
        pid,
        project_title,
        objective,
        domain,
        programme_type,
        dataset_link,
        technology,
        description,
        tools,
        learning_objective
    FROM problem_statements
    ORDER BY pid";

    $stmt = $db->prepare($query);
    $stmt->execute();
    
    // Handle response
    if($stmt->rowCount() > 0) {
        $problemStatements = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Format the response
        $response = [
            "status" => "success",
            "data" => $problemStatements,
            "count" => count($problemStatements)
        ];
        
        http_response_code(200);
        echo json_encode($response);
    } else {
        http_response_code(200);  // Changed from 404 to 200 for empty results
        echo json_encode([
            "status" => "success",
            "data" => [],
            "count" => 0,
            "message" => "No problem statements found."
        ]);
    }
} catch(PDOException $e) {
    error_log($e->getMessage());  // Log the actual error
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Database error occurred",
        "code" => "DB_ERROR"
    ]);
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage(),
        "code" => "INPUT_ERROR"
    ]);
}
