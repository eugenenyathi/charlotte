<template>
  <main class="signin-container">
    <section class="signin-content">
      <div class="logo-wrapper">
        <img :src="logo" alt="lupane-logo" class="logo-img" />
      </div>
      <form @submit.prevent="handleLogin">
        <div class="signin-control">
          <BaseInput
            :class="{
              'signin-input': !error.stNumber,
              'signin-input error shake': error.stNumber
            }"
            placeholder="Student Number"
            :value="studentNumber"
            @input="updateStNumber($event)"
            autocomplete="off"
          />
        </div>
        <div class="signin-control last-child">
          <BaseInput
            class="signin-input"
            :type="pwdInputType"
            placeholder="Password"
            autocomplete="off"
            v-model="password"
          />
          <ButtonIcon
            v-if="showPassword"
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

        <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />

        <router-link
          to="/reset_password"
          :class="{
            'forgot-password show-alert': alert.show,
            'forgot-password': !alert.show
          }"
        >
          Recover Password
        </router-link>

        <button class="signin-btn" v-if="!isLoading">Sign In</button>
        <button class="signin-btn" v-else>
          <Loader size="small" />
        </button>

        <router-link
          to="/signup"
          :class="{
            'create-account show-alert': alert.show,
            'create-account': !alert.show
          }"
        >
          Create a new account
        </router-link>
      </form>
    </section>
    <footer class="footer">
      <p>Lupane State University &copy; {{ footerYear }}. All Rights Reserved.</p>
    </footer>
  </main>
</template>

<script setup>
//components
import { BaseInput, ButtonIcon } from '../base/'
import Loader from '../components/BtnLoader.vue'
import Alert from '../components/Alert.vue'
//composables
import useLogin from '../composables/useLogin.js'
import useValidator from '../composables/useValidator.js'
//helpers
import getFooterYear from '../helpers/getFooterYear.js'
import watchFn from '../helpers/watchFn.js'

import { ref, reactive, watch } from 'vue'

//assets
import logo from '../assets/lupane.png'

const studentNumber = ref('')
const password = ref('12345678')
const pwdInputType = ref('password')
const showPassword = ref(false)

const footerYear = getFooterYear()

const error = reactive({ stNumber: false, password: false })
const alert = reactive({ show: false, msg: '', type: '' })
const { watchAxiosErr } = watchFn()

const { validateStNumber, validatePassword } = useValidator()
const { axiosError, isLoading, login } = useLogin()

const updateStNumber = (event) => {
  studentNumber.value = event.target.value.toUpperCase()
}
//low-grey -f2f2f2

const togglePassword = () => {
  showPassword.value = !showPassword.value

  if (pwdInputType.value === 'password') pwdInputType.value = 'text'
  else pwdInputType.value = 'password'
}

watch(
  () => {
    return { ...error }
  },
  (newValue, oldValue) => {
    if (newValue.stNumber || newValue.password) {
      reset()
    }
  }
)

watchAxiosErr(alert, axiosError)

const handleLogin = async () => {
  if (!validateStNumber(studentNumber.value)) {
    error.stNumber = true
  } else if (!validatePassword(password.value)) {
    error.password = true
  } else {
    //if there are no errors, well let's proceed!
    await login(studentNumber.value, password.value)
  }
}

const reset = () => {
  const clear = setTimeout(() => {
    error.stNumber = false
    error.password = false
  }, 3000)

  return () => clearTimeout(clear)
}
</script>
