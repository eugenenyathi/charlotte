<template>
  <section class="confirm-mates-container multi" v-for="singleRequester in requesters">
    <h2>
      {{ singleRequester.requester.fullName }} selected you as part of his roommates, do you
      consent?
    </h2>
    <div class="confirm-mates">
      <div class="st-details" v-for="roomie in singleRequester.roomies" :key="roomie.id">
        <router-link
          :to="{ name: 'RoomieProfile', params: { studentID: roomie.id } }"
          class="st-name"
          >{{ roomie.fullName }}</router-link
        >
        <ButtonIcon
          class="response-icon"
          icon-name="fa-solid fa-check-circle"
          v-if="roomie.response === 'Yes'"
        />
        <ButtonIcon
          class="response-icon"
          icon-name="fa-solid fa-times-circle"
          v-else-if="roomie.response === 'No'"
        />
        <ButtonIcon
          class="response-icon"
          icon-name="fa-solid fa-clock"
          v-else-if="roomie.response === 'Waiting'"
        />
      </div>
    </div>

    <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />

    <div class="confirm-cancel-btns">
      <BaseButton
        class="confirm-btn"
        text="confirm"
        @btnFn="response('confirm', singleRequester.requester.id)"
        :disabled="isLoading"
      />
      <BaseButton
        class="cancel-btn"
        text="cancel"
        @btnFn="response('cancel', singleRequester.requester.id)"
        :disabled="isLoading"
      />
    </div>
  </section>
</template>

//TODO FIX THE BINDING ISSUE

<script setup>
import { BaseButton, ButtonIcon } from '../../base/'
import Alert from '../Alert.vue'
import AlertFn from '../../helpers/AlertFn.js'
import watchFn from '../../helpers/watchFn.js'
import useRequest from '../../composables/useRequest.js'

import { ref, reactive } from 'vue'

const emit = defineEmits(['show'])
const props = defineProps({ multiRequesters: Array })
const requesters = ref([])

requesters.value = props.multiRequesters
// const gender = props.requester.gender === "Female" ? "her" : "his";

const alert = reactive({ show: false, msg: '', type: '' })
const { showAlert, removeAlert } = AlertFn(alert)
const { watchAxiosErr } = watchFn()

const { isLoading, axiosError, sendResponse } = useRequest()

watchAxiosErr(alert, axiosError)

const response = async (response, requester) => {
  // console.log(response, requester);
  switch (response) {
    case 'confirm':
      await sendResponse('yes', requester)

      const fullName = requesterFullName(requester)

      showAlert(true, `You are now part of ${fullName}'s roommates.`, 'success')
      removeAlert()

      //send event to parent element only if there are no errors
      setTimeout(() => {
        emit('show', 'preferredMates')
      }, 3000)

      break
    case 'cancel':
      await sendResponse('no', requester)

      if (requesters.value.length > 1) {
        filterOutRequester(requester)
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

const requesterFullName = (requester) => {
  let name = ''

  requesters.value.forEach((singleRequester) => {
    if (singleRequester.requester.id === requester) {
      name = singleRequester.requester.fullName
    }
  })

  return name
}

const filterOutRequester = (requester) => {
  const newRequesters = requesters.value.filter(
    (singleRequester) => singleRequester.requester.id !== requester
  )
  requesters.value = newRequesters
}
</script>
