<script setup>
    import AppManufacturingTableField from './AppManufacturingTableField.vue'
    import {defineProps} from 'vue'

    defineProps({fields: {required: true, type: Array}})
</script>

<template>
    <tr>
        <AppManufacturingTableField
            v-for="field in fields"
            :key="field.name"
            :field="field"/>
    </tr>
</template>
