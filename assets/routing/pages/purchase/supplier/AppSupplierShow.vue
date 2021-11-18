<template>
    <AppShowGui/>
</template>

<script lang="ts" setup>
import AppShowGui from '../../../../components/gui/AppShowGui.vue'
import {useNamespacedGetters, useNamespacedState} from "vuex-composition-helpers";
import {defineProps} from "vue";
import {State} from "../../../../store/gui";


defineProps<{ cssClass?: string }>()

const height = useNamespacedState<State>('gui',['windowHeight'])
const getpx = useNamespacedGetters('gui',['heightBottompx'])
console.log('afefff',getpx)
</script>

