<template>
  <div class="reminder" v-for="reminder in reminders">
    <p class="reminder-text">
      {{ reminder }}
    </p>
  </div>
</template>

<script setup>
const props = defineProps({
  reminders: Array,
});
</script>
