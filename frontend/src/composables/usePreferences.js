import axios from 'axios'
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useStore } from 'vuex'
import useAxiosError from '@/composables/useAxiosError.js'

const usePreferences = () => {
  const isLoading = ref(false)
  const axiosSuccess = ref(false)
  const axiosError = ref(null)
  const router = useRouter()
  const store = useStore()

  const preferences = computed(() => store.getters.cachedRoommatePreference)
  const student = computed(() => store.getters.getUser)
  const { studentNumber } = student.value

  const getPreferences = async () => {
    try {
      isLoading.value = true

      const response = await axios(`/roommate_preference/${studentNumber}`)

      // console.log('Preferences:', response.data.data.preference_set)

      let payload = {}

      switch (response.data.data.preference_set) {
        case true:
          payload = {
            question_1: response.data.data.question_1,
            question_2: response.data.data.question_2
          }

          store.dispatch('cacheRoommatePreference', payload)
          break
        case false:
          //redirect to CreatePreferences
          return router.push({ name: 'Create Preferences' })
      }

      isLoading.value = false
    } catch (error) {
      console.log(error)
      // useAxiosError(error, axiosError, isLoading)
    }
  }

  const createPreferences = async (payload) => {
    const request = {
      student_id: studentNumber,
      question_1: payload.question_1,
      question_2: payload.question_2
    }

    try {
      isLoading.value = true

      await axios.post('/roommate_preference', request)

      isLoading.value = false
      axiosSuccess.value = true
    } catch (error) {
      useAxiosError(error, axiosError, isLoading)
    }
  }

  const updatePreferences = async () => {
    const payload = {
      student_id: studentNumber,
      question_1: preferences.value.question_1,
      question_2: preferences.value.question_2
    }

    try {
      isLoading.value = true

      await axios.put('/roommate_preference', payload)

      isLoading.value = false
      axiosSuccess.value = true
    } catch (error) {
      useAxiosError(error, axiosError, isLoading)
    }
  }

  return {
    axiosSuccess,
    axiosError,
    isLoading,
    getPreferences,
    createPreferences,
    updatePreferences
  }
}

export default usePreferences
