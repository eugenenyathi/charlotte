<template>
  <div class="reminder">
    <p class="reminder-text" v-if="!data.fullyCleared">
      Hie
      <span class="student-name">{{ data.studentName }}</span>, Thank you for paying {{ data.amountCleared }}. We kindly
      remind
      you to clear
      the remainder of <span class="amount-owing">{{ data.amountOwing }}</span>.
    </p>

    <p v-else>
      Hie
      <span class="student-name">{{ data.studentName }}</span>, Thank you for fully paying you tuition fees}.
    </p>
  </div>
</template>

<script setup>

import { computed } from 'vue';
import { useStore } from 'vuex';

const store = useStore();
const data = computed(() => store.getters.cachedReminderInfo)

</script>
