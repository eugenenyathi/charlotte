<template>
  <section class="confirm-mates-container preferred-mates">
    <h2>You have selected these as your preferred room mates.</h2>

    <div class="confirm-mates">
      <div class="st-details" v-for="roomie in preferredMates" :key="roomie.id">
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
  </section>
</template>

<script setup>
import { ButtonIcon } from '../../base/'
import { ref, computed } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const preferredMates = computed(() => store.getters.preferredRoommates)

const responseMsg = ref(false)
const roomieResponse = ref('')
const activeHoverStudentID = ref('')

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
      break
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
</script>
