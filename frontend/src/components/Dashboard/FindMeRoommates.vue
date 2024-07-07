<template>
    <section>
        <h1 v-if="showFindMeRoommatesHeading"
            class="text-sm text-neutral-500 cursor-pointer hover:underline hover:text-black transition my-4"
            @click="findMeRoommates">
            Find me roommates
        </h1>

        <h1 v-else class="font-semibold my-6">{{ message }}</h1>

        <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />
        <!-- Match Results -->
        <Loader v-if="isLoading" />

        <div class="search-results-container" v-if="showMatchingRoommates">
            <ul class="search-ul-wrapper">
                <li class="list-item" v-for="student in matchingRoommates" :key="student.id"
                    @click="selectStudent(student)">
                    <div :class="{
                        'search-details': student.available === SelectionResponse.YES,
                        'search-details grey-out': student.available === SelectionResponse.NO
                    }">
                        <span class="st-name">{{ student.fullName }}</span>
                        <span class="st-number"> {{ student.id }}</span>
                        <span class="st-program"> {{ student.program }}</span>
                    </div>
                </li>
            </ul>
        </div>
        <UiPagination v-if="showMatchingRoommates" />

        <h1 v-if="!showFindMeRoommatesHeading"
            class="text-sm text-neutral-500 cursor-pointer hover:underline hover:text-black transition mb-4"
            @click="searchForRoommates">
            Search for roommates
        </h1>
    </section>
</template>

<script setup>
//components & helpers
import Loader from '@/components/Loader.vue'
import Alert from '@/components/Alert.vue'
import UiPagination from '../ui/UiPagination.vue'

import SelectionResponse from '@/constants/SelectionResponse'
import useAxiosError from '@/composables/useAxiosError.js'
import usePagination from '@/composables/usePagination'
import AlertFn from '@/helpers/AlertFn.js'

//vue
import { ref, reactive, computed } from 'vue'
import { useStore } from 'vuex'
import { useRouter } from 'vue-router'
import axios from 'axios'

const emit = defineEmits(['hideSearchbar', 'show', 'showSearch'])

const store = useStore()
const router = useRouter()
const showFindMeRoommatesHeading = ref(true)
const message = ref("Showing Matches")

const isLoading = ref(false)
const showMatchingRoommates = ref(false)

const student = computed(() => store.getters.getUser)
const { studentNumber } = student.value
const matchingRoommates = computed(() => store.getters.getPaginatedData);
const selectedStudents = computed(() => store.getters.selectedStudents)
const editingSelection = computed(() => store.getters.getEditingSelection);

const alert = reactive({ show: false, msg: 'testing', type: 'success' })
const { showAlert, removeAlert } = AlertFn(alert)
const { pagination, paginate } = usePagination()

const findMeRoommates = async () => {
    emit('hideSearchbar')
    await getPotentialRoommates(studentNumber)
}


const getPotentialRoommates = async (studentNumber) => {
    try {
        isLoading.value = true

        const { data } = await axios(`match/${studentNumber}`)

        // console.log(data)

        isLoading.value = false

        switch (data.preference_set) {
            case true:
                if (data.matchingRoommates.length > 0) {
                    showFindMeRoommatesHeading.value = false
                    showMatchingRoommates.value = true
                    //first clear any pre-existing data
                    store.dispatch("flushRouteData");
                    //store the route data
                    store.dispatch("setRouteData", data.matchingRoommates);
                    //set pagination stuff, this will update the paginated data
                    pagination();
                    store.dispatch("setCurrentPage", 1);
                    //settings for the pagination bar
                    const pageNumbers = paginate(data.matchingRoommates.length);
                    store.dispatch("setPageNumbers", pageNumbers);
                }
                else {
                    console.log('hello')
                    showFindMeRoommatesHeading.value = false
                    message.value = "No matches found!"
                }
                break;
            case false:
                // redirect to preference_setting route
                showMatchingRoommates.value = false
                router.push({ name: 'Create Preferences' })
                break;
        }


    } catch (error) {
        // console.log(error)
        useAxiosError(error)
    }
}

const selectStudent = (student) => {
    // console.log(student.id)
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
    store.dispatch('addSelectedStudent', { 'student_id': student.id, 'context': 'find' })

    //check the number of selected students if there are now
    //three clear the results
    if (selectedStudents.value.length === 3) store.dispatch('clearResults')

    if (!editingSelection.value) emit('show', 'selectedMates')
}

const searchForRoommates = () => {
    showMatchingRoommates.value = false
    showFindMeRoommatesHeading.value = true
    emit('showSearch')
}
</script>