import { createRouter, createWebHistory } from 'vue-router'

import Home from '../views/HomeView.vue'
import Login from '../views/LoginView.vue'
import NotFound from '../views/NotFoundView.vue'

//Home route children components
import Dashboard from '../views/DashboardView.vue'
import Profile from '../views/ProfileView.vue'
import Residence from '../views/ResidenceView.vue'

import RoommateProfile from '../views/RoommateProfile.vue'
import RoommatePreferenceView from '../views/RoommatePreferenceView.vue'
import EditPreferences from '@/components/EditPreferences.vue'

//composables
import useAuthGuard from '../composables/useAuthGuard.js'
import CreatePreferences from '@/components/CreatePreferences.vue'

const { redirectUser, isUserAuthenticated } = useAuthGuard()

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      component: Home,
      name: 'Home',
      beforeEnter: isUserAuthenticated,
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
          path: 'roommate/:studentID',
          component: RoommateProfile,
          name: 'Roommate Profile'
        },
        {
          path: 'roommate_preference',
          component: RoommatePreferenceView,
          name: 'Roommate Preference'
        },
        {
          path: 'create_preferences',
          component: CreatePreferences,
          name: 'Create Preferences'
        },
        {
          path: 'edit_preferences',
          component: EditPreferences,
          name: 'Edit Preferences'
        },
        {
          path: 'residence',
          component: Residence,
          name: 'Residence'
        }
      ]
    },
    {
      path: '/login',
      component: Login,
      name: 'Login',
      beforeEnter: redirectUser
    },
    {
      path: '/:catchAll(.*)',
      component: NotFound,
      name: 'NotFound'
    }
  ]
})

export default router
