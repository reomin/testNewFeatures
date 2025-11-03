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

//ここにphpのapiを書く

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

// データベースの追加
$stmt = $pdo->prepare("INSERT INTO todos (title, description) VALUES (:title, :description)");
$stmt->bindParam(":title", $title);
$stmt->bindParam(":description", $description);
$stmt->execute();

//元の画面に戻す
header("Location: http://localhost:5173");
