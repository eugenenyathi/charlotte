<template>
  <section class="confirm-mates-container preferred-mates">
    <h2>{{ myname }}, here are your roommates.</h2>

    <div class="confirm-mates">
      <div class="st-details" v-for="roomie in roommates" :key="roomie.id">
        <router-link
          :to="{ name: 'RoomieProfile', params: { studentID: roomie.id } }"
          class="st-name"
          >{{ roomie.fullName }}</router-link
        >
        <ButtonIcon class="response-icon" icon-name="fa-solid fa-check-circle" />
      </div>
    </div>
  </section>
</template>

<script setup>
import { ButtonIcon } from '../../base/'
import { computed } from 'vue'
import { useStore } from 'vuex'

const props = defineProps({ myname: String })
const store = useStore()
const roommates = computed(() => store.getters.roommates)

// console.log(preferredMates.value);
</script>
