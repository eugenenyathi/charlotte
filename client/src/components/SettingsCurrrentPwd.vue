<template>
  <div class="current-password">
    <h2>To change your password, please provide the current password.</h2>

    <form @submit.prevent="handleSubmit">
      <div class="settings-control">
        <BaseInput
          :type="pwdType"
          label="Current Password"
          class="settings-input"
          placeholder="Password"
          v-model="currentPassword"
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

      <button class="settings-btn" :disabled="isLoading">Verify</button>
    </form>
  </div>
</template>

<script setup>
import { BaseInput, ButtonIcon } from '../base/'

import useToken from '../composables/useToken.js'
import Alert from '../components/Alert.vue'
import AlertFn from '../helpers/AlertFn.js'
import watchFn from '../helpers/watchFn.js'
import isEmpty from '../helpers/isEmpty.js'
import useAxiosError from '../composables/useAxiosError.js'
import axios from 'axios'

import { ref, reactive, computed } from 'vue'
import { useStore } from 'vuex'

const store = useStore()
const student = computed(() => store.getters.getUser)
const { studentNumber } = student.value

const emit = defineEmits(['next'])

const currentPassword = ref('12345678')
const pwdType = ref('password')
const showPassword = ref(false)

const axiosError = ref('')
const isLoading = ref(false)

const alert = reactive({ show: false, msg: 'testing', type: 'success' })
const { showAlert, removeAlert } = AlertFn(alert)
const { watchAxiosErr } = watchFn()

const togglePassword = () => {
  showPassword.value = !showPassword.value

  if (pwdType.value === 'password') pwdType.value = 'text'
  else pwdType.value = 'password'
}

watchAxiosErr(alert, axiosError)

const token = useToken()

const handleSubmit = async () => {
  if (isEmpty(currentPassword.value)) {
    showAlert(true, 'Please fill in all fields!', 'danger')
    removeAlert()
  } else if (currentPassword.value.length < 8) {
    showAlert(true, 'Incorrect password!', 'danger')
    removeAlert()
  } else {
    try {
      isLoading.value = true
      const res = await axios.post(
        '/password/verify',
        {
          studentID: studentNumber,
          password: currentPassword.value
        },
        {
          headers: {
            Authorization: `Bearer ${token}`
          }
        }
      )

      showAlert(true, res.data.message, 'success')

      //send event to parent element only if there are no errors
      setTimeout(() => {
        emit('next')
      }, 3000)
    } catch (err) {
      useAxiosError(err, axiosError, isLoading)
    }
  }
}
</script>
