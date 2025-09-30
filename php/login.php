<?php
// php/login.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/redis_helper.php';

function json($arr) { echo json_encode($arr); exit; }

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) json(['success'=>false, 'error'=>'Missing credentials']);

try {
    $stmt = $pdo->prepare("SELECT id, password_hash, fullname, email FROM users WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();
    if (!$user) json(['success'=>false, 'error'=>'Invalid credentials']);

    if (!password_verify($password, $user['password_hash'])) {
        json(['success'=>false, 'error'=>'Invalid credentials']);
    }

    // create a random token
    $token = bin2hex(random_bytes(32));
    $sessionData = [
        'user_id' => (int)$user['id'],
        'email' => $user['email'],
        'fullname' => $user['fullname'],
        'created_at' => time()
    ];

    // store in Redis
    if ($redis) {
        $redisKey = "sess:".$token;
        $redis->set($redisKey, json_encode($sessionData), $REDIS_TTL);
    } else {
        // Fallback: store in database sessions table (optional)
        // For simplicity, still return token but backend endpoints will fail if Redis missing
    }

    json(['success'=>true, 'token'=>$token, 'email'=>$user['email']]);
} catch (PDOException $e) {
    json(['success'=>false, 'error'=>'Server error']);
}
