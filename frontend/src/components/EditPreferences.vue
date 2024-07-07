<template>
    <section class="settings-wrapper">
        <div class="settings-content">
            <div>
                <div v-for="question in RoommatePreference.QUESTIONS" :key="question.id"
                    class="border-b border-neutral-300 last:border-b-0 py-4 mb-4">
                    <div class="flex justify-between items-center">
                        <h1 class="text-xl">{{ question.question }}</h1>
                        <div class="flex items-center gap-4">
                            <button @click="handleResponseUpdate(question.id, RoommatePreference.YES)"
                                :class="{ 'bg-neutral': !isYesSelected(question.id), 'bg-orange-500': isYesSelected(question.id) }"
                                class="bg-neutral py-2 px-4 text-base rounded text-white">
                                Yes
                            </button>
                            <button @click="handleResponseUpdate(question.id, RoommatePreference.NO)"
                                :class="{ 'bg-neutral': !isNoSelected(question.id), 'bg-orange-500': isNoSelected(question.id) }"
                                class="bg-neutral py-2 px-4 text-base rounded text-white">
                                No
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />

            <button @click="handleSubmit()" class="settings-btn" v-if="!isLoading"
                :disabled="!responseUpdated">Update</button>

            <button @click="handleSubmit()" class="settings-btn" v-else>
                <Loader size="small" />
            </button>

        </div>

    </section>
</template>

<script setup>
import RoommatePreference from '@/constants/RoommatePreference';
import Loader from '@/components/BtnLoader.vue'
import Alert from '@/components/Alert.vue'
import AlertFn from '@/helpers/AlertFn.js'
import watchFn from '@/helpers/watchFn.js'
import usePreferences from '@/composables/usePreferences.js';
import { ref, reactive, computed, onMounted } from 'vue'
import { useStore } from 'vuex';
import { useRouter } from 'vue-router';

const store = useStore()
const router = useRouter()
const responseUpdated = ref(false)

const preferences = computed(() => store.getters.cachedRoommatePreference)

onMounted(() => {
    if (Object.entries(preferences.value).length === 0) {
        router.push({ name: 'Roommate Preference' })
    }
})


const alert = reactive({ show: false, msg: '', type: '' })
const { showAlert, removeAlert } = AlertFn(alert)
const { watchAxiosErr } = watchFn()
const { axiosSuccess, axiosError, isLoading, updatePreferences } = usePreferences()

const isYesSelected = computed(() => (questionId) => {
    return questionId === 1 && preferences.value.question_1 === RoommatePreference.YES ||
        questionId === 2 && preferences.value.question_2 === RoommatePreference.YES;
});

const isNoSelected = computed(() => (questionId) => {
    return questionId === 1 && preferences.value.question_1 === RoommatePreference.NO ||
        questionId === 2 && preferences.value.question_2 === RoommatePreference.NO;
});

watchAxiosErr(alert, axiosError)

const handleResponseUpdate = (questionId, questionResponse) => {
    store.dispatch('updatePreference', { questionId, response: questionResponse })
    responseUpdated.value = true
}

const handleSubmit = async () => {
    await updatePreferences()

    if (axiosSuccess.value) {
        showAlert(true, 'Preferences updated successfully!', 'success')
        removeAlert()
        //redirect to roommate preferences
        setTimeout(() => {
            router.push({
                name: 'Roommate Preference'
            })
        }, 3200)

    }
}

</script>