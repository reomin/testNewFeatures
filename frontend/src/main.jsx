import React from 'react'
import ReactDOM from 'react-dom/client'
import App from './App.jsx'
import Detail from './detail.jsx'
import { BrowserRouter, Routes, Route } from "react-router-dom"
import Todos from './todos.jsx'


ReactDOM.createRoot(document.getElementById('root')).render(
  <React.StrictMode>
    <BrowserRouter>
        <Routes>
          <Route path="/" element={<App />} />
          <Route path="/detail" element={<Detail />} />
          <Route path="/todos" element={<Todos />} />
        </Routes>
    </BrowserRouter>
  </React.StrictMode>,
)
