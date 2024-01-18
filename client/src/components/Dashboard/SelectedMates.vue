<template>
  <section class="selected-mates-container" v-if="selectedMates.length !== 0">
    <h2>You have added the following as your preferred roommates.</h2>
    <ul class="selected-ul-wrapper">
      <li class="list-item" v-for="student in selectedMates" :key="student.id">
        {{ student.fullName }}
        <ButtonIcon
          class="remove-roommate-btn"
          icon-name="fa-regular fa-trash-alt"
          @btnFn="removeStudent(student.id)"
        />
      </li>
    </ul>

    <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />

    <BaseButton
      class="request-room-btn"
      text="Request Room"
      @btnFn="requestRoom"
      v-if="!isLoading"
    />
    <button class="request-room-btn" v-else>
      Request Room
      <Loader size="tiny" />
    </button>
  </section>
</template>

<script setup>
import { BaseButton, ButtonIcon } from '../../base/'
import Loader from '../../components/BtnLoader.vue'

import Alert from '../Alert.vue'
import AlertFn from '../../helpers/AlertFn.js'
import watchFn from '../../helpers/watchFn.js'
import useRequest from '../../composables/useRequest.js'

import { reactive, computed, watch } from 'vue'
import { useStore } from 'vuex'

const emit = defineEmits(['close', 'show'])
const store = useStore()
const selectedMates = computed(() => store.getters.selectedStudentsInfo)

const alert = reactive({ show: false, msg: 'testing', type: 'success' })
const { showAlert, removeAlert } = AlertFn(alert)
const { watchAxiosErr } = watchFn()

const { isLoading, axiosError, createRequest } = useRequest()

watch(selectedMates, (newValue, oldValue) => {
  if (newValue.length === 0) {
    emit('close', 'selectedMates')
  }
})

const removeStudent = (studentNumber) => {
  store.dispatch('removeSelectedStudent', studentNumber)
}

watchAxiosErr(alert, axiosError)

const requestRoom = async () => {
  const data = await createRequest()

  // console.log(data);
  switch (data.status) {
    case 'success':
      requestSuccessful(data)
      break
    case 'failed':
      requestFailed(data)
      break
  }
}

const requestSuccessful = (data) => {
  console.log(data)

  store.dispatch('addPreferredRoommates', data.roomies)

  showAlert(true, 'Your request was successful.', 'success')
  removeAlert()

  //send event to parent element only if there are no errors
  setTimeout(() => {
    emit('show', 'preferredMates')
  }, 3000)
}

const requestFailed = (data) => {
  data.candidates.forEach((candidate) => removeStudent(candidate.id))

  showAlert(true, 'taken student(s) removed, please add new roommates.', 'success')
  removeAlert()
}
</script>
