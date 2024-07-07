<template>
	<div class="flush-message">{{ message }}</div>
</template>

<script setup>
const props = defineProps({ message: String });
</script>
