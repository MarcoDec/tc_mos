<script setup>
    import AppTableField from './AppTableField.vue'
    import {inject} from 'vue'

    const fields = inject('fields', [])
</script>

<template>
    <tr>
        <th>Actions</th>
        <AppTableField v-for="field in fields" :key="field.name" :field="field"/>
    </tr>
</template>
