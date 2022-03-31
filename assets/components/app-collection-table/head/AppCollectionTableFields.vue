<script setup>
    import {inject, ref} from 'vue'
    import AppCollectionTableField from './AppCollectionTableField.vue'

    const fields = inject('fields', [])
    const asc = ref(false)
    const sort = ref('code')

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
        <th/>
        <th>Actions</th>
        <AppCollectionTableField
            v-for="field in fields"
            :key="field.name"
            :asc="asc"
            :field="field"
            :sort="sort"
            @click="handleSort"/>
    </tr>
</template>
