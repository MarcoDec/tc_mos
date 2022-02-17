<script lang="ts" setup>
    import {defineProps} from 'vue'
    defineProps<{title: string}>()
</script>

<template>
    <div class="dropdown">
        <button class="dropbtn">
            <Fa icon="sitemap"/>
            {{ title }}
            <svg viewBox="0 0 1030 638" width="10">
                <path d="M1017 68L541 626q-11 12-26 12t-26-12L13 68Q-3 49 6 24.5T39 0h952q24 0 33 24.5t-7 43.5z" fill="#FFF"/>
            </svg>
        </button>

        <div class="dropdown-content">
            <RouterLink class="drop" to="/orderSupplier">
                <Fa class="me-2" icon="shopping-cart"/>Commandes Fermes
            </RouterLink>
        </div>
    </div>
</template>

<style scoped>
.dropbtn {
  background-color: #222;
  color: white;
  padding: 16px;
  font-size: 16px;
  border: none;
  cursor: pointer;
}
.dropdown {
  position: relative;
  display: inline-block;
  margin-right: 500px;
}
.dropdown-content {
  display: none;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 225px;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
  left: 10px;
  top: 48px;
}
.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}
.dropdown-content a:hover {
  background-color:  #222;
  color: #f1f1f1;
}
.dropdown:hover .dropdown-content {
  display: block;
}
.dropdown:hover .dropbtn {
  background-color: #222;
}
</style>
