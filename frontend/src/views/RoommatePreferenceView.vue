<template>
    <section class="settings-wrapper">
        <div class="settings-content">
            <Loader v-if="isLoading" />

            <div v-else class="info-wrapper">
                <div class="info">
                    <p>{{ RoommatePreference.QUESTION_1.question }}</p>
                    <p :style="{ 'color': 'white' }"
                        class="bg-orange-500 py-2 px-4 text-base capitalize rounded text-white">{{
                            roommatePreference.question_1 }}</p>
                </div>
                <div class="info">
                    <p>{{ RoommatePreference.QUESTION_2.question }}</p>
                    <p :style="{ 'color': 'white' }"
                        class="bg-orange-500 py-2 px-4 text-base capitalize rounded text-white">{{
                            roommatePreference.question_2 }}</p>
                </div>
            </div>

            <Alert v-if="alert.show" :msg="alert.msg" :type="alert.type" />

            <router-link v-if="!isLoading" :to="{ name: 'Edit Preferences' }" class="settings-btn">Edit</router-link>
        </div>
    </section>
</template>

<script setup>
import RoommatePreference from '@/constants/RoommatePreference'
import usePreferences from '@/composables/usePreferences.js';
import Loader from '@/components/Loader.vue'
import Alert from '@/components/Alert.vue'
import watchFn from '@/helpers/watchFn.js'

import { useStore } from 'vuex'
import { computed, reactive, onMounted } from 'vue'

const store = useStore()

const alert = reactive({ show: false, msg: '', type: '' })
const { watchAxiosErr } = watchFn()

const roommatePreference = computed(() => store.getters.cachedRoommatePreference)

const { axiosError, isLoading, getPreferences } = usePreferences()

onMounted(async () => {
    await getPreferences()
})

watchAxiosErr(alert, axiosError)
</script>
