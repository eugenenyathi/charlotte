<template>
	<section class="reset-container">
		<div class="reset-content">
			<PersonalInfo v-if="page === 1" @signal="signal()" />
			<SetPassword v-else @pop="pop()" />
		</div>
	</section>
</template>

<script setup>
import PersonalInfo from "../components/ResetPersonalInfo.vue";
import SetPassword from "../components/ResetSetPassword.vue";
import { BaseButton } from "../base/";

import { ref, inject } from "vue";

const page = ref(1);

const studentNumber = ref("");
const nationalId = ref("");

const signal = () => {
	page.value = 2;
};

const pop = () => {
	page.value = 1;
};
</script>
