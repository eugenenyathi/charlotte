<template>
  <section class="confirm-mates-container preferred-mates">
    <h2>{{ studentName }}, here are your roommates.</h2>

    <!-- //TODO: save the loaded roommates into the vuex store, such that we don't refresh every time -->
    <div class="confirm-mates">
      <router-link v-for="roommate in roommates" :key="roommate.student_id"
        :to="{ name: 'Roommate Profile', params: { studentID: roommate.student_id } }" class="st-details roommates">
        <p class="roommates-st-name">{{ roommate.fullName }}</p>
        <ButtonIcon class="response-icon" icon-name="fa-solid fa-check-circle" />
      </router-link>
    </div>
  </section>
</template>

<script setup>
import { ButtonIcon } from '@/base/'
import { computed } from 'vue'
import { useStore } from 'vuex'

defineProps({ studentName: String })
const store = useStore()
const roommates = computed(() => store.getters.roommates)

</script>
