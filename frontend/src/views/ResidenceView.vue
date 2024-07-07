<template>
  <section class="residence-wrapper">
    <div class="residence-content">
      <Loader v-if="isLoading" />
      <div class="join join-vertical w-full" v-if="!isLoading">
        <UiAccordion v-for="(res, index) in residence" :key="res.part" :res="res" :index="index" />
      </div>
    </div>
  </section>
</template>

<script setup>
import Loader from '@/components/Loader.vue'
import UiAccordion from '@/components/ui/UiAccordion.vue'
import useAxiosError from '@/composables/useAxiosError.js'

import axios from 'axios'
import { ref, computed } from 'vue'
import { useStore } from 'vuex'

// TODO: fetch once, not ever again, store it on vuex

const store = useStore()
const student = computed(() => store.getters.getUser)
const { studentNumber } = student.value
const axiosError = ref('')
const isLoading = ref(true)
const residence = computed(() => store.getters.cachedResidence)


const fetchResidence = async (studentNumber) => {
  try {
    const { data } = await axios(`/residence/${studentNumber}`)

    store.dispatch('cacheResidence', data)
    isLoading.value = false
  } catch (err) {
    useAxiosError(err, axiosError)
  }
}

if (residence.value.length < 1) {
  fetchResidence(studentNumber)
} else isLoading.value = false

</script>
