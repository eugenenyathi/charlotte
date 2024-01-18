<template>
  <form class="signup-form set-password" @submit.prevent="handleSubmit">
    <h2 class="signup-header set-password">Set new password</h2>
    <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />
    <div class="signup-control">
      <BaseInput
        :type="pwdType"
        label="New Password"
        class="signup-input"
        placeholder="Password"
        autocomplete="off"
        v-model="password"
      />
      <ButtonIcon
        v-if="!showPassword"
        class="eye-icon"
        iconName="fa-solid fa-eye"
        @btnFn="togglePassword"
      />
      <ButtonIcon
        v-else
        class="eye-icon"
        iconName="fa-solid fa-eye-slash"
        @btnFn="togglePassword"
      />
    </div>
    <button class="signup-btn" :disabled="isLoading">Submit</button>

    <p class="pop-back">
      <ButtonIcon iconName="fa-solid fa-chevron-left" />
      <router-link :to="{ name: 'Login' }">Go back</router-link>
    </p>
  </form>
</template>

<script setup>
import { BaseInput, ButtonIcon } from '../base/'
import Alert from './Alert.vue'
import AlertFn from '../helpers/AlertFn.js'
import watchFn from '../helpers/watchFn.js'

import useSignup from '../composables/useSignup.js'
import isEmpty from '../helpers/isEmpty.js'

import { ref, reactive } from 'vue'
// import { useStore } from "vuex";
// const store = useStore();

const showPassword = ref(false)
const password = ref('')
const pwdType = ref('password')

const alert = reactive({ show: false, msg: '', type: '' })
const { showAlert, removeAlert } = AlertFn(alert)
const { watchAxiosErr } = watchFn()
const { axiosError, isLoading, signup } = useSignup()

const togglePassword = () => {
  showPassword.value = !showPassword.value

  if (pwdType.value === 'password') pwdType.value = 'text'
  else pwdType.value = 'password'
}

watchAxiosErr(alert, axiosError)

const handleSubmit = async () => {
  if (isEmpty(password.value)) {
    showAlert(true, 'Password can not be empty!', 'danger')
    removeAlert()
  } else if (password.value.length < 8) {
    showAlert(true, 'Required minimum password length is 8', 'danger')
    removeAlert()
  } else {
    //send the data to the backend
    await signup(password.value)
  }
}
</script>
