<template>
	<!-- Desktop-size components-->
	<Sidebar />
	<Header />

	<!-- Tablet-size components -->
	<TabletHeader />

	<!-- Mobile-size components -->
	<MobileHeader />

	<!-- router-view -->
	<router-view />
</template>

<script setup>
//components
import Sidebar from "../components/Sidebar";
import Header from "../components/Header";
import TabletHeader from "../components/TabletHeader";
import MobileHeader from "../components/MobileHeader";
//composables
import useAuth from "../composables/useAuth";
//vuex
import { useStore } from "vuex";

const store = useStore();
const { getAuthUser } = useAuth();
const user = getAuthUser();

//set current user global state
store.dispatch("setUser", user);
</script>
