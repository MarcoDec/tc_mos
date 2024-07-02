<script setup>
    import {computed} from 'vue'
    import AppPricesTableFields from './AppPricesTableFields.vue'

    const props = defineProps({
        mainFields: {required: true, type: Array},
        title: {default: '', required: false, type: String}
    })

    const rows = computed(() => {
        const ranks = []
        let current = props.mainFields
        do {
            ranks.push(current)
            current = current.map(field => (Array.isArray(field.children) && field.children.length > 0 ? field.children : [])).flat()
        } while (current.length > 0)
        return ranks
    })
</script>

<template>
    <thead class="table-dark">
        <tr v-if="title !== ''"><td :colspan="rows[0].length + rows[1].length + 1" class="text-center">{{ title }}</td></tr>
        <AppPricesTableFields v-for="(row, i) in rows" :key="i" :fields="row"/>
    </thead>
</template>
