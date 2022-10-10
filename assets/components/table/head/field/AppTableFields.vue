<script setup>
    import AppTableField from './AppTableField.vue'

    defineProps({fields: {required: true, type: Array}, store: {required: true, type: Object}})
</script>

<template>
    <tr>
        <th/>
        <AppTableField v-for="field in fields" :key="field.name" :field="field" :store="store"/>
    </tr>
</template>
