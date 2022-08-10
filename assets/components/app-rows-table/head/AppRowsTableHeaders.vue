<script setup>
    import {computed, inject} from 'vue'
    import AppRowsTableFields from "./AppRowsTableFields.vue";


    const fields = inject('fields', [])

    const rows = computed(() => {
        const ranks = []
        let current = fields
        do {
            ranks.push(current)
            current = current.map(field => (Array.isArray(field.children) && field.children.length > 0 ? field.children : [])).flat()
        } while (current.length > 0)
        return ranks
    })
</script>

<template>
    <thead class="table-dark">
        <AppRowsTableFields v-for="(row, i) in rows" :key="i" :fields="row"/>
    </thead>
</template>
