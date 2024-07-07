<template>
  <section class="confirm-mates-container preferred-mates">
    <h2>You have selected these as your preferred room mates.</h2>

    <div v-auto-animate class="confirm-mates">
      <div class="st-details" v-for="roommate in preferredMates" :key="roommate.id">
        <router-link :to="{ name: 'Roommate Profile', params: { studentID: roommate.id } }" class="st-name">{{
          roommate.fullName }}</router-link>
        <ButtonIcon class="response-icon" icon-name="fa-solid fa-check-circle"
          @mouseover="showResponseMsg(roommate.id, 'Yes')" @mouseleave="removeResponseMsg()"
          v-if="roommate.response === 'Yes'" />
        <ButtonIcon class="response-icon" icon-name="fa-solid fa-times-circle"
          @mouseover="showResponseMsg(roommate.id, 'No')" @mouseleave="removeResponseMsg()"
          v-else-if="roommate.response === 'No'" />
        <ButtonIcon class="response-icon" icon-name="fa-solid fa-clock"
          @mouseover="showResponseMsg(roommate.id, 'Waiting')" @mouseleave="removeResponseMsg()"
          v-else-if="roommate.response === 'Waiting'" />

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

    <h1 @click="editSelection"
      class="text-sm text-neutral-500 cursor-pointer hover:underline hover:text-black transition my-4">Edit Selection
    </h1>
  </section>
</template>

<script setup>
import { ButtonIcon } from '@/base/'
import { ref, computed } from 'vue'
import { useStore } from 'vuex'

const emit = defineEmits(['editRoommates'])

const store = useStore()
const preferredMates = computed(() => store.getters.preferredRoommates)

const responseMsg = ref(false)
const roommateResponse = ref('')
const activeHoverStudentID = ref('')

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

const editSelection = () => {
  //copy the preferred roommates to the selectedMates array
  store.dispatch('editSelection')
  emit('editRoommates')
}
</script>
