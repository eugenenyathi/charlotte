<template>
	<button type="button" :="$attrs" @click="$emit('btnFn')">
		{{ text }}
	</button>
</template>

<script setup>
const props = defineProps({ text: String });
const emits = defineEmits(["btnFn"]);
</script>
