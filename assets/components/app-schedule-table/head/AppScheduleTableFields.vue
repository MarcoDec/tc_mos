<script setup>
import AppScheduleTableField from './AppScheduleTableField.vue'
import { defineProps } from 'vue'

defineProps({
    fields: { required: true, type: Array },
    lengthFields: { required: true, type: Number }
})
</script>

<template>
    <tr>
        <AppScheduleTableField v-for="(field, index) in fields" :key="field.name" :field="field" :index="index"
            :length-fields="lengthFields" />
    </tr>
</template>
