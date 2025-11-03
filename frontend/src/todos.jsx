import React from 'react';
import Todo from './components/todo.jsx';
import { useEffect, useState } from 'react';

function Todos() {
    const [todos, setTodos] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    //ページをロードした時に１回だけ実行する
    useEffect(() => {
        fetchTodos();
    }, []);

    const fetchTodos = async () => {
        try {
            const response = await fetch(`http://localhost:8080/todosGet.php`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            if (!response.ok) {
                throw new Error('Todoの取得に失敗しました');
            }
            const data = await response.json();
            console.log(data);
            // dataが配列でない場合の対処
            if (Array.isArray(data)) {
                setTodos(data);
            } else {
                setTodos([]);
            }
        } catch (err) {
            setError(err.message);
        } finally {
            setLoading(false);
        }
    };

    if (loading) {
        return <div style={{ padding: '20px', textAlign: 'center' }}>読み込み中...</div>;
    }

    if (error) {
        return <div style={{ padding: '20px', textAlign: 'center', color: 'red' }}>エラー: {error}</div>;
    }

    return (
        <div style={{ padding: '20px' }}>
            <h1>Todo一覧</h1>
            <div style={{ marginBottom: '20px' }}>
                <a href="/" style={{ color: 'blue', textDecoration: 'underline' }}>ホームに戻る</a>
            </div>
            {todos.length === 0 ? (
                <p>Todoがありません</p>
            ) : (
                todos.map(todo => (
                    <Todo key={todo.id} todo={todo} />
                ))
            )}
        </div>
    );
}

export default Todos;