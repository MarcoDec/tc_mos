<script setup>
    import {computed, inject} from 'vue'
    import {format} from '../../../utils'

    const props = defineProps({field: {required: true, type: Object}, item: {required: true, type: Object}})
    const guesser = computed(() => props.field.type === 'boolean')
    const value = computed(() => props.item[props.field.name])
    const normalizedValue = computed(() => {
        switch (props.field.type) {
            case 'number':
                return format(value.value)
            case 'select':
                return props.item[`${props.field.name}Label`]
            default:
                return value.value
        }
    })
    const tableId = inject('table-id')
    const inputId = computed(() => `${tableId}-${props.item.id}-${props.field.name}`)
</script>

<template>
    <td>
        <AppInputGuesser v-if="guesser" :id="inputId" :field="field" :model-value="value" disabled no-label/>
        <template v-else>
            {{ normalizedValue }}
        </template>
    </td>
</template>
