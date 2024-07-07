<template>
  <form class="signup-form personal-info" @submit.prevent="handleSubmit">
    <h2
      :class="{
        'signup-header': !alert.show,
        'signup-header show-alert': alert.show
      }"
    >
      Hello, Signup here!
    </h2>
    <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />
    <div class="signup-control">
      <label for="studentNumber">Student Number</label>
      <input
        type="text"
        :class="{
          'signup-input': !error.studentNumber,
          'signup-input error shake': error.studentNumber
        }"
        id="studentNumber"
        :value="studentNumber"
        @input="capitalize($event, 'studentNumber')"
        autocomplete="off"
      />
    </div>
    <div class="signup-control">
      <label for="nationalId">National ID</label>
      <input
        type="text"
        :class="{
          'signup-input': !error.nationalId,
          'signup-input error shake': error.nationalId
        }"
        id="nationalId"
        :value="nationalId"
        @input="capitalize($event, 'nat')"
        placeholder="e.g. 00-123456Q78"
        autocomplete="off"
      />
    </div>
    <button class="signup-btn" :disabled="isLoading">Next</button>
    <p class="pop-back">
      <ButtonIcon iconName="fa-solid fa-chevron-left" />
      <router-link :to="{ name: 'Login' }">Go back</router-link>
    </p>
  </form>
</template>

<script setup>
import { ButtonIcon } from '../base/'
import Alert from '../components/Alert.vue'
import AlertFn from '../helpers/AlertFn.js'
import watchFn from '../helpers/watchFn.js'
//composables
import useValidator from '../composables/useValidator.js'
import useSignup from '../composables/useSignup.js'

import { ref, reactive } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const studentNumber = ref('')
const nationalId = ref('')
const error = reactive({
  studentNumber: false,
  nationalId: false
})
const alert = reactive({ show: false, msg: '', type: '' })
const { showAlert, removeAlert } = AlertFn(alert)
const { watchAxiosErr } = watchFn()

const { validateStNumber, validateNationalID } = useValidator()
const { axiosError, isLoading, verifyIdentity } = useSignup()

const emit = defineEmits(['signal'])

watchAxiosErr(alert, axiosError)

const capitalize = (event, target = 'studentNumber') => {
  if (target === 'studentNumber') studentNumber.value = event.target.value.toUpperCase()
  else nationalId.value = event.target.value.toUpperCase()
}

const handleSubmit = async () => {
  //validate inputs
  if (!validateStNumber(studentNumber.value)) {
    error.studentNumber = true
    console.log('error -st')
  } else if (!validateNationalID(nationalId.value)) {
    error.nationalId = true
    console.log('error -nat')
  } else {
    //send the data to the backend
    const user = await verifyIdentity(studentNumber.value, nationalId.value)

    if (!user) return

    //set studentNumber as global state
    store.dispatch('setStudentID', studentNumber.value)

    showAlert(true, 'Credentials verified successfully.', 'success')

    //send event to parent element only if there are no errors
    setTimeout(() => {
      emit('signal')
    }, 3000)
  }
}
</script>
