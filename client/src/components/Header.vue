<template>
  <header class="header-container">
    <div class="header-content">
      <h2 class="route-name">{{ currentRoute }}</h2>
      <div class="flow-right">
        <h2 class="student-name">{{ user.fullName }}</h2>
        <button class="logout-btn">
          <router-link to="/logout"> Logout </router-link>
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from "vue";
import { useStore } from "vuex";
import { useRoute } from "vue-router";

const store = useStore();
const user = computed(() => store.getters.getUser);

const currentRoute = computed(() => {
  if (useRoute().name === "Dashboard") return "Home";
  else if (useRoute().name === "RoomieProfile") return "Roommate";
  else return useRoute().name;
});
</script>
