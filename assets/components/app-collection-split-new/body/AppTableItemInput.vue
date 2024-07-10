<script setup>
    import {computed} from 'vue-demi'

    const props = defineProps({field: {required: true, type: Object}, item: {required: true, type: Object}})
    const val = computed(() => props.item[props.field.name])
    const emit = defineEmits('input')
    function input(value) {
        emit('input', {
            name: props.field.name,
            value: parseInt(value) || 0
        })
    }
</script>

<template>
    <td>
        <AppInputGuesser
            id="id"
            form="form"
            :field="field"
            :model-value="val"
            @update:model-value="input"/>
    </td>
</template>
