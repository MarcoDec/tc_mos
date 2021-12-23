<script lang="ts" setup>
    import {defineProps, ref} from 'vue'
    defineProps<{title: string}>()
    const isOpen = ref(false)
</script>

<template>
    <div class="menu-item" @click="isOpen = !isOpen">
        <Fa class="me-1 mt-1" icon="shopping-bag"/>
        <a id="title" href="#">
            {{ title }}
        </a>
        <svg viewBox="0 0 1030 638" width="10">
            <path d="M1017 68L541 626q-11 12-26 12t-26-12L13 68Q-3 49 6 24.5T39 0h952q24 0 33 24.5t-7 43.5z" fill="#FFF"/>
        </svg>
        <Transition name="fade" apear>
            <div v-if="isOpen" class="sub-menu">
                <div class="menu-item">
                    <Fa class="me-2" icon="layer-group"/>
                    <RouterLink class="drop" to="/component/families">
                        Familles
                    </RouterLink>
                </div>
            </div>
        </Transition>
    </div>
</template>

<style scoped>
#title{
  color: white;
  text-decoration: none;
}
.drop{
  color: white;
  text-decoration: none;
}
nav .menu-item svg {
  width: 10px;
  margin-left: 10px;
}
nav .menu-item .sub-menu {
  position: absolute;
  background-color: #222;
  top: calc(100% + 12px);
  left: 50%;
  transform: translateX(-50%);
  width:132px;
  border-radius: 0px 0px 6px 6px;
  z-index: 1000;

}
.fade-enter-active,
.fade-leave-active {
  transition: all .5s ease-out;
}
.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>
