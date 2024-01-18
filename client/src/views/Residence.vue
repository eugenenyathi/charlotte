<template>
  <section class="residence-wrapper">
    <div class="residence-content">
      <Loader v-if="isLoading" />
      <div class="mobile-size-content show" v-if="!isLoading">
        <h1 class="heading">Accommodation</h1>
        <div class="info-wrapper">
          <div class="info">
            <p>Residence fees</p>
            <p>{{ collective.fees }}</p>
          </div>
          <div class="info">
            <p>Check-in</p>
            <p>{{ collective.checkIn }}</p>
          </div>

          <div class="info">
            <p>Check-out</p>
            <p>{{ collective.checkOut }}</p>
          </div>
        </div>
      </div>
      <div class="desktop-size-content" v-if="!isLoading">
        <h1 class="heading">Living Space</h1>
        <p class="mini-heading" v-if="showPrevResidence">
          You have not been allocated a room yet. Showing previous residence.
        </p>
        <div class="info-wrapper">
          <div class="info">
            <p>Hostel</p>
            <p>{{ collective.hostel }}</p>
          </div>
          <div class="info">
            <p>Side</p>
            <p>{{ collective.side }}</p>
          </div>
          <div class="info">
            <p>Floor</p>
            <p>{{ collective.floor }}</p>
          </div>
          <div class="info">
            <p>Floor side</p>
            <p>{{ collective.floorSide }}</p>
          </div>
          <div class="info">
            <p>Room</p>
            <p>{{ collective.room }}</p>
          </div>
          <div class="info" v-if="showPrevResidence">
            <p>Part</p>
            <p>{{ collective.part }}</p>
          </div>
          <div class="info" v-if="collective.checkedIn">
            <p>Checked-in</p>
            <p>{{ collective.checkedIn }}</p>
          </div>
          <div class="info" v-if="collective.checkedOut">
            <p>Checked-out</p>
            <p>{{ collective.checkedOut }}</p>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup>
import Loader from '../components/Loader.vue'
import useAxiosError from '../composables/useAxiosError.js'
import useToken from '../composables/useToken.js'
import formatDollars from '../helpers/formatDollars.js'

import axios from 'axios'
import dayjs from 'dayjs'

import { ref, reactive, computed } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const student = computed(() => store.getters.getUser)
const { studentNumber } = student.value

const axiosError = ref('')
const isLoading = ref(true)
const showPrevResidence = ref(false)
const collective = reactive({
  hostel: '',
  room: '',
  part: '',
  floor: '',
  side: '',
  checkedIn: '',
  checkedOut: '',
  fees: '',
  checkIn: '',
  checkOut: ''
})

const token = useToken()
const fetchResidence = async (studentNumber) => {
  try {
    const { data } = await axios(`/residence/${studentNumber}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    console.log(data)

    if (!data.currentResidence) showPrevResidence.value = true

    collective.hostel = data.hostel
    collective.room = data.room
    collective.part = data.part
    collective.floor = data.floor
    collective.floorSide = data.floorSide
    collective.side = data.side

    if (!data.checkedIn) {
      collective.checkedIn = ''
    } else {
      collective.checkedIn = dayjs(data.checkedIn).format('DD MMM YYYY')
    }

    if (!data.checkedOut) {
      collective.checkedOut = ''
    } else {
      collective.checkedOut = dayjs(data.checkedOut).format('DD MMM YYYY')
    }

    //for mobile devices
    collective.fees = formatDollars(data.hostelFees)
    collective.checkIn = dayjs(data.checkIn).format('DD MMM YYYY')
    collective.checkOut = dayjs(data.checkOut).format('DD MMM YYYY')

    isLoading.value = false
  } catch (err) {
    useAxiosError(err, axiosError)
  }
}

fetchResidence(studentNumber)
</script>
