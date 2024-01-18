<template>
	<button :="$attrs">
		{{ text }}
	</button>
</template>

<script setup>
const props = defineProps({ text: String });
</script>
