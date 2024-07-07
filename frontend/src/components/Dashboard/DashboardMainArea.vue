<template>
  <section class="dashboard-main-area">
    <Loader v-if="isLoading" />
    <div v-auto-animate class="dashboard-main-area-content" v-else>
      <div class="reminder-container">
        <ReminderStatic />
        <ReminderDynamic v-if="showDynamicReminder" />
      </div>
      <!-- TODO: Prefetch all potential roommates -->
      <!-- Search Area -->
      <SearchArea @show="onShow" v-if="showSearchArea" />

      <!-- FindMe Roommates -->
      <FindMeRoommates v-if="showFindMeRoommates" @hideSearchbar="onHideSearchbar" @show="onShow"
        @showSearch="onShowSearch" />

      <!-- SelectedMates -->
      <SelectedMates v-if="showSelectedMates" @close="onClose" @show="onShow" />

      <EditSelectedMates v-if="showEditSelectedMates" @close="onClose" @show="onShow" @cancel="onCancel" />

      <!-- PreferredMates -->
      <PreferredMates v-if="showPreferredMates" @editRoommates="onEditRoommates" />

      <!-- ConfirmMates -->
      <ConfirmMates v-if="showConfirmMates" @show="onShow" />

      <!-- MultiConfirmMates -->
      <MultiConfirmMates v-if="showMultiConfirmMates" @show="onShow" />

      <!-- Roommates -->
      <Roommates :studentName="studentName" v-if="showRoommates" />

    </div>

    <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />
  </section>
</template>

<script setup>

import axios from 'axios'
import ReminderStatic from './ReminderStatic.vue'
import ReminderDynamic from './ReminderDynamic.vue'
import SearchArea from './SearchArea.vue'
import FindMeRoommates from './FindMeRoommates.vue'
import SelectedMates from './SelectedMates.vue'
import EditSelectedMates from './EditSelectedMates.vue'
import PreferredMates from './PreferredMates.vue'
import ConfirmMates from './ConfirmMates.vue'
import MultiConfirmMates from './MultiConfirmMates.vue'
import Roommates from './Roommates.vue'
import Loader from '../Loader.vue'


//composables & helpers
import useAxiosError from '@/composables/useAxiosError.js'
import Alert from '@/components/Alert.vue'
import watchFn from '@/helpers/watchFn.js'

import formatDollars from '@/helpers/formatDollars.js'

import { ref, reactive, computed } from 'vue'
import { useStore } from 'vuex'
import RequestStatus from '@/constants/RequestStatus'


const store = useStore()
const student = computed(() => store.getters.getUser)

const { studentNumber } = student.value

const axiosError = ref(null)
const isLoading = ref(true)

const showSearchArea = ref(false)
const showFindMeRoommates = ref(false)
const showSelectedMates = ref(false)
const showEditSelectedMates = ref(false)
const showPreferredMates = ref(false)
const showConfirmMates = ref(false)
const showMultiConfirmMates = ref(false)
const showRoommates = ref(false)
const showDynamicReminder = ref(false)

const studentName = ref('')

const alert = reactive({ show: false, msg: 'testing', type: 'success' })
const { watchAxiosErr } = watchFn()

const fetchReminder = async (studentNumber) => {
  try {
    const { data: student } = await axios(`dashboard/1/${studentNumber}`)

    let amountOwing = formatDollars(student.tuition - student.amount_cleared)

    let payload = {
      studentName: student.name,
      amountCleared: formatDollars(student.amount_cleared),
      amountOwing: formatDollars(student.tuition - student.amount_cleared),
      fullyCleared: amountOwing ? false : true
    }

    store.dispatch('cacheReminderInfo', payload)
    isLoading.value = false
  } catch (error) {
    useAxiosError(error, axiosError, isLoading)
  }
}

const fetchRequestStatus = async (studentNumber) => {
  try {
    const { data } = await axios(`/request/status/${studentNumber}`)
    isLoading.value = false
    let reminder = ""

    switch (data.status) {
      case RequestStatus.PORTAL_CLOSED:
        showSearchArea.value = false
        reminder = "We are sorry, the portal is not yet open."
        store.dispatch('cacheDynamicReminder', reminder)
        showDynamicReminder.value = true
        break

      case RequestStatus.NOT_REGISTERED:
        showSearchArea.value = false
        reminder = 'Please register for the semester to continue.'
        showDynamicReminder.value = true
        store.dispatch('cacheDynamicReminder', reminder)
        break

      case RequestStatus.ROOMS_ASSIGNED:
        showSearchArea.value = false
        reminder = "We are sorry, rooms have already been allocated."
        store.dispatch('cacheDynamicReminder', reminder)
        showDynamicReminder.value = true
        break
      case RequestStatus.ALLOCATED:
        showSearchArea.value = false
        showRoommates.value = true
        studentName.value = data.name

        reminder = `You have been allocated room ${data.room}, see more in the residence tab.`
        showDynamicReminder.value = true
        store.dispatch('cacheDynamicReminder', reminder)
        store.dispatch('addRoommates', data.roommates)
        break
      case RequestStatus.REQUESTER:
        showSearchArea.value = false
        showPreferredMates.value = true
        store.dispatch('addPreferredRoommates', data.roommates)
        break
      case RequestStatus.NOT_SELECTED:
        showSearchArea.value = true
        showFindMeRoommates.value = true
        break
      case RequestStatus.CONFIRMED:
        showSearchArea.value = false
        showFindMeRoommates.value = false
        store.dispatch('addPreferredRoommates', data.roommates)
        showPreferredMates.value = true
        break
      case RequestStatus.CANCELLED:
        showConfirmMates.value = false
        showSearchArea.value = true
        showFindMeRoommates.value = true
        break
      case RequestStatus.WAITING:
        if (data.type === 'multi') {
          showSearchArea.value = false
          showFindMeRoommates.value = false
          showMultiConfirmMates.value = true
          store.dispatch('multiConfirmRoommates', {
            multiRequesters: data.requesters
          })
        } else {
          showSearchArea.value = false
          showFindMeRoommates.value = false
          showConfirmMates.value = true
          store.dispatch('confirmRoommates', {
            requester: data.requester,
            roommates: data.roommates
          })
        }
        break

      default:
        showSearchArea.value = true
        showFindMeRoommates.value = true
        showMultiConfirmMates.value = false
        showSelectedMates.value = false
        showConfirmMates.value = false
        showPreferredMates.value = false
        showDynamicReminder.value = false
    }
  } catch (error) {
    useAxiosError(error, axiosError, isLoading)
  }
}

fetchReminder(studentNumber)
fetchRequestStatus(studentNumber)


// Emits 
const onShow = (target) => {
  switch (target) {
    case 'selectedMates':
      showSelectedMates.value = true
      break
    case 'preferredMates':
      showSearchArea.value = false
      showFindMeRoommates.value = false
      showConfirmMates.value = false
      showMultiConfirmMates.value = false
      showSelectedMates.value = false
      showEditSelectedMates.value = false
      showPreferredMates.value = true
      break
    case 'searchArea':
      showConfirmMates.value = false
      showMultiConfirmMates.value = false
      showSearchArea.value = true
      showFindMeRoommates.value = true
      break
  }
}

const onClose = (target) => {
  switch (target) {
    case 'selectedMates':
      showSelectedMates.value = false
      break
  }
}

const onHideSearchbar = () => {
  showSearchArea.value = false
}

const onShowSearch = () => {
  showSearchArea.value = true
}

const onEditRoommates = () => {
  showPreferredMates.value = false
  showSearchArea.value = true
  showFindMeRoommates.value = true
  showEditSelectedMates.value = true
}

const onCancel = () => {
  showEditSelectedMates.value = false
  showSearchArea.value = false
  showFindMeRoommates.value = false
  showPreferredMates.value = true
}

watchAxiosErr(alert, axiosError)
</script>
