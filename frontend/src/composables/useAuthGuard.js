import useAuth from './useAuth.js'

const useAuthGuard = () => {
  const { getAuthUser, deleteAuthUser, validateAuthUser } = useAuth()

  const isUserAuthenticated = async (to, from, next) => {
    const isAuthenticated = getAuthUser()
    const tokenIsValid = isAuthenticated ? await validateAuthUser(isAuthenticated.token) : false

    if (tokenIsValid) next()
    else {
      deleteAuthUser()
      next('/login')
    }
  }

  const redirectUser = (to, from, next) => {
    let isAuthenticated = getAuthUser()
    if (isAuthenticated) next('')
    else next()
  }

  return {
    isUserAuthenticated,
    redirectUser
  }
}

export default useAuthGuard
