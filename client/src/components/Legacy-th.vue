<template>
	<header class="tablet-header-container">
		<div class="tablet-header-content width-container">
			<div class="logo-area">
				<!-- <img :src="logo" alt="lupane-logo" class="lupane-logo" /> -->
				<h2>myLSU</h2>
			</div>
			<nav class="tablet-nav-wrapper">
				<ul class="nav-ul-wrapper">
					<li class="nav-link">
						<router-link to="/">
							<!-- <Dashboard class="nav-icon" /> -->
							<DashboardAlt
								class="nav-icon nav-icon-alt active"
							/>
						</router-link>
					</li>
					<li class="nav-link">
						<router-link to="/profile">
							<User class="nav-icon" />
						</router-link>
					</li>
					<li class="nav-link">
						<router-link to="/residence">
							<Bed class="nav-icon" />
						</router-link>
					</li>
					<li class="nav-link">
						<router-link to="/settings">
							<Settings class="nav-icon" />
						</router-link>
					</li>
				</ul>
			</nav>
		</div>
	</header>
</template>

<script setup>
//icons
import Dashboard from "vue-material-design-icons/HomeOutline.vue";
import DashboardAlt from "vue-material-design-icons/Home.vue";
import User from "vue-material-design-icons/AccountOutline.vue";
import UserAlt from "vue-material-design-icons/Account.vue";
import Bed from "vue-material-design-icons/BedOutline.vue";
import BedAlt from "vue-material-design-icons/Bed.vue";
import Settings from "vue-material-design-icons/CogOutline.vue";
import SettingsAlt from "vue-material-design-icons/Cog.vue";

//assets
const logo = require("../assets/lupane.png");
</script>
