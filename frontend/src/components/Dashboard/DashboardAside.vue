<template>
  <aside class="dashboard-aside">
    <div class="dashboard-aside-content">
      <div class="accomodation-fees">
        <p>
          <ButtonIcon class="aside-icon" icon-name="fa-solid fa-money-bill-alt" />
          Accomodation fees
        </p>
        <p>{{ collective.fees }}</p>
      </div>

      <div class="check-in-out">
        <p>
          <ButtonIcon class="aside-icon" icon-name="fa-solid fa-door-closed" />
          Check In
        </p>
        <p>{{ collective.checkIn }}</p>
      </div>
      <div class="check-in-out">
        <p>
          <ButtonIcon class="aside-icon" icon-name="fa-solid fa-door-open" />
          Check Out
        </p>
        <p>{{ collective.checkOut }}</p>
      </div>

      <div class="last-login">
        <p>
          <ButtonIcon class="aside-icon" icon-name="fa-solid fa-clock" />
          Last Login
        </p>

        <p>{{ collective.lastLogin }}</p>
      </div>
    </div>

    <footer class="aside-footer">
      <p>&copy; {{ getYear }} eugene nyathi</p>
    </footer>
  </aside>
</template>

<script setup>
//components
import { ButtonIcon } from '@/base/'

//composables & helpers
import useAxiosError from '@/composables/useAxiosError.js'

import getFooterYear from '@/helpers/getFooterYear.js'
import dateFns from '@/helpers/dateFns.js'
import formatDollars from '@/helpers/formatDollars.js'

import axios from 'axios'
import dayjs from 'dayjs'

//vue
import { ref, reactive, computed } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const student = computed(() => store.getters.getUser)
const { studentNumber } = student.value

const getYear = getFooterYear()
const axiosError = ref('')
const isLoading = ref(true)
const collective = reactive({
  fees: '',
  checkIn: '',
  checkOut: '',
  lastLogin: ''
})


const fetchData = async (studentNumber) => {
  try {
    const { data } = await axios(`dashboard/2/${studentNumber}`)

    collective.fees = formatDollars(data.hostelFees)
    collective.checkIn = dayjs(data.checkIn).format('DD MMM YYYY')
    collective.checkOut = dayjs(data.checkOut).format('DD MMM YYYY')

    if (!data.timestamp) collective.lastLogin = '1st time login :)'
    else collective.lastLogin = dateFns(data.timestamp)

    isLoading.value = false
  } catch (err) {
    useAxiosError(err, axiosError, isLoading)
  }
}

fetchData(studentNumber)
</script>
