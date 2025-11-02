import React from 'react';
import { useNavigate } from 'react-router-dom';

function Todo({ todo, onToggle }) {
    const navigate = useNavigate();
    
    const handleChange = () => {
        if (onToggle) {
            onToggle(todo.id);
        }
    };

    const handleDetailClick = () => {
        navigate(`/detail?id=${todo.id}`);
    };

    const handleDeleteClick = async (id) => {
        //本当に削除しますか？の確認ダイアログを作成
        console.log(id);
        if (!window.confirm('本当に削除しますか？')) {
            return;
        }
        try {
            const response = await fetch(`http://localhost:8080/todoDelete.php`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id })
            });
            if (!response.ok) {
                throw new Error('Todoの削除に失敗しました');
            } else {
                window.location.reload();
            }
        } catch (err) {
            alert(err.message);
        }
    };

    return (
        <div style={{
            border: '1px solid #ccc',
            borderRadius: '5px',
            padding: '10px',
            marginBottom: '10px',
            backgroundColor: todo.completed ? '#e0ffe0' : '#fff'
        }}>
            <h3>{todo.title}</h3>
            <p>{todo.description}</p>
            <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
                <label>
                    <input
                        type="checkbox"
                        checked={todo.completed}
                        onChange={handleChange}
                    />
                    Completed
                </label>
                <button
                    onClick={handleDetailClick}
                    style={{
                        padding: '5px 10px',
                        backgroundColor: '#007bff',
                        color: 'white',
                        border: 'none',
                        borderRadius: '3px',
                        cursor: 'pointer'
                    }}
                >
                    詳細
                </button>
                <button
                    onClick={() => handleDeleteClick(todo.id)}
                    style={{
                        padding: '5px 10px',
                        backgroundColor: 'red',
                        color: 'white',
                        border: 'none',
                        borderRadius: '3px',
                        cursor: 'pointer'
                    }}
                >
                    削除
                </button>
            </div>
        </div>
        );
    };

export default Todo;