<template>
  <form class="reset-form personal-info" @submit.prevent="handleSubmit">
    <h2
      :class="{
        'reset-header': !alert.show,
        'reset-header show-alert': alert.show
      }"
    >
      Let's start here!
    </h2>
    <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />
    <div class="reset-control">
      <label for="studentNumber">Student Number</label>
      <input
        type="text"
        :class="{
          'reset-input': !error.studentNumber,
          'reset-input error shake': error.studentNumber
        }"
        id="studentNumber"
        :value="studentNumber"
        @input="capitalize($event, 'studentNumber')"
        autocomplete="off"
      />
    </div>
    <div class="reset-control">
      <label for="nationalId">National ID</label>
      <input
        type="text"
        :class="{
          'reset-input': !error.nationalId,
          'reset-input error shake': error.nationalId
        }"
        id="nationalId"
        :value="nationalId"
        @input="capitalize($event, 'nat')"
        placeholder="e.g. 00-123456Q78"
        autocomplete="off"
      />
    </div>
    <div class="reset-control">
      <label for="dob">Date of Birth</label>
      <input
        type="text"
        :class="{
          'reset-input': !error.dob,
          'reset-input error shake': error.dob
        }"
        id="dob"
        placeholder="e.g. 2005-02-24"
        v-model="dob"
        autocomplete="off"
      />
    </div>
    <button class="reset-btn" :disabled="isLoading">Verify</button>
    <p class="pop-back">
      <ButtonIcon iconName="fa-solid fa-chevron-left" />
      <router-link :to="{ name: 'Login' }" class="pop-back-link">Go back</router-link>
    </p>
  </form>
</template>

<script setup>
import { ButtonIcon } from '../base/'
import Alert from '../components/Alert.vue'
import AlertFn from '../helpers/AlertFn.js'
//composables
import useValidator from '../composables/useValidator.js'
import useResetPassword from '../composables/useResetPassword.js'

import { ref, reactive, watch } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const studentNumber = ref('')
const nationalId = ref('')
const dob = ref('')
const alert = reactive({ show: false, msg: '', type: '' })
const { showAlert, removeAlert } = AlertFn(alert)

const error = reactive({
  studentNumber: false,
  nationalId: false,
  dob: false
})
const { validateStNumber, validateNationalID, validateDob } = useValidator()
const { axiosError, isLoading, verifyIdentity } = useResetPassword()

const emit = defineEmits(['signal'])

watch(axiosError, (currentValue, oldValue) => {
  if (currentValue) {
    showAlert(true, currentValue, 'danger')
    removeAlert()
  }

  axiosError.value = ''
})

const capitalize = (event, target = 'studentNumber') => {
  if (target === 'studentNumber') studentNumber.value = event.target.value.toUpperCase()
  else nationalId.value = event.target.value.toUpperCase()
}

const handleSubmit = async () => {
  //validate inputs
  if (!validateStNumber(studentNumber.value)) {
    error.studentNumber = true
    // console.log("error -st");
  } else if (!validateNationalID(nationalId.value)) {
    error.nationalId = true
    // console.log("error -nat");
  } else if (!validateDob(dob.value)) {
    error.dob = true
    // console.log("error -dob");
  } else {
    //send the data to the backend
    const user = await verifyIdentity(studentNumber.value, nationalId.value, dob.value)

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
//Trust 0782504742
</script>
