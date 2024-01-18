<template>
	<template v-if="label">
		<label>{{ label }}</label>
		<input
			type="text"
			:="$attrs"
			@input="$emit('update:modelValue', $event.target.value)"
		/>
	</template>
	<template v-else>
		<input
			type="text"
			:="$attrs"
			@input="$emit('update:modelValue', $event.target.value)"
		/>
	</template>
</template>

<script setup>
const props = defineProps({ modelValue: String, label: String });
</script>
