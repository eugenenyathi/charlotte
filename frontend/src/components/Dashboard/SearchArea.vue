<template>
  <section class="search-area-container">
    <!-- Search Form -->
    <form class="search-area-form">
      <div class="search-control">
        <div class="search-icon-wrapper">
          <ButtonIcon class="search-icon" iconName="fa-solid fa-search" v-if="!isLoading" />
          <Loader v-if="isLoading" size="small" />
        </div>
        <div class="search-input-wrapper">
          <BaseInput class="search-input" placeholder="Search by student number" :value="searchInput"
            @input="updateStNumber($event)" />
          <ButtonIcon class="clear-search-icon" iconName="fa-solid fa-times" @click="clearSearch" />
        </div>
      </div>
    </form>

    <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />

    <!-- Search Results -->
    <div class="search-results-container" v-if="searchResults">
      <ul v-auto-animate class="search-ul-wrapper">
        <li class="list-item" v-for="student in searchResults.slice(0, 5)" :key="student.id"
          @click="selectStudent(student)">
          <div :class="{
            'search-details': student.available === 'Yes',
            'search-details grey-out': student.available === 'No'
          }">
            <span class="st-name">{{ student.fullName }}</span>
            <span class="st-number"> {{ student.id }}</span>
            <span class="st-program"> {{ student.program }}</span>
          </div>
        </li>
      </ul>
    </div>
  </section>
</template>

<script setup>
//components & helpers
import { BaseInput, ButtonIcon } from '@/base/index.js'
import Loader from '../BtnLoader.vue'
import Alert from '../Alert.vue'
import AlertFn from '@/helpers/AlertFn.js'

//composables
import useSearch from '@/composables/useSearch.js'
//vue
import { ref, reactive, watch, computed } from 'vue'
import { useStore } from 'vuex'
import SelectionResponse from '@/constants/SelectionResponse'

const searchInput = ref('')
const regex = /^L0\d*/
const store = useStore()
const emit = defineEmits(['show'])

const editingSelection = computed(() => store.getters.getEditingSelection);

const alert = reactive({ show: false, msg: 'testing', type: 'success' })
const { showAlert, removeAlert } = AlertFn(alert)

const { isLoading, searchStudent } = useSearch()

const updateStNumber = ($event) => {
  searchInput.value = event.target.value.toUpperCase()
}

if (!searchInput.value) {
  store.dispatch('clearResults')
}

watch(searchInput, (newValue, oldValue) => {
  if (newValue.length < 3 || !regex.test(newValue)) return store.dispatch('clearResults')
  searchStudent(searchInput.value)
})

const searchResults = computed(() => store.getters.searchResults)
const selectedStudents = computed(() => store.getters.selectedStudents)

const clearSearch = () => (searchInput.value = '')

const selectStudent = (student) => {
  //check if the student is unavailable
  if (student.available === SelectionResponse.NO) {
    showAlert(true, 'Student is taken', 'danger')
    removeAlert()
    return
  }

  if (selectedStudents.value.includes(student.id))
    //get the current selectedStudents list
    return

  //add the current student to the list of selectedStudents
  store.dispatch('addSelectedStudent', { 'student_id': student.id, 'context': 'search' })

  //check the number of selected students if there are now
  //three clear the results
  if (selectedStudents.value.length === 3) store.dispatch('clearResults')

  if (!editingSelection.value) emit('show', 'selectedMates')
}
</script>
