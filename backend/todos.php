<?php
// CORSヘッダーを設定
header("Access-Control-Allow-Origin: http://localhost:5173");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// OPTIONSリクエストの場合は200を返して終了
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'services.php';

$title = $_POST["title"];
$description = $_POST["description"];

$dsn = "mysql:host=db;dbname=todo_app;charset=utf8mb4";
$username = "user";
$password = "password";
$driver_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, $username, $password, $driver_options);

// データベースに追加
$stmt = $pdo->prepare("INSERT INTO todos (title, description) VALUES (:title, :description)");
$stmt->bindParam(":title", $title);
$stmt->bindParam(":description", $description);
$stmt->execute();

$todoId = $pdo->lastInsertId();

// 作成されたTodoの詳細を取得
$stmt = $pdo->prepare("SELECT * FROM todos WHERE id = :id");
$stmt->bindParam(":id", $todoId);
$stmt->execute();
$newTodo = $stmt->fetch();

try {
    // Redisキャッシュをクリア
    $redis = new RedisCache();
    $redis->delete('todos_list');

    // Elasticsearchにインデックス（completed フィールドをbooleanに変換）
    $newTodo['completed'] = (bool)$newTodo['completed'];
    $elasticsearch = new ElasticsearchClient();
    $elasticsearch->indexTodo($todoId, $newTodo);
} catch (Exception $e) {
    // サービスが利用できない場合は無視して続行
    error_log("Cache/Search service error: " . $e->getMessage());
}

//元の画面に戻す
header("Location: http://localhost:5173");
