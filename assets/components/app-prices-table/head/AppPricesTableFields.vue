<script setup>
    import {computed, defineProps} from 'vue'
    import AppPricesTableField from './AppPricesTableField.vue'

    const props = defineProps({fields: {required: true, type: Array}})

    function walkRowspan(walkedFields, span = 1) {
        let max = span
        for (const field of walkedFields)
            if (field.children) {
                const depth = walkRowspan(field.children, span + 1)
                if (depth > max)
                    max = depth
            }
        return max
    }

    const rowspan = computed(() => walkRowspan(props.fields))
</script>

<template>
    <tr>
        <th :rowspan="rowspan" width="50">
            Actions
        </th>
        <AppPricesTableField
            v-for="field in fields"
            :key="field.name"
            :field="field"
            :rowspan="rowspan"/>
    </tr>
</template>

<style scoped>
    th {
        vertical-align: middle;
        text-align: center;
        font-size: small;
    }
</style>