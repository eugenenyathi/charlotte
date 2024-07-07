<template>
  <header class="mobile-header-container">
    <div class="mobile-header-content">
      <div class="show-area">
        <img :src="logo" alt="" class="lupane-logo" />
        <font-awesome-icon icon="fa-solid fa-bars" class="menu-icon" @click="openSideMenu" />
      </div>
      <nav :class="{
        'mobile-nav-wrapper slide-in-out': sideMenuActive,
        'mobile-nav-wrapper': !sideMenuActive,
      }">
        <font-awesome-icon icon="fa-solid fa-times" class="close-menu-icon" @click="closeSideMenu" />
        <ul class="mobile-nav-ul-wrapper">
          <li class="nav-link">
            <router-link to="/" class="active" @click="closeSideMenu">
              <font-awesome-icon icon="fa-solid fa-home" class="link-icon" />
              Home
            </router-link>
          </li>
          <li class="nav-link">
            <router-link to="/profile" @click="closeSideMenu">
              <font-awesome-icon icon="fa-solid fa-user" class="link-icon" />
              Profile
            </router-link>
          </li>
          <li class="nav-link">
            <router-link to="/residence" @click="closeSideMenu">
              <font-awesome-icon icon="fa-solid fa-bed" class="link-icon" />
              Residence
            </router-link>
          </li>
          <!-- <li class="nav-link">
            <router-link to="/settings" @click="closeSideMenu">
              <font-awesome-icon icon="fa-solid fa-cog" class="link-icon" />
              Settings
            </router-link>
          </li> -->
          <li class="nav-link" @click="logout()">
            <font-awesome-icon icon="fa-solid fa-door-open" class="link-icon" />
            Logout
          </li>
        </ul>
      </nav>
    </div>
  </header>
</template>

<script setup>
//assets
import logo from "../assets/lupane.png";

import { ref } from "vue";
import useLogout from "@/composables/useLogout";

const { logout } = useLogout()

const sideMenuActive = ref(false);

const openSideMenu = () => {
  sideMenuActive.value = true;
};
const closeSideMenu = () => {
  sideMenuActive.value = false;
};
</script>
