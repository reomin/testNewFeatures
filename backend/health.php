<?php
// CORSヘッダーを設定
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// OPTIONSリクエストの場合は200を返して終了
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// GETリクエストのみ許可
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$health = [
    'status' => 'healthy',
    'timestamp' => date('Y-m-d H:i:s'),
    'service' => 'Todo App API',
    'version' => '1.0.0'
];

// データベース接続チェック（オプション）
try {
    $host = 'db';
    $db = 'todo_app';
    $user = 'user';
    $pass = 'password';
    $charset = 'utf8mb4';

    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $health['database'] = 'connected';
} catch (PDOException $e) {
    $health['database'] = 'disconnected';
    $health['database_error'] = $e->getMessage();
}

// レスポンスを返す
echo json_encode($health, JSON_PRETTY_PRINT);
