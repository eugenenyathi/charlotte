<template>
  <header class="tablet-header-container">
    <div class="tablet-header-content width-container">
      <div class="logo-area">
        <img :src="logo" alt="" class="lupane-logo" />
      </div>
      <nav class="tablet-nav-wrapper">
        <ul class="nav-ul-wrapper">
          <li class="nav-link">
            <router-link to="/" class="active">
              <font-awesome-icon icon="fa-solid fa-home" class="link-icon" />
            </router-link>
          </li>
          <li class="nav-link">
            <router-link to="/profile">
              <font-awesome-icon icon="fa-solid fa-user" class="link-icon" />
            </router-link>
          </li>
          <li class="nav-link">
            <router-link to="/residence">
              <font-awesome-icon icon="fa-solid fa-bed" class="link-icon" />
            </router-link>
          </li>
          <li class="nav-link">
            <router-link to="/settings">
              <font-awesome-icon icon="fa-solid fa-cog" class="link-icon" />
            </router-link>
          </li>
          <li class="nav-link">
            <router-link to="/logout">
              <font-awesome-icon icon="fa-solid fa-door-open" class="link-icon" />
            </router-link>
          </li>
        </ul>
      </nav>
    </div>
  </header>
</template>

<script setup>
//assets
import logo from '../assets/lupane.png'
</script>
