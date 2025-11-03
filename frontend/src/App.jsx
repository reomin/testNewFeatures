import React, { useState, useEffect } from 'react'
import Detail from './detail.jsx'
import Todos from './todos.jsx'

function Home() {
  return (
    <div style={{ padding: '20px', textAlign: "center" }}>
      <h1>This is Todo App</h1>
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

function App() {
  const [currentPage, setCurrentPage] = useState('home')

  useEffect(() => {
    const handleHashChange = () => {
      const hash = window.location.hash.slice(1)
      if (hash.startsWith('detail')) {
        setCurrentPage('detail')
      } else if (hash === 'todos') {
        setCurrentPage('todos')
      } else {
        setCurrentPage('home')
      }
    }

    handleHashChange()
    window.addEventListener('hashchange', handleHashChange)

    return () => {
      window.removeEventListener('hashchange', handleHashChange)
    }
  }, [])

  if (currentPage === 'detail') {
    return <Detail />
  }

  if (currentPage === 'todos') {
    return <Todos />
  }

  return <Home />
}

export default App
