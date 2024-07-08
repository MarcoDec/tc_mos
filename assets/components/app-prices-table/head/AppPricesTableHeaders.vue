<script setup>
    import {computed} from 'vue'
    import AppPricesTableFields from './AppPricesTableFields.vue'

    const props = defineProps({
        mainFields: {required: true, type: Array},
        title: {default: '', required: false, type: String}
    })
    // console.log('MainFields', props.mainFields)
    const filteredHeaders = computed(() => props.mainFields.filter(field => !field.children))
    // console.log('filteredHeaders', filteredHeaders.value)
    const rows = computed(() => {
        const ranks = []
        let current = filteredHeaders.value
        do {
            ranks.push(current)
            current = current.map(field => (Array.isArray(field.children) && field.children.length > 0 ? field.children : [])).flat()
        } while (current.length > 0)
        return ranks
    })
</script>

<template>
    <thead class="table-dark">
        <tr v-if="title !== ''">
            <td class="text-center" :colspan="filteredHeaders.length + 1">
                {{ title }}
            </td>
        </tr>
        <AppPricesTableFields :fields="filteredHeaders"/>
    </thead>
</template>
