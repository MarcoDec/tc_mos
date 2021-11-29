<template>
    <AppShowGui/>
</template>

<script lang="ts" setup>
import AppShowGui from '../../../../components/gui/AppShowGui.vue'
import {defineProps} from "vue";
defineProps<{ cssClass?: string }>()

</script>

