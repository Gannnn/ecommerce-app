import axios from 'axios'

const SESSION_KEY = 'cart_session_id'

function getSessionId(): string {
  let id = localStorage.getItem(SESSION_KEY)
  if (!id) {
    id = crypto.randomUUID()
    localStorage.setItem(SESSION_KEY, id)
  }
  return id
}

const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

api.interceptors.request.use((config) => {
  config.headers['X-Session-ID'] = getSessionId()

  const token = localStorage.getItem('auth_token')
  if (token) {
    config.headers['Authorization'] = `Bearer ${token}`
  }

  return config
})

export { getSessionId }
export default api
