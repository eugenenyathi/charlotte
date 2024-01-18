<template>
  <div class="new-password">
    <h2>Choose a fresh new password.</h2>

    <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />

    <form class="new-password-form" @submit.prevent="handleSubmit">
      <div class="settings-control">
        <BaseInput
          :type="pwdType.password"
          label="New Password"
          class="settings-input"
          placeholder="Password"
          v-model="password"
        />
        <ButtonIcon
          v-if="showPassword.password"
          class="eye-icon"
          iconName="fa-solid fa-eye"
          @btnFn="togglePassword('password')"
        />
        <ButtonIcon
          v-else
          class="eye-icon"
          iconName="fa-solid fa-eye-slash"
          @btnFn="togglePassword('password')"
        />
      </div>
      <div class="settings-control">
        <BaseInput
          :type="pwdType.confirmPassword"
          label="Confirm Password"
          class="settings-input"
          placeholder="Confirm Password"
          v-model="confirmPassword"
        />
        <ButtonIcon
          v-if="showPassword.confirmPassword"
          class="eye-icon"
          iconName="fa-solid fa-eye"
          @btnFn="togglePassword('confirmPassword')"
        />
        <ButtonIcon
          v-else
          class="eye-icon"
          iconName="fa-solid fa-eye-slash"
          @btnFn="togglePassword('confirmPassword')"
        />
      </div>
      <button class="settings-btn new-password" :disabled="isLoading">Save</button>
    </form>

    <p class="pop-back settings" @click="$emit('pop')">
      <ButtonIcon iconName="fa-solid fa-chevron-left" />
      Go back
    </p>
  </div>
</template>

<script setup>
import { BaseInput, ButtonIcon } from '../base/'

import Alert from '../components/Alert.vue'
import AlertFn from '../helpers/AlertFn.js'
import watchFn from '../helpers/watchFn.js'
import isEmpty from '../helpers/isEmpty.js'
import useResetPassword from '../composables/useResetPassword.js'

import { ref, reactive } from 'vue'

const password = ref('')
const confirmPassword = ref('')
const pwdType = reactive({
  password: 'password',
  confirmPassword: 'password'
})
const showPassword = reactive({
  password: false,
  confirmPassword: false
})

const emit = defineEmits(['pop'])

const alert = reactive({ show: false, msg: '', type: '' })
const { showAlert, removeAlert } = AlertFn(alert)
const { watchAxiosErr } = watchFn()
const { axiosError, isLoading, updatePassword } = useResetPassword()

const togglePassword = (field) => {
  if (field === 'password') {
    if (pwdType.password === 'password') {
      showPassword.password = true
      pwdType.password = 'text'
    } else {
      showPassword.password = false
      pwdType.password = 'password'
    }
  } else {
    if (pwdType.confirmPassword === 'password') {
      showPassword.confirmPassword = true
      pwdType.confirmPassword = 'text'
    } else {
      showPassword.confirmPassword = false
      pwdType.confirmPassword = 'password'
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
    await updatePassword(password.value)

    showAlert(true, 'Password updated successfully!', 'success')

    //send event to parent element only if there are no errors
    setTimeout(() => {
      emit('pop')
    }, 3000)
  }
}
</script>
