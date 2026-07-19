import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL || 'http://todo-backend.test/api',
  headers: { Accept: 'application/json' },
  timeout: 10000,
})

export default api
