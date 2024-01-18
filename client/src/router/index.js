import { createRouter, createWebHistory } from 'vue-router'

import Home from '../views/Home.vue'
import Login from '../views/Login.vue'
import Logout from '../views/Logout.vue'
import Signup from '../views/Signup.vue'
import ResetPassword from '../views/ResetPassword.vue'
import NotFound from '../views/NotFound.vue'

//Home route children components
import Dashboard from '../views/Dashboard.vue'
import Profile from '../views/Profile.vue'
import Residence from '../views/Residence.vue'
import Settings from '../views/Settings.vue'

import RoomieProfile from '../views/RoomieProfile.vue'

//composables
import useRoute from '../composables/useRoute.js'

const { redirectRoute, protectHomeRoute } = useRoute()

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      component: Home,
      name: 'Home',
      beforeEnter: protectHomeRoute,
      children: [
        {
          path: '/',
          component: Dashboard,
          name: 'Dashboard'
        },
        {
          path: 'profile',
          component: Profile,
          name: 'Profile'
        },
        {
          path: 'roomie/:studentID',
          component: RoomieProfile,
          name: 'RoomieProfile'
        },
        {
          path: 'residence',
          component: Residence,
          name: 'Residence'
        },
        {
          path: 'settings',
          component: Settings,
          name: 'Settings'
        }
      ]
    },
    {
      path: '/login',
      component: Login,
      name: 'Login',
      beforeEnter: redirectRoute
    },
    {
      path: '/signup',
      component: Signup,
      name: 'Signup',
      beforeEnter: redirectRoute
    },
    {
      path: '/reset_password',
      component: ResetPassword,
      name: 'ResetPassword',
      beforeEnter: redirectRoute
    },
    {
      path: '/logout',
      component: Logout,
      name: 'Logout'
    },
    {
      path: '/:catchAll(.*)',
      component: NotFound,
      name: 'NotFound'
    }
  ]
})

export default router
