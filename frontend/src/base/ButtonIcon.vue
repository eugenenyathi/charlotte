<template>
	<button type="button" :="$attrs" @click="$emit('btnFn')">
		<font-awesome-icon :icon="iconName" />
	</button>
</template>

<script setup>
const props = defineProps({ iconName: String });
const emits = defineEmits(["btnFn"]);
</script>
