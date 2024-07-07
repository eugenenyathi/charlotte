<template>
  <section class="confirm-mates-container multi" v-for="singleRequester in requesters"
    :key="singleRequester.requester.fullName">
    <h2>
      {{ singleRequester.requester.fullName }} selected you as part of {{ gender(singleRequester.requester.gender) }}
      roommates, do you
      consent?
    </h2>
    <div class="confirm-mates">
      <div class="st-details" v-for="roommate in singleRequester.roommates" :key="roommate.id">
        <router-link :to="{ name: 'RoommateProfile', params: { studentID: roommate.id } }" class="st-name">{{
    roommate.fullName }}
        </router-link>
        <ButtonIcon class="response-icon" icon-name="fa-solid fa-check-circle"
          v-if="roommate.response === SelectionResponse.YES" />
        <ButtonIcon class="response-icon" icon-name="fa-solid fa-times-circle"
          v-else-if="roommate.response === SelectionResponse.NO" />
        <ButtonIcon class="response-icon" icon-name="fa-solid fa-clock"
          v-else-if="roommate.response === SelectionResponse.WAITING" />
      </div>
    </div>

    <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />

    <div class="confirm-cancel-btns">
      <BaseButton class="confirm-btn" text="confirm"
        @btnFn="response(SelectionResponse.CONFIRM, singleRequester.requester.id)" :disabled="isLoading" />
      <BaseButton class="cancel-btn" text="cancel"
        @btnFn="response(SelectionResponse.CANCEL, singleRequester.requester.id)" :disabled="isLoading" />
    </div>
  </section>
</template>

<script setup>
import { BaseButton, ButtonIcon } from '@/base/'
import Alert from '@/components/Alert.vue'
import AlertFn from '@/helpers/AlertFn.js'
import watchFn from '@/helpers/watchFn.js'
import useRequest from '@/composables/useRequest.js'
import SelectionResponse from '@/constants/SelectionResponse'

import { ref, reactive, computed } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const emit = defineEmits(['show'])
const data = computed(() => store.getters.multiConfirmMates)
const requesters = ref([])

requesters.value = data.value.multiRequesters


const alert = reactive({ show: false, msg: '', type: '' })
const { showAlert, removeAlert } = AlertFn(alert)
const { watchAxiosErr } = watchFn()

const { isLoading, axiosError, sendResponse } = useRequest()

watchAxiosErr(alert, axiosError)

const response = async (response, requesterId) => {
  // console.log(response, requester);
  switch (response) {
    case SelectionResponse.CONFIRM:
      await sendResponse(requesterId, SelectionResponse.YES)

      showAlert(true, `You are now part of ${requesterFullName(requesterId)}'s roommates.`, 'success')
      removeAlert()

      //send event to parent element only if there are no errors
      setTimeout(() => {
        emit('show', 'preferredMates')
      }, 3000)

      break
    case SelectionResponse.CANCEL:
      await sendResponse(requesterId, SelectionResponse.NO)

      if (requesters.value.length > 1) {
        filterOutRequester(requesterId)
      } else {
        showAlert(true, `You can now choose your own roommates.`, 'success')
        removeAlert()

        setTimeout(() => {
          emit('show', 'searchArea')
        }, 3000)
      }

      break
  }
}

const requesterFullName = (requesterId) => {
  let name = ''

  requesters.value.forEach((singleRequester) => {
    if (singleRequester.requester.id === requesterId) {
      name = singleRequester.requester.fullName
    }
  })

  return name
}

const gender = (requesterGender) => {
  return requesterGender === "Female" ? "her" : "his";
}

const filterOutRequester = (requesterId) => {
  const newRequesters = requesters.value.filter(
    (singleRequester) => singleRequester.requester.id !== requesterId
  )
  requesters.value = newRequesters
}
</script>
