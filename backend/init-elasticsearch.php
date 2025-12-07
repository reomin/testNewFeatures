<?php
require_once 'services.php';

try {
    $elasticsearch = new ElasticsearchClient();

    // インデックスを作成
    echo "Creating Elasticsearch index...\n";
    $result = $elasticsearch->createIndex();
    echo "Index creation result: " . json_encode($result) . "\n";

    // 既存のTodoデータを全てインデックス
    echo "Indexing existing todos...\n";

    $dsn = "mysql:host=db;dbname=todo_app;charset=utf8mb4";
    $username = "user";
    $password = "password";
    $driver_options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    $pdo = new PDO($dsn, $username, $password, $driver_options);
    $stmt = $pdo->prepare("SELECT * FROM todos");
    $stmt->execute();
    $todos = $stmt->fetchAll();

    foreach ($todos as $todo) {
        // completed フィールドを boolean に変換
        $todo['completed'] = (bool)$todo['completed'];

        $result = $elasticsearch->indexTodo($todo['id'], $todo);
        echo "Indexed todo {$todo['id']}: " . json_encode($result) . "\n";
    }

    echo "Elasticsearch initialization completed!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
