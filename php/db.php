<?php
// php/db.php
// central DB connection using PDO

$DB_HOST = '127.0.0.1';
$DB_NAME = 'userdb';
$DB_USER = 'dbuser';
$DB_PASS = '';
$DB_CHAR = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=$DB_CHAR", $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // In production do not echo errors
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit;
}
