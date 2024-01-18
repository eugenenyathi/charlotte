<template>
  <div v-if="width > 800">
    <img :src="loader" alt="loader" width="35" v-if="!size" />
    <img :src="loader" alt="loader" width="25" v-else-if="size === 'small'" />
    <img :src="loader" alt="loader" width="20" v-else-if="size === 'tiny'" />
  </div>
  <div v-else>
    <img :src="loader" alt="loader" width="20" v-if="!size" />
    <img :src="loader" alt="loader" width="15" v-else-if="size === 'small'" />
    <img :src="loader" alt="loader" width="10" v-else-if="size === 'tiny'" />
  </div>
</template>

<script setup>
//assets
import loader from "@/assets/loader-1.svg";

const props = defineProps({ size: String });

const width = window.innerWidth;

// console.log(width);
</script>
