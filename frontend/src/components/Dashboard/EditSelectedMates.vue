<template>
  <section class="selected-mates-container" v-if="selectedMates.length !== 0">
    <h2>You have added the following as your preferred roommates.</h2>
    <ul v-auto-animate class="selected-ul-wrapper">
      <li class="list-item" v-for="student in selectedMates" :key="student.id">
        {{ student.fullName }}
        <ButtonIcon class="remove-roommate-btn" icon-name="fa-regular fa-trash-alt"
          @btnFn="removeStudent(student.id)" />
      </li>
    </ul>

    <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />

    <div class="flex items-center gap-x-4">
      <BaseButton class="request-room-btn" text="Update Request" @btnFn="updateRoomRequest" v-if="!isLoading" />
      <button class="request-room-btn" v-else>
        Update Request
        <Loader size="tiny" />
      </button>

      <h1 @click="$emit('cancel')"
        class="text-sm text-neutral-500 cursor-pointer hover:underline hover:text-black transition my-4">
        Cancel
      </h1>
    </div>

  </section>
</template>

<script setup>
import { BaseButton, ButtonIcon } from '@/base/'
import Loader from '@/components/BtnLoader.vue'

import Alert from '../Alert.vue'
import AlertFn from '@/helpers/AlertFn.js'
import watchFn from '@/helpers/watchFn.js'
import useRequest from '@/composables/useRequest.js'

import { reactive, computed, watch } from 'vue'
import { useStore } from 'vuex'

const emit = defineEmits(['close', 'show', 'cancel'])
const store = useStore()
const selectedMates = computed(() => store.getters.selectedStudentsInfo)

const alert = reactive({ show: false, msg: 'testing', type: 'success' })
const { showAlert, removeAlert } = AlertFn(alert)
const { watchAxiosErr } = watchFn()

const { isLoading, axiosSuccess, axiosError, updateRequest } = useRequest()

watch(selectedMates, (newValue, oldValue) => {
  if (newValue.length === 0) {
    emit('close', 'editSelectedMates')
  }
})

const removeStudent = (studentNumber) => {
  store.dispatch('removeSelectedStudent', studentNumber)
}

const updateRoomRequest = async () => {
  await updateRequest();

  if (axiosSuccess.value) {
    showAlert(true, 'Room request was successful updated.', 'success')
    removeAlert()

    setTimeout(() => {
      emit('show', 'preferredMates')
    }, 3000)
  }
}


watchAxiosErr(alert, axiosError)


</script>
