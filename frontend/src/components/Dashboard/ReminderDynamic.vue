<template>
  <div class="reminder">
    <p class="reminder-text">
      {{ reminder }}
    </p>
  </div>
</template>

<script setup>

import { computed } from 'vue';
import { useStore } from 'vuex';

const store = useStore();

const reminder = computed(() => store.getters.cachedDynamicReminder)
</script>
