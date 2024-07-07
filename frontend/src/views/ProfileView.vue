<template>
  <section class="user-profile-wrapper">
    <div class="user-profile-content">
      <Loader v-if="isLoading" />
      <h1 class="heading" v-if="!isLoading">About You</h1>
      <div class="info-wrapper" v-if="!isLoading">
        <div class="info">
          <p>Student Number</p>
          <p>{{ profile.studentNumber }}</p>
        </div>
        <div class="info">
          <p>Faculty</p>
          <p>{{ profile.faculty }}</p>
        </div>
        <div class="info">
          <p>Program</p>
          <p>{{ profile.program }}</p>
        </div>
        <div class="info">
          <p>Student Type</p>
          <p>{{ profile.studentType }}</p>
        </div>
        <div class="info">
          <p>Part</p>
          <p>{{ profile.part }}</p>
        </div>
        <div class="info">
          <p>Enrolled</p>
          <p>{{ profile.enrolled }}</p>
        </div>
        <div class="info show">
          <p>Last Login</p>
          <p>{{ dateFns(profile.timestamp) }}</p>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import Loader from '../components/Loader.vue'
import useAxiosError from '../composables/useAxiosError.js'

import axios from 'axios'
import dateFns from '../helpers/dateFns.js'
import { ref, computed } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const student = computed(() => store.getters.getUser)
const { studentNumber } = student.value

const profile = computed(() => store.getters.cachedProfile)

const axiosError = ref('')
const isLoading = ref(true)


const fetchProfile = async (studentNumber) => {
  try {
    const { data } = await axios(`/profile/${studentNumber}`)

    store.dispatch('cacheProfile', data)
    isLoading.value = false
  } catch (err) {
    useAxiosError(err, axiosError)
  }
}

if (profile.value.length < 1) fetchProfile(studentNumber)
else isLoading.value = false

</script>
