<template>
  <section class="dashboard-main-area">
    <Loader v-if="isLoading" />
    <div class="dashboard-main-area-content" v-else>
      <div class="reminder-container">
        <ReminderStatic
          :fullyCleared="fullyCleared"
          :studentName="reminderInfo.name"
          :amountCleared="reminderInfo.amountCleared"
          :amountOwing="reminderInfo.amountOwing"
        />
        <ReminderDynamic :reminders="reminders" v-if="!searchResults" />
      </div>
      <!-- Search Area -->
      <SearchArea @show="show" v-if="showSearchArea" />

      <!-- SelectedMates -->
      <SelectedMates v-if="showSelectedMates" @close="close" @show="show" />

      <!-- PreferredMates -->
      <PreferredMates v-if="showPreferredMates" />

      <!-- ConfirmMates -->
      <ConfirmMates
        v-if="showConfirmMates"
        :requester="requester"
        :roommates="roommates"
        @show="show"
      />

      <!-- MultiConfirmMates -->
      <MultiConfirmMates
        v-if="showMultiConfirmMates"
        :multiRequesters="multiRequesters"
        @show="show"
      />

      <!-- Roommates -->
      <Roommates :myname="myname" v-if="showRoommates" />

      <!-- Mobile-size Footer -->
      <footer class="mobile-size-footer">
        <p>&copy; {{ getYear }} mylsu from eugene nyathi</p>
      </footer>
    </div>
  </section>
</template>

<script setup>
import axios from 'axios'
import Loader from '../Loader.vue'
import ReminderStatic from './ReminderStatic.vue'
import ReminderDynamic from './ReminderDynamic.vue'
import SearchArea from './SearchArea.vue'
import SelectedMates from './SelectedMates.vue'
import PreferredMates from './PreferredMates.vue'
import ConfirmMates from './ConfirmMates.vue'
import MultiConfirmMates from './MultiConfirmMates.vue'
import Roommates from './Roommates.vue'

//composables & helpers
import useAxiosError from '../../composables/useAxiosError.js'
import useToken from '../../composables/useToken.js'
import getFooterYear from '../../helpers/getFooterYear.js'
import formatDollars from '../../helpers/formatDollars.js'

import { ref, reactive, computed } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const student = computed(() => store.getters.getUser)
const searchResults = computed(() => store.getters.searchResults)

const { studentNumber } = student.value

const axiosError = ref(null)
const isLoading = ref(true)

const fullyCleared = ref(false)
const reminderInfo = reactive({
  name: '',
  amountCleared: null,
  amountOwing: null
})
const getYear = getFooterYear()

//!!!!!!!!! --------- REMOVE THIS ITS FOR TESTING --------- !!!!!!
const showSearchArea = ref(false)
const showSelectedMates = ref(false)
const showPreferredMates = ref(false)
const showConfirmMates = ref(false)
const showMultiConfirmMates = ref(false)
const showRoommates = ref(false)

const myname = ref('')
const requester = ref()
const roommates = ref([])
const multiRequesters = ref([])

const active = ref(false)
const reminders = ref(['The system defaults to randomized room mates if data is insufficient. '])

const token = useToken()
const fetchReminder = async (studentNumber) => {
  try {
    const { data: student } = await axios(`dashboard/1/${studentNumber}`, {
      headers: {
        Authorization: `Bearer ${token}`
      }
    })

    reminderInfo.name = student.name
    reminderInfo.amountCleared = formatDollars(student.amount_cleared)
    reminderInfo.amountOwing = formatDollars(student.tuition - student.amount_cleared)

    if (!reminderInfo.amountOwing) {
      fullyCleared.value = true
    }

    if (!student.registered) {
      reminders.value = []
      reminders.value.push('Please register for the semester to continue.')
      active.value = false
    }

    isLoading.value = false
  } catch (err) {
    useAxiosError(err, axiosError)
  }
}

const fetchRequestStatus = async (studentNumber) => {
  const { data } = await axios(`/request/status/${studentNumber}`, {
    headers: {
      Authorization: `Bearer ${token}`
    }
  })

  switch (data.status) {
    case 'portalClosed':
      showSearchArea.value = false

      reminders.value = []
      reminders.value.push(`We are sorry, the portal is not yet open.`)
      break
    case 'roomsAssigned':
      showSearchArea.value = false

      reminders.value = []
      reminders.value.push(`We are sorry, rooms have already been allocated.`)
      break
    case 'allocated':
      showSearchArea.value = false
      showRoommates.value = true
      myname.value = data.name

      reminders.value = []
      reminders.value.push(
        `You have been allocated room ${data.room}, see more in the residence tab.`
      )
      store.dispatch('addRoommates', data.roomies)
      break
    case 'requester':
      showSearchArea.value = false
      showPreferredMates.value = true
      store.dispatch('addPreferredRoommates', data.roomies)
      break
    case 'clean':
      showSearchArea.value = true
      break
    case 'confirmed':
      showSearchArea.value = false
      store.dispatch('addPreferredRoommates', data.roomies)
      showPreferredMates.value = true
      break
    case 'cancelled':
      showConfirmMates.value = false
      showSearchArea.value = true
      break
    case 'waiting':
      if (data.type === 'multi') {
        showSearchArea.value = false
        showMultiConfirmMates.value = true
        multiRequesters.value = data.requesters
      } else {
        showSearchArea.value = false
        showConfirmMates.value = true
        requester.value = data.requester
        roommates.value = data.roomies
      }
      break

    default:
      showSearchArea.value = true
      showMultiConfirmMates.value = false
      showSelectedMates.value = false
      showConfirmMates.value = false
      showPreferredMates.value = false
  }
}

fetchReminder(studentNumber)
fetchRequestStatus(studentNumber)

const show = (target) => {
  switch (target) {
    case 'selectedMates':
      showSelectedMates.value = true
      break
    case 'preferredMates':
      showSearchArea.value = false
      showConfirmMates.value = false
      showMultiConfirmMates.value = false
      showSelectedMates.value = false
      showPreferredMates.value = true
      break
    case 'searchArea':
      showConfirmMates.value = false
      showMultiConfirmMates.value = false
      showSearchArea.value = true
      break
  }
}

const close = (target) => {
  switch (target) {
    case 'selectedMates':
      showSelectedMates.value = false
      break
  }
}
</script>
