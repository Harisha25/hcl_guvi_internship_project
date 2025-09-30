<?php
// php/profile_api.php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/redis_helper.php';

function json($arr) { echo json_encode($arr); exit; }

$headers = getallheaders();
$auth = null;
if (!empty($headers['Authorization'])) $auth = $headers['Authorization'];
elseif (!empty($_SERVER['HTTP_AUTHORIZATION'])) $auth = $_SERVER['HTTP_AUTHORIZATION'];

if (!$auth || !preg_match('/Bearer\s+([a-f0-9]{64})/i', $auth, $m)) {
    http_response_code(401);
    json(['success'=>false, 'error'=>'Unauthorized']);
}
$token = $m[1];

$redisKey = "sess:".$token;
if (!$redis) {
    http_response_code(500);
    json(['success'=>false, 'error'=>'Session store unavailable']);
}

$sessionJson = $redis->get($redisKey);
if (!$sessionJson) {
    http_response_code(401);
    json(['success'=>false, 'error'=>'Invalid session']);
}

$session = json_decode($sessionJson, true);
$userId = (int)$session['user_id'];

$action = $_REQUEST['action'] ?? 'get_profile';

try {
    if ($action === 'get_profile') {
        $stmt = $pdo->prepare("SELECT id, fullname, email, age, dob, contact FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch();
        if (!$user) {
            json(['success'=>false, 'error'=>'User not found']);
        }
        json(['success'=>true, 'user' => $user]);
    } elseif ($action === 'update_profile') {
        $fullname = trim($_POST['fullname'] ?? '');
        $age = isset($_POST['age']) && $_POST['age'] !== '' ? (int)$_POST['age'] : null;
        $dob = $_POST['dob'] ?? null;
        $contact = $_POST['contact'] ?? null;

        if (!$fullname) json(['success'=>false, 'error'=>'fullname required']);

        $upd = $pdo->prepare("UPDATE users SET fullname = :fullname, age = :age, dob = :dob, contact = :contact WHERE id = :id");
        $upd->execute([
            ':fullname' => $fullname,
            ':age' => $age,
            ':dob' => $dob,
            ':contact' => $contact,
            ':id' => $userId
        ]);

        // refresh redis session fullname
        $session['fullname'] = $fullname;
        $redis->set($redisKey, json_encode($session), $REDIS_TTL);

        json(['success'=>true]);
    } elseif ($action === 'logout') {
        $redis->del($redisKey);
        json(['success'=>true]);
    } else {
        json(['success'=>false, 'error'=>'Unknown action']);
    }
} catch (PDOException $e) {
    json(['success'=>false, 'error'=>'Server error']);
}
