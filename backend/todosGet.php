<?php
// CORSヘッダーを設定
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

// OPTIONSリクエストの場合は200を返して終了
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'services.php';

try {
    $redis = new RedisCache();

    // キャッシュから取得を試す
    $todos = $redis->get('todos_list');

    if ($todos === false) {
        // キャッシュにない場合はデータベースから取得
        $dsn = "mysql:host=db;dbname=todo_app;charset=utf8mb4";
        $username = "user";
        $password = "password";
        $driver_options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $pdo = new PDO($dsn, $username, $password, $driver_options);
        $stmt = $pdo->prepare("SELECT * FROM todos ORDER BY created_at DESC");
        $stmt->execute();
        $todos = $stmt->fetchAll();

        // データをキャッシュに保存（5分間）
        $redis->set('todos_list', $todos, 300);
    }

    echo json_encode($todos);
} catch (Exception $e) {
    // Redisが利用できない場合はDBから直接取得
    $dsn = "mysql:host=db;dbname=todo_app;charset=utf8mb4";
    $username = "user";
    $password = "password";
    $driver_options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO($dsn, $username, $password, $driver_options);
    $stmt = $pdo->prepare("SELECT * FROM todos ORDER BY created_at DESC");
    $stmt->execute();
    $todos = $stmt->fetchAll();

    echo json_encode($todos);
}
