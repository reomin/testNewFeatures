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

$dsn = "mysql:host=db;dbname=todo_app;charset=utf8mb4";
$username = "user";
$password = "password";
$driver_options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

$pdo = new PDO($dsn, $username, $password, $driver_options);

// データベースの追加
$stmt = $pdo->prepare("SELECT * FROM todos");
$stmt->execute();

$todos = $stmt->fetchAll();

// サンプルのTodoデータ（実際のアプリケーションではデータベースから取得）
// $sampleTodos = [
//     [
//         'id' => 1,
//         'title' => 'サンプルタスク1',
//         'description' => 'これは最初のサンプルタスクです。',
//         'completed' => false,
//         'created_at' => '2024-01-01 10:00:00',
//         'updated_at' => '2024-01-01 10:00:00'
//     ],
//     [
//         'id' => 2,
//         'title' => 'サンプルタスク2',
//         'description' => 'これは完了済みのサンプルタスクです。',
//         'completed' => true,
//         'created_at' => '2024-01-02 14:30:00',
//         'updated_at' => '2024-01-03 16:45:00'
//     ],
//     [
//         'id' => 3,
//         'title' => '長いタイトルのサンプルタスク',
//         'description' => 'これは説明文が長いサンプルタスクです。複数行にわたる詳細な説明があります。',
//         'completed' => false,
//         'created_at' => '2024-01-03 09:15:00',
//         'updated_at' => '2024-01-03 09:15:00'
//     ]
// ];

echo json_encode($todos);
