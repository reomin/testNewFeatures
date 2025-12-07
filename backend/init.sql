-- todosテーブルの作成
CREATE TABLE IF NOT EXISTS todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    completed BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- サンプルデータの挿入（オプション）
INSERT INTO todos (title, description, completed) VALUES 
('サンプルタスク1', 'これはサンプルのタスクです', FALSE),
('完了済みタスク', 'これは完了済みのタスクです', TRUE);
