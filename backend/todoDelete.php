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

// DELETEリクエストのJSON bodyを取得
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// IDの取得
if (!isset($data['id']) || empty($data['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'IDが指定されていません']);
    exit;
}

$id = $data['id'];

try {
    $dsn = "mysql:host=db;dbname=todo_app;charset=utf8mb4";
    $username = "user";
    $password = "password";
    $driver_options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO($dsn, $username, $password, $driver_options);

    // データベースから削除
    $stmt = $pdo->prepare("DELETE FROM todos WHERE id = :id");
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // 削除されたレコード数を確認
    if ($stmt->rowCount() > 0) {
        http_response_code(200);
        echo json_encode(['success' => true, 'message' => 'Todoが削除されました']);
    } else {
        http_response_code(404);
        echo json_encode(['error' => '指定されたTodoが見つかりません']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'データベースエラー: ' . $e->getMessage()]);
}
