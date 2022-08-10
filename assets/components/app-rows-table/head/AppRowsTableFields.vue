<script setup>
    import {computed, defineProps, ref} from 'vue'
    import AppRowsTableField from "./AppRowsTableField.vue";

    const props = defineProps({fields: {required: true, type: Array}})
            

    const asc = ref(false)
    const sort = ref('code')

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

    function handleSort(field) {
        if (sort.value === field.name)
            asc.value = !asc.value
        else {
            asc.value = false
            sort.value = field.name
        }
    }
</script>

<template>
    <tr>
        <th :rowspan="rowspan"/>
        <th :rowspan="rowspan">
            Actions
        </th>
        <AppRowsTableField
            v-for="field in fields"
            :key="field.name"
            :asc="asc"
            :field="field"
            :sort="sort"
            :rowspan="rowspan"
            @click="handleSort"/>
    </tr>
</template>
