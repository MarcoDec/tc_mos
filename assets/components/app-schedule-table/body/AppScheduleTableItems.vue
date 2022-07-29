<script setup>
import AppScheduleTableItem from './AppScheduleTableItem.vue'
import { defineProps } from 'vue'
const props= defineProps({
    fields: { required: true, type: Array },
    items: { required: true, type: Array },
    lengthFields: { required: true, type: Number }
})

</script>

<template>
    <tbody>
        <AppScheduleTableItem v-for="item in items" :key="item.id" :item="item" :fields="fields"
            :length-fields="lengthFields" />
    </tbody>
</template>
