<script setup>
    import {computed} from 'vue'
    import {get} from '../../../utils'

    const props = defineProps({
        field: {required: true, type: Object},
        id: {required: true, type: String},
        item: {required: true, type: Object},
        machine: {required: true, type: Object}
    })
    const isBoolean = computed(() => props.field.type === 'boolean')
    const value = computed(() => get(props.item, props.field.name))
    const label = computed(() => (props.field.type === 'select' ? props.field.options.label(value.value) : value.value))
</script>

<template>
    <AppTableFormField
        v-if="isBoolean"
        :id="id"
        :field="field"
        :machine="machine"
        :model-value="value"
        disabled
        form="none"/>
    <td v-else :id="id">
        <slot :field="field" :item="item" :value="value">
            {{ label }}
        </slot>
    </td>
</template>
