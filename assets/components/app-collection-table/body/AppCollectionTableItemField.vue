<script setup>
    import {computed, inject} from 'vue'

    const props = defineProps({field: {required: true, type: Object}, item: {required: true, type: Object}})
    const guesser = computed(() => props.field.type === 'boolean')
    const value = computed(() => props.item[props.field.name])
    const tableId = inject('table-id')
    const inputId = computed(() => `${tableId}-${props.item.id}-${props.field.name}`)
</script>

<template>
    <td>
        <AppInputGuesser v-if="guesser" :id="inputId" :field="field" :model-value="value" disabled no-label/>
        <template v-else>
            {{ value }}
        </template>
    </td>
</template>
