import axios from 'axios'
import { ref, computed } from 'vue'
import { useStore } from 'vuex'
import useAxiosError from '../composables/useAxiosError.js'

import SelectionResponse from '@/constants/SelectionResponse.js'

const useRequest = () => {
  const isLoading = ref(false)
  const axiosSuccess = ref(false)
  const axiosError = ref(null)

  const store = useStore()

  const student = computed(() => store.getters.getUser)
  const { studentNumber } = student.value

  const selectedMates = computed(() => store.getters.selectedStudents)

  const createRequest = async () => {
    const request = {
      requester: studentNumber,
      roommates: selectedMates.value
    }

    try {
      isLoading.value = true

      const { data } = await axios.post('/request', request)

      isLoading.value = false
      return data
    } catch (err) {
      useAxiosError(err, axiosError, isLoading)
    }
  }

  const updateRequest = async () => {
    const request = {
      requester: studentNumber,
      roommates: selectedMates.value
    }

    try {
      isLoading.value = true

      const { data } = await axios.put('/request', request)
      store.dispatch('addPreferredRoommates', data.roommates)

      isLoading.value = false
      axiosSuccess.value = true
    } catch (err) {
      useAxiosError(err, axiosError, isLoading)
    }
  }

  const sendResponse = async (requesterID, response) => {
    try {
      isLoading.value = true
      // console.log(studentNumber, requesterID, response);

      const { data: roommates } = await axios.patch('/request/response', {
        studentID: studentNumber,
        requesterID,
        response
      })

      if (response === SelectionResponse.YES) store.dispatch('addPreferredRoommates', roommates)
      isLoading.value = false
    } catch (err) {
      useAxiosError(err, axiosError, isLoading)
    }
  }

  const sendMultiResponse = async (response, requesterID) => {
    try {
      isLoading.value = true
      const { data: roommates } = await axios.patch('/request/response', {
        studentID: studentNumber,
        requesterID,
        response
      })

      if (response === SelectionResponse.YES) store.dispatch('addPreferredRoommates', roommates)
      isLoading.value = false
    } catch (err) {
      useAxiosError(err, axiosError, isLoading)
    }
  }

  return {
    isLoading,
    axiosSuccess,
    axiosError,
    createRequest,
    updateRequest,
    sendResponse,
    sendMultiResponse
  }
}

export default useRequest
