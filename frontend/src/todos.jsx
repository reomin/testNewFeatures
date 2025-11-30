import React from 'react';
import Todo from './components/todo.jsx';
import { useEffect, useState } from 'react';

function Todos() {
    const [todos, setTodos] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const [searchQuery, setSearchQuery] = useState('');
    const [isSearching, setIsSearching] = useState(false);
    const [searchResults, setSearchResults] = useState([]);

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

    const handleSearch = async (e) => {
        e.preventDefault();
        if (!searchQuery.trim()) {
            setSearchResults([]);
            setIsSearching(false);
            return;
        }

        setIsSearching(true);
        try {
            const response = await fetch(`http://localhost:8080/search.php?q=${encodeURIComponent(searchQuery)}`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('検索に失敗しました');
            }
            
            const result = await response.json();
            if (result.success) {
                setSearchResults(result.data || []);
            } else {
                throw new Error(result.error || '検索エラー');
            }
        } catch (err) {
            setError(err.message);
            setSearchResults([]);
        }
    };

    const clearSearch = () => {
        setSearchQuery('');
        setSearchResults([]);
        setIsSearching(false);
    };

    if (loading) {
        return <div style={{ padding: '20px', textAlign: 'center' }}>読み込み中...</div>;
    }

    if (error) {
        return <div style={{ padding: '20px', textAlign: 'center', color: 'red' }}>エラー: {error}</div>;
    }

    const displayTodos = isSearching ? searchResults : todos;

    return (
        <div style={{ padding: '20px' }}>
            <h1>Todo一覧</h1>
            
            {/* 検索フォーム */}
            <div style={{ marginBottom: '20px', padding: '15px', border: '1px solid #ddd', borderRadius: '5px' }}>
                <form onSubmit={handleSearch} style={{ display: 'flex', gap: '10px', alignItems: 'center' }}>
                    <input
                        type="text"
                        value={searchQuery}
                        onChange={(e) => setSearchQuery(e.target.value)}
                        placeholder="Todoを検索..."
                        style={{
                            flex: 1,
                            padding: '8px 12px',
                            border: '1px solid #ccc',
                            borderRadius: '4px',
                            fontSize: '14px'
                        }}
                    />
                    <button
                        type="submit"
                        style={{
                            padding: '8px 16px',
                            backgroundColor: '#007bff',
                            color: 'white',
                            border: 'none',
                            borderRadius: '4px',
                            cursor: 'pointer',
                            fontSize: '14px'
                        }}
                    >
                        検索
                    </button>
                    {isSearching && (
                        <button
                            type="button"
                            onClick={clearSearch}
                            style={{
                                padding: '8px 16px',
                                backgroundColor: '#6c757d',
                                color: 'white',
                                border: 'none',
                                borderRadius: '4px',
                                cursor: 'pointer',
                                fontSize: '14px'
                            }}
                        >
                            クリア
                        </button>
                    )}
                </form>
                {isSearching && (
                    <div style={{ marginTop: '10px', fontSize: '14px', color: '#666' }}>
                        検索結果: {searchResults.length} 件
                    </div>
                )}
            </div>
            
            <div style={{ marginBottom: '20px' }}>
                <a href="/" style={{ color: 'blue', textDecoration: 'underline' }}>ホームに戻る</a>
            </div>
            
            {displayTodos.length === 0 ? (
                <p>{isSearching ? '検索結果がありません' : 'Todoがありません'}</p>
            ) : (
                displayTodos.map(todo => (
                    <Todo key={todo.id} todo={todo} />
                ))
            )}
        </div>
    );
}

export default Todos;