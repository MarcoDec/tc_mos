<script setup>
    import {defineEmits, defineProps, inject} from 'vue'
    import AppTableItemInput from './AppTableItemInput.vue'

    const emit = defineEmits(['update'])
    const props = defineProps({index: {required: true, type: Number}, item: {required: true, type: Object}})
    const fields = inject('fields', [])

    function update(payload) {
        emit('update', {
            index: props.index,
            item: {...props.item, [payload.name]: payload.value}
        })
    }
</script>

<template>
    <tr>
        <td class="text-center">
            {{ index + 1 }}
        </td>
        <AppTableItemInput
            v-for="field in fields"
            :key="field.name"
            :field="field"
            :item="item"
            @input="update"/>
    </tr>
</template>
