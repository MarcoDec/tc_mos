<script lang="ts" setup>
import { computed, onMounted, ref } from "@vue/runtime-core";
import type { Suppliers } from "../../../store/suppliers/supplier/getters";
import { TableField } from "../../../types/app-collection-table";

const props = defineProps<{ item: Suppliers }>();
const statut = props.item.etat



</script>

<template>
  <div id="traffic-light" :item="item">
    <input type="radio" name="traffic-light-color" id="color1" v-bind:value="item.etat" v-bind:checked="item.etat == 'refus' "/>
    <input type="radio" name="traffic-light-color" id="color2" v-bind:value="item.etat" v-bind:checked="item.etat == 'attente' "/>
    <input type="radio" name="traffic-light-color" id="color3" v-bind:value="item.etat" v-bind:checked="item.etat == 'valide' "/>
  </div>
  

</template>

<style scoped>
@whole: ;
@color1: #FF0000;
@color2: #FFFF00;
@color3: #00FF00;

body {
  background-image: url(https://f.cl.ly/items/2K0P3z2y3X3c052f2B3Y/Image%202013.04.30%2021%3A28%3A02.png);
}

#traffic-light {
  position: absolute;
  display: block;
  margin-left: 127px;
  margin-top: -58px;
  background-color: #333;
  width: 40px;
  height: 148px;
  border-radius: 5px;
  transform: rotate(90deg);
}

input {
  appearance: none;
  position: relative;
  left: 20%;
  width: 25px;
  height: 25px;
  margin-top: 20px;
  margin-left: 0px;
  background-color: grey;
  vertical-align: middle;
  border-radius: 100%;
  display: block;
}
input #color1 {
  background-color: darken (#ff0000, 15%);
}

#color1:checked {
  background-color: #ff0000;
  box-shadow: 0 0 6em lighten(#ff0000, 10%);
}
input #color1:hover {
  animation: blink1 1.1s step-end infinite;
}

input #color2 {
  background-color: darken (#ffff00, 15%);
}

#color2:checked {
  background-color: #ffff00;
  box-shadow: 0 0 6em lighten(#ffff00, 10%);
}
input #color2:hover {
  animation: blink2 1.1s step-end infinite;
}

input #color3 {
  background-color: darken (#00ff00, 15%);
}

#color3:checked {
  background-color: #00ff00;
  box-shadow: 0 0 6em lighten(#00ff00, 10%);
}
input #color3:hover {
  animation: blink3 1.1s step-end infinite;
}

@keyframes blink1 {
  0% {
    background-color: #ff0000;
    box-shadow: 0 0 6em lighten(#ff0000, 10%);
  }

  50% {
    background-color: darken(#ff0000, 15%);
    box-shadow: 0 0 0em transparent;
  }
}

@keyframes blink2 {
  0% {
    background-color: #ffff00;
    box-shadow: 0 0 6em lighten(#ffff00, 10%);
  }

  50% {
    background-color: darken(#ffff00, 15%);
    box-shadow: 0 0 0em transparent;
  }
}

@keyframes blink3 {
  0% {
    background-color: #00ff00;
    box-shadow: 0 0 6em lighten(#00ff00, 10%);
  }

  50% {
    background-color: darken(#00ff00, 15%);
    box-shadow: 0 0 0em transparent;
  }
}

* {
  user-select: none;
  outline: none;
}
</style>
