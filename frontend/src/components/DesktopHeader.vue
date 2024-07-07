<template>
  <header class="header-container">
    <div class="header-content">
      <h2 class="route-name">{{ currentRoute }}</h2>
      <div class="flow-right">
        <h2 class="student-name">{{ user.fullName }}</h2>
        <button @click="logout()" class="logout-btn">
          Logout
        </button>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed } from "vue";
import { useStore } from "vuex";
import { useRoute } from "vue-router";
import useLogout from "@/composables/useLogout";

const store = useStore();
const user = computed(() => store.getters.getUser);
const { logout } = useLogout()

const currentRoute = computed(() => {
  if (useRoute().name === "Dashboard") return "Home";
  else if (useRoute().name === "Roommate Profile") return "Roommate";
  else return useRoute().name;
});
</script>
