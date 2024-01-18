<template>
  <section class="confirm-mates-container">
    <h2>
      {{ requester.fullName }} selected you as part of his roommates, do you consent to
      {{ gender }}
      choice?
    </h2>

    <div class="confirm-mates">
      <div class="st-details" v-for="roomie in roommates" :key="roomie.id">
        <router-link
          :to="{ name: 'RoomieProfile', params: { studentID: roomie.id } }"
          class="st-name"
          >{{ roomie.fullName }}</router-link
        >
        <ButtonIcon
          class="response-icon"
          icon-name="fa-solid fa-check-circle"
          @mouseover="showResponseMsg(roomie.id, 'Yes')"
          @mouseleave="removeResponseMsg()"
          v-if="roomie.response === 'Yes'"
        />
        <ButtonIcon
          class="response-icon"
          icon-name="fa-solid fa-times-circle"
          @mouseover="showResponseMsg(roomie.id, 'No')"
          @mouseleave="removeResponseMsg()"
          v-else-if="roomie.response === 'No'"
        />
        <ButtonIcon
          class="response-icon"
          icon-name="fa-solid fa-clock"
          @mouseover="showResponseMsg(roomie.id, 'Waiting')"
          @mouseleave="removeResponseMsg()"
          v-else-if="roomie.response === 'Waiting'"
        />

        <div
          :class="{
            'response-message-container': !responseMsg,
            'response-message-container show-response': responseMsg
          }"
        >
          <div
            class="response-message"
            v-if="roomieResponse === 'Yes' && activeHoverStudentID === roomie.id"
          >
            Roommate confirmed
          </div>
          <div
            class="response-message"
            v-else-if="roomieResponse === 'No' && activeHoverStudentID === roomie.id"
          >
            Roommate declined
          </div>
          <div
            class="response-message"
            v-else-if="roomieResponse === 'Waiting' && activeHoverStudentID === roomie.id"
          >
            Awaiting response
          </div>
        </div>
      </div>
    </div>

    <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />

    <div class="confirm-cancel-btns">
      <BaseButton
        class="confirm-btn"
        text="confirm"
        @btnFn="response('confirm')"
        :disabled="isLoading"
      />
      <BaseButton
        class="cancel-btn"
        text="cancel"
        @btnFn="response('cancel')"
        :disabled="isLoading"
      />
    </div>
  </section>
</template>

<script setup>
import { BaseButton, ButtonIcon } from '../../base/'
import Alert from '../Alert.vue'
import AlertFn from '../../helpers/AlertFn.js'
import watchFn from '../../helpers/watchFn.js'
import useRequest from '../../composables/useRequest.js'

import { ref, reactive } from 'vue'

const emit = defineEmits(['show'])
const props = defineProps({ requester: Object, roommates: Array })
const gender = props.requester.gender === 'Female' ? 'her' : 'his'

const responseMsg = ref(false)
const roomieResponse = ref('')
const activeHoverStudentID = ref('')

const alert = reactive({ show: false, msg: 'testing', type: 'success' })
const { showAlert, removeAlert } = AlertFn(alert)
const { watchAxiosErr } = watchFn()

const { isLoading, axiosError, sendResponse } = useRequest()

watchAxiosErr(alert, axiosError)

const showResponseMsg = (roomieID, state = '') => {
  activeHoverStudentID.value = roomieID
  switch (state) {
    case 'Yes':
      responseMsg.value = true
      roomieResponse.value = 'Yes'
      break
    case 'No':
      responseMsg.value = true
      roomieResponse.value = 'No'
    case 'Waiting':
      responseMsg.value = true
      roomieResponse.value = 'Waiting'
      break
    default:
      responseMsg.value = false
      roomieResponse.value = ''
  }
}

const removeResponseMsg = () => {
  responseMsg.value = false
  roomieResponse.value = ''
  activeHoverStudentID.value = ''
}

const response = async (response) => {
  switch (response) {
    case 'confirm':
      await sendResponse('yes')

      showAlert(true, `You are now part of ${props.requester.fullName}'s roommates`, 'success')
      removeAlert()

      //send event to parent element only if there are no errors
      setTimeout(() => {
        emit('show', 'preferredMates')
      }, 3000)

      break
    case 'cancel':
      await sendResponse('no')

      showAlert(true, 'You can now choose your own roommates', 'success')
      removeAlert()

      setTimeout(() => {
        emit('show', 'searchArea')
      }, 3000)
      break
  }
}
</script>
