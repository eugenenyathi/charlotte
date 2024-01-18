<template>
	<div :class="`alert alert-${type}`">{{ msg }}</div>
</template>

<script setup>
const props = defineProps({
	show: Boolean,
	msg: String,
	type: String,
});
</script>
