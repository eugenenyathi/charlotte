<template>
  <section class="user-profile-wrapper">
    <div class="user-profile-content">
      <Loader v-if="isLoading" />
      <h1 class="heading" v-if="!isLoading">{{ collective.fullName }}</h1>
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

import axios from 'axios'
import { ref, reactive } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()

const studentNumber = route.params.studentID

const collective = reactive({
  studentNumber: '',
  fullName: '',
  faculty: '',
  program: '',
  studentType: '',
  part: '',
  enrolled: ''
})

const axiosError = ref('')
const isLoading = ref(true)


const fetchProfile = async (studentNumber) => {
  try {
    const { data } = await axios(`/profile/${studentNumber}`)

    collective.studentNumber = data.studentNumber
    collective.fullName = data.fullName
    collective.faculty = data.faculty
    collective.program = data.program
    collective.studentType = data.studentType
    collective.part = data.part
    collective.enrolled = data.enrolled

    isLoading.value = false
  } catch (err) {
    useAxiosError(err, axiosError)
  }
}

fetchProfile(studentNumber)
</script>
