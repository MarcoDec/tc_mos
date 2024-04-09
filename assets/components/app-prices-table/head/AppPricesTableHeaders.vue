<script setup>
    import {computed} from 'vue'
    import AppPricesTableFields from "./AppPricesTableFields.vue";


    const props = defineProps({
        fieldsComponenentSuppliers: { required: true, type: Array },
        fieldsComponenentSuppliersPrices: { required: true, type: Array }
    });

    const rows = computed(() => {
        const ranks = []
        let current = props.fieldsComponenentSuppliers
        do {
            ranks.push(current)
            current = current.map(field => (Array.isArray(field.children) && field.children.length > 0 ? field.children : [])).flat()
        } while (current.length > 0)
        return ranks
    })
</script>

<template>
    <thead class="table-dark">
        <AppPricesTableFields v-for="(row, i) in rows" :key="i" :fields="row"/>
    </thead>
</template>
