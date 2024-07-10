<script setup>
    import {computed, defineProps} from 'vue'
    import AppPricesTableField from './AppPricesTableField.vue'

    const props = defineProps({fields: {required: true, type: Array}})
    const filterFields = computed(() => props.fields.filter(field => !field.children))
</script>

<template>
    <tr>
        <th width="100">
            Actions
        </th>
        <AppPricesTableField
            v-for="field in filterFields"
            :key="field.name"
            :field="field"/>
    </tr>
</template>

<style scoped>
    th {
        vertical-align: middle;
        text-align: center;
        font-size: small;
    }
</style>
