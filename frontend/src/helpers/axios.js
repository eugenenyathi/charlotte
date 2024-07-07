import axios from 'axios'
import useToken from '@/composables/useToken'

const AUTH_TOKEN = useToken()

axios.defaults.baseURL = 'http://localhost:8000/api/v1'
axios.defaults.headers.common['Authorization'] = `Bearer ${AUTH_TOKEN}`
axios.defaults.headers.post['Content-Type'] = 'application/json'
