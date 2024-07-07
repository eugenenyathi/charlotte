<template>
  <section class="confirm-mates-container">
    <h2>
      {{ data.requester.fullName }} selected you as part of {{ gender }} roommates, do you consent to
      {{ gender }}
      choice?
    </h2>

    <div class="confirm-mates">
      <div class="st-details" v-for="roommate in data.roommates" :key="roommate.id">
        <router-link :to="{ name: 'Roommate Profile', params: { studentID: roommate.id } }" class="st-name">{{
          roommate.fullName }}</router-link>

        <ButtonIcon v-if="roommate.response === 'Yes'" class="response-icon" icon-name="fa-solid fa-check-circle"
          @mouseover="showResponseMsg(roommate.id, 'Yes')" @mouseleave="removeResponseMsg()" />

        <ButtonIcon v-else-if="roommate.response === 'No'" class="response-icon" icon-name="fa-solid fa-times-circle"
          @mouseover="showResponseMsg(roommate.id, 'No')" @mouseleave="removeResponseMsg()" />

        <ButtonIcon v-else-if="roommate.response === 'Waiting'" class="response-icon" icon-name="fa-solid fa-clock"
          @mouseover="showResponseMsg(roommate.id, 'Waiting')" @mouseleave="removeResponseMsg()" />

        <div :class="{
          'response-message-container': !responseMsg,
          'response-message-container show-response': responseMsg
        }">
          <div class="response-message" v-if="roommateResponse === 'Yes' && activeHoverStudentID === roommate.id">
            Roommate confirmed
          </div>
          <div class="response-message" v-else-if="roommateResponse === 'No' && activeHoverStudentID === roommate.id">
            Roommate declined
          </div>
          <div class="response-message"
            v-else-if="roommateResponse === 'Waiting' && activeHoverStudentID === roommate.id">
            Awaiting response
          </div>
        </div>
      </div>
    </div>

    <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />

    <div class="confirm-cancel-btns">
      <Loader size="small" v-if="isLoading" />
      <BaseButton class="confirm-btn" text="confirm" @btnFn="response('confirm')" :disabled="isLoading" />
      <BaseButton class="cancel-btn" text="cancel" @btnFn="response('cancel')" :disabled="isLoading" />
    </div>
  </section>
</template>

<script setup>
import { BaseButton, ButtonIcon } from '@/base/'
import Loader from '@/components/Loader.vue'
import Alert from '@/components/Alert.vue'
import AlertFn from '@/helpers/AlertFn.js'
import watchFn from '@/helpers/watchFn.js'
import useRequest from '@/composables/useRequest.js'
import SelectionResponse from '@/constants/SelectionResponse'

import { ref, reactive, computed } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const emit = defineEmits(['show'])
const data = computed(() => store.getters.confirmRoommates)
const gender = data.value.requester.gender === 'Female' ? 'her' : 'his'

const responseMsg = ref(false)
const roommateResponse = ref('')
const activeHoverStudentID = ref('')

const alert = reactive({ show: false, msg: 'testing', type: 'success' })
const { showAlert, removeAlert } = AlertFn(alert)
const { watchAxiosErr } = watchFn()

const { isLoading, axiosError, sendResponse } = useRequest()

watchAxiosErr(alert, axiosError)

const showResponseMsg = (roommateID, state = '') => {
  activeHoverStudentID.value = roommateID
  switch (state) {
    case 'Yes':
      responseMsg.value = true
      roommateResponse.value = 'Yes'
      break
    case 'No':
      responseMsg.value = true
      roommateResponse.value = 'No'
      break
    case 'Waiting':
      responseMsg.value = true
      roommateResponse.value = 'Waiting'
      break
    default:
      responseMsg.value = false
      roommateResponse.value = ''
  }
}

const removeResponseMsg = () => {
  responseMsg.value = false
  roommateResponse.value = ''
  activeHoverStudentID.value = ''
}

const response = async (response) => {
  switch (response) {
    case 'confirm':
      await sendResponse(data.value.requester.id, SelectionResponse.YES)

      showAlert(true, `You are now part of ${data.value.requester.fullName}'s roommates`, 'success')
      removeAlert()

      //send event to parent element only if there are no errors
      setTimeout(() => {
        emit('show', 'preferredMates')
      }, 3000)

      break
    case 'cancel':
      await sendResponse(data.value.requester.id, SelectionResponse.NO)

      showAlert(true, 'You can now choose your own roommates', 'success')
      removeAlert()

      setTimeout(() => {
        emit('show', 'searchArea')
      }, 3000)
      break
  }
}
</script>
