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

// GETパラメータからIDを取得
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

// サンプルデータ（実際のアプリケーションではデータベースから取得）
$sampleTodos = [
    1 => [
        'id' => 1,
        'title' => 'サンプルタスク1',
        'description' => 'これは最初のサンプルタスクです。',
        'completed' => false,
        'created_at' => '2024-01-01 10:00:00',
        'updated_at' => '2024-01-01 10:00:00'
    ],
    2 => [
        'id' => 2,
        'title' => 'サンプルタスク2',
        'description' => 'これは完了済みのサンプルタスクです。',
        'completed' => true,
        'created_at' => '2024-01-02 14:30:00',
        'updated_at' => '2024-01-03 16:45:00'
    ],
    3 => [
        'id' => 3,
        'title' => '長いタイトルのサンプルタスク',
        'description' => 'これは説明文が長いサンプルタスクです。複数行にわたる詳細な説明があります。実際のアプリケーションでは、このような詳細な情報が含まれる場合があります。',
        'completed' => false,
        'created_at' => '2024-01-03 09:15:00',
        'updated_at' => '2024-01-03 09:15:00'
    ]
];

// 指定されたIDのTodoが存在するかチェック
if (!isset($sampleTodos[$id])) {
    http_response_code(404);
    echo json_encode(['error' => 'Todo not found']);
    exit;
}

// Todoデータを返す
echo json_encode($sampleTodos[$id]);
?>