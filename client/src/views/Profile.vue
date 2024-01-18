<template>
  <section class="user-profile-wrapper">
    <div class="user-profile-content">
      <Loader v-if="isLoading" />
      <h1 class="heading" v-if="!isLoading">About You</h1>
      <div class="info-wrapper" v-if="!isLoading">
        <div class="info">
          <p>Student Number</p>
          <p>{{ collective.studentNumber }}</p>
        </div>
        <div class="info">
          <p>Faculty</p>
          <p>{{ collective.faculty }}</p>
        </div>
        <div class="info">
          <p>Program</p>
          <p>{{ collective.program }}</p>
        </div>
        <div class="info">
          <p>Student Type</p>
          <p>{{ collective.studentType }}</p>
        </div>
        <div class="info">
          <p>Part</p>
          <p>{{ collective.part }}</p>
        </div>
        <div class="info">
          <p>Enrolled</p>
          <p>{{ collective.enrolled }}</p>
        </div>
        <div class="info show">
          <p>Last Login</p>
          <p>{{ collective.lastLogin }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import Loader from '../components/Loader.vue'
import useAxiosError from '../composables/useAxiosError.js'
import useToken from '../composables/useToken.js'
import axios from 'axios'
import dateFns from '../helpers/dateFns.js'
import { ref, reactive, computed } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const student = computed(() => store.getters.getUser)
const { studentNumber } = student.value

const collective = reactive({
  studentNumber: '',
  faculty: '',
  program: '',
  studentType: '',
  part: '',
  enrolled: '',
  lastLogin: ''
})

const axiosError = ref('')
const isLoading = ref(true)

const token = useToken()
const fetchProfile = async (studentNumber) => {
  try {
    const { data } = await axios(`/profile/${studentNumber}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    collective.studentNumber = data.studentNumber
    collective.faculty = data.faculty
    collective.program = data.program
    collective.studentType = data.studentType
    collective.part = data.part
    collective.enrolled = data.enrolled
    collective.lastLogin = dateFns(data.timestamp)

    isLoading.value = false
  } catch (err) {
    useAxiosError(err, axiosError)
  }
}

fetchProfile(studentNumber)
</script>
