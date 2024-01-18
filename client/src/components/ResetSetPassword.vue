<template>
  <form class="reset-form set-password" @submit.prevent="handleSubmit">
    <h2
      :class="{
        'reset-header': !alert.show,
        'reset-header show-alert': alert.show
      }"
    >
      Set new password
    </h2>
    <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />
    <div class="reset-control">
      <BaseInput
        :type="pwdType.firstField"
        label="New Password"
        class="reset-input"
        autocomplete="off"
        placeholder="Password"
        v-model="password"
      />
      <ButtonIcon
        v-if="!showPassword.password"
        class="eye-icon"
        iconName="fa-solid fa-eye"
        @btnFn="togglePassword('first')"
      />
      <ButtonIcon
        v-else
        class="eye-icon"
        iconName="fa-solid fa-eye-slash"
        @btnFn="togglePassword('first')"
      />
    </div>
    <div class="reset-control">
      <BaseInput
        :type="pwdType.secondField"
        label="Confirm Password"
        class="reset-input"
        autocomplete="off"
        placeholder="Password"
        v-model="confirmPassword"
      />
      <ButtonIcon
        v-if="!showPassword.confirmPassword"
        class="eye-icon"
        iconName="fa-solid fa-eye"
        @btnFn="togglePassword('second')"
      />
      <ButtonIcon
        v-else
        class="eye-icon"
        iconName="fa-solid fa-eye-slash"
        @btnFn="togglePassword('second')"
      />
    </div>
    <button class="reset-btn" :disabled="isLoading">Submit</button>

    <p class="pop-back">
      <ButtonIcon iconName="fa-solid fa-chevron-left" />
      <router-link :to="{ name: 'Login' }">Go back</router-link>
    </p>
  </form>
</template>

<script setup>
import { BaseInput, ButtonIcon } from '../base/'

import Alert from '../components/Alert.vue'
import AlertFn from '../helpers/AlertFn.js'
import watchFn from '../helpers/watchFn.js'

import useResetPassword from '../composables/useResetPassword.js'
import isEmpty from '../helpers/isEmpty.js'

import { ref, reactive } from 'vue'

const password = ref('')
const confirmPassword = ref('')
const pwdType = reactive({ firstField: 'password', secondField: 'password' })
const showPassword = reactive({ password: false, confirmPassword: false })

const alert = reactive({ show: false, msg: '', type: '' })
const { showAlert, removeAlert } = AlertFn(alert)
const { watchAxiosErr } = watchFn()
const { axiosError, isLoading, resetPwd } = useResetPassword()

const togglePassword = (field) => {
  if (field === 'first') {
    if (pwdType.firstField === 'password') {
      showPassword.password = true
      pwdType.firstField = 'text'
    } else {
      showPassword.password = false
      pwdType.firstField = 'password'
    }
  } else {
    if (pwdType.secondField === 'password') {
      showPassword.confirmPassword = true
      pwdType.secondField = 'text'
    } else {
      showPassword.confirmPassword = false
      pwdType.secondField = 'password'
    }
  }
}

watchAxiosErr(alert, axiosError)

const handleSubmit = async () => {
  if (isEmpty(password.value) || isEmpty(confirmPassword.value)) {
    showAlert(true, 'Please fill in all fields!', 'danger')
    removeAlert()
  } else if (password.value.length < 8 || confirmPassword.value.length < 8) {
    showAlert(true, 'Required minimum password length is 8', 'danger')
    removeAlert()
  } else if (password.value !== confirmPassword.value) {
    showAlert(true, 'Passwords do not match.', 'danger')
    removeAlert()
  } else {
    //send the data to the backend
    await resetPwd(password.value)
  }
}
</script>
