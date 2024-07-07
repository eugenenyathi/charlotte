import Cookies from 'js-cookie'
import axios from 'axios'

const useAuth = () => {
  const setAuthUser = (user) => {
    user = JSON.stringify(user)
    Cookies.set('charlotte', user, {
      expires: 14,
      path: '/',
      domain: 'localhost',
      sameSite: 'strict',
      secure: true
    })
  }

  const getAuthUser = () => {
    const cookie = Cookies.get('charlotte')
    return cookie ? JSON.parse(cookie) : false
  }

  const validateAuthUser = async (token) => {
    try {
      await axios('/token', {
        headers: {
          Authorization: `Bearer ${token}`
        }
      })
      return true
    } catch (error) {
      return false
    }
  }

  const deleteAuthUser = () => {
    Cookies.remove('charlotte', {
      path: '/',
      domain: 'localhost'
    })
  }

  return {
    setAuthUser,
    getAuthUser,
    validateAuthUser,
    deleteAuthUser
  }
}

export default useAuth
