<template>
  <aside class="sidebar-wrapper">
    <div class="logo-wrapper">
      <img :src="logo" alt="" class="lupane-logo" />
    </div>
    <div class="menu-links">
      <ul class="menu-ul-wrapper">
        <li class="menu-link">
          <router-link to="/">
            <font-awesome-icon icon="fa-solid fa-home" class="link-icon" />
            Home
          </router-link>
        </li>
        <li class="menu-link">
          <router-link to="/profile">
            <font-awesome-icon icon="fa-solid fa-user" class="link-icon" />
            Profile
          </router-link>
        </li>
        <li class="menu-link">
          <router-link to="/residence">
            <font-awesome-icon icon="fa-solid fa-bed" class="link-icon" />
            Residence
          </router-link>
        </li>
        <li class="menu-link">
          <router-link to="/settings">
            <font-awesome-icon icon="fa-solid fa-cog" class="link-icon" />
            Settings
          </router-link>
        </li>
      </ul>
    </div>
  </aside>
</template>

<script setup>
//assets
import logo from '../assets/lupane.png'
</script>
