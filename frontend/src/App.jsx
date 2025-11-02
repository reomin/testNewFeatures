import React from 'react'

function App() {
  return (
    <div style={{ padding: '20px', textAlign: "center" }}>
      <h1>This is Todo App Frontend</h1>
      <p>Reactアプリケーションが正常に動作しています</p>
      <div className="h1-container">
        <form action="http://localhost:8080/todos.php" method="post">
          <input type="text" name="title" required style={{ marginBottom: '10px', marginRight: '10px' }} />
          <input type="text" name="description" required style={{ marginBottom: '10px', marginRight: '10px' }} />
          <button type="submit">Add Todo</button>
        </form>
      </div>
    </div>
  )
}

export default App
