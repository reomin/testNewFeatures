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

// GETパラメータから検索クエリを取得
$query = $_GET['q'] ?? '';

if (empty($query)) {
    http_response_code(400);
    echo json_encode(['error' => '検索クエリが指定されていません']);
    exit;
}

try {
    $elasticsearch = new ElasticsearchClient();
    $result = $elasticsearch->searchTodos($query);

    if ($result['code'] === 200) {
        $hits = $result['body']['hits']['hits'] ?? [];
        $todos = [];

        foreach ($hits as $hit) {
            $todo = $hit['_source'];
            $todo['id'] = $hit['_id'];
            $todo['score'] = $hit['_score'];
            $todos[] = $todo;
        }

        echo json_encode([
            'success' => true,
            'data' => $todos,
            'total' => $result['body']['hits']['total']['value'] ?? 0
        ]);
    } else {
        // Elasticsearchが利用できない場合は、データベースでLIKE検索
        $dsn = "mysql:host=db;dbname=todo_app;charset=utf8mb4";
        $username = "user";
        $password = "password";
        $driver_options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $pdo = new PDO($dsn, $username, $password, $driver_options);
        $stmt = $pdo->prepare("SELECT * FROM todos WHERE title LIKE :query OR description LIKE :query ORDER BY created_at DESC");
        $searchTerm = "%{$query}%";
        $stmt->bindParam(':query', $searchTerm);
        $stmt->execute();
        $todos = $stmt->fetchAll();

        echo json_encode([
            'success' => true,
            'data' => $todos,
            'total' => count($todos),
            'fallback' => true
        ]);
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => '検索エラー: ' . $e->getMessage()]);
}
