<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

include_once './config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    $dbName = filter_input(INPUT_GET, 'dbName', FILTER_UNSAFE_RAW) ?? 'ibmprojects';
    $dbName = htmlspecialchars(trim($dbName), ENT_QUOTES, 'UTF-8');
    if (!preg_match('/^[a-zA-Z0-9_-]+$/', $dbName)) {
        throw new Exception('Invalid database name format');
    }
    
    $database = new Database($dbName);
    $db = $database->getConnection();
    
    echo json_encode([
        "message" => "Database connection successful",
        "database" => $dbName,
        "status" => "success",
        "timestamp" => date('Y-m-d H:i:s'),
        "server_info" => [
            "php_version" => PHP_VERSION,
            "server_software" => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'
        ]
    ]);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "message" => "Database connection failed",
        "error" => "Database error occurred", // Hide actual DB error from response
        "status" => "error",
        "timestamp" => date('Y-m-d H:i:s')
    ]);
} catch(Exception $e) {
    http_response_code(400);
    echo json_encode([
        "message" => $e->getMessage(),
        "status" => "error",
        "timestamp" => date('Y-m-d H:i:s')
    ]);
}
