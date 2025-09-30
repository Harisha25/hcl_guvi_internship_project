<?php
// php/register.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/redis_helper.php';

function json($arr) { echo json_encode($arr); exit; }

$fullname = trim($_POST['fullname'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$age = isset($_POST['age']) && $_POST['age'] !== '' ? (int)$_POST['age'] : null;
$dob = $_POST['dob'] ?? null;
$contact = $_POST['contact'] ?? null;

if (!$fullname || !$email || !$password) json(['success'=>false, 'error'=>'fullname, email and password required']);

try {
    // check email exists (prepared)
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        json(['success'=>false, 'error'=>'Email already registered']);
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $insert = $pdo->prepare("INSERT INTO users (fullname, email, password_hash, age, dob, contact, created_at) VALUES (:fullname, :email, :password_hash, :age, :dob, :contact, NOW())");
    $insert->execute([
        ':fullname' => $fullname,
        ':email' => $email,
        ':password_hash' => $password_hash,
        ':age' => $age,
        ':dob' => $dob,
        ':contact' => $contact
    ]);

    $userId = $pdo->lastInsertId();

    // Optional: mirror to Mongo (if configured) - non-blocking try
    // if (class_exists('MongoDB\Driver\Manager')) {
    //     try {
    //         $m = new MongoDB\Client("mongodb://127.0.0.1:27017");
    //         $col = $m->selectCollection('user_mirror_db', 'users');
    //         $col->insertOne([
    //             'user_id' => (int)$userId,
    //             'fullname' => $fullname,
    //             'email' => $email,
    //             'age' => $age,
    //             'dob' => $dob,
    //             'contact' => $contact,
    //             'created_at' => new MongoDB\BSON\UTCDateTime()
    //         ]);
    //     } catch (Exception $e) { /* ignore */ }
    // }

    json(['success'=>true]);
} catch (PDOException $e) {
    json(['success'=>false, 'error'=>'Server error']);
}
