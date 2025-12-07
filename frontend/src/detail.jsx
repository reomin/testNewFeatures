import React, { useState, useEffect } from 'react';

function Detail() {
    const [todo, setTodo] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        const hash = window.location.hash.slice(1); // #を除去
        const urlParams = new URLSearchParams(hash.split('?')[1] || ''); // detail?id=123の?以降を取得
        const id = urlParams.get('id');

        if (id) {
            fetchTodoDetail(id);
        } else {
            setError('Todo IDが指定されていません');
            setLoading(false);
        }
    }, []);

    const fetchTodoDetail = async (id) => {
        try {
            const response = await fetch(`http://localhost:8080/todo.php?id=${id}`);
            if (!response.ok) {
                throw new Error('Todoの取得に失敗しました');
            }
            const data = await response.json();
            setTodo(data);
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    const handleBack = () => {
        window.history.back();
    };

    if (loading) {
        return (
            <div style={{ padding: '20px', textAlign: 'center' }}>
                <p>読み込み中...</p>
            </div>
        );
    }

    if (error) {
        return (
            <div style={{ padding: '20px', textAlign: 'center' }}>
                <p style={{ color: 'red' }}>エラー: {error}</p>
                <button onClick={handleBack}>戻る</button>
            </div>
        );
    }

    return (
        <div style={{ padding: '20px', maxWidth: '600px', margin: '0 auto' }}>
            <div style={{ marginBottom: '20px' }}>
                <button onClick={handleBack}>← 戻る</button>
            </div>

            {todo && (
                <div style={{
                    border: '1px solid #ddd',
                    borderRadius: '8px',
                    padding: '20px',
                    backgroundColor: '#f9f9f9'
                }}>
                    <h1 style={{ marginBottom: '15px', color: '#333' }}>
                        {todo.title}
                    </h1>

                    <div style={{ marginBottom: '15px' }}>
                        <strong>説明:</strong>
                        <p style={{ marginTop: '5px', lineHeight: '1.5' }}>
                            {todo.description || '説明がありません'}
                        </p>
                    </div>

                    <div style={{ marginBottom: '15px' }}>
                        <strong>ステータス:</strong>
                        <span style={{
                            marginLeft: '10px',
                            padding: '4px 8px',
                            borderRadius: '4px',
                            backgroundColor: todo.completed ? '#d4edda' : '#fff3cd',
                            color: todo.completed ? '#155724' : '#856404'
                        }}>
                            {todo.completed ? '完了' : '未完了'}
                        </span>
                    </div>

                    {todo.created_at && (
                        <div style={{ marginBottom: '15px' }}>
                            <strong>作成日:</strong>
                            <span style={{ marginLeft: '10px' }}>
                                {new Date(todo.created_at).toLocaleString('ja-JP')}
                            </span>
                        </div>
                    )}

                    {todo.updated_at && (
                        <div>
                            <strong>更新日:</strong>
                            <span style={{ marginLeft: '10px' }}>
                                {new Date(todo.updated_at).toLocaleString('ja-JP')}
                            </span>
                        </div>
                    )}
                </div>
            )}
        </div>
    );
}

export default Detail;