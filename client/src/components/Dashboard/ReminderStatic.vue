<template>
  <div class="reminder">
    <p class="reminder-text" v-if="!fullyCleared">
      Hie
      <span class="student-name">{{ studentName }}</span
      >, Thank you for paying {{ amountCleared }}. We kindly remind you to clear
      the remainder of <span class="amount-owing">{{ amountOwing }}</span
      >.
    </p>

    <p v-else>
      Hie
      <span class="student-name">{{ studentName }}</span
      >, Thank you for fully paying you tuition fees}.
    </p>
  </div>
</template>

<script setup>
const props = defineProps({
  fullyCleared: Boolean,
  studentName: String,
  amountCleared: String,
  amountOwing: String,
});
</script>
