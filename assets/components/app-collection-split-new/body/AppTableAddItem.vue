<script setup>
    import {defineEmits, defineProps, inject, reactive, watch} from 'vue'
    import AppTableItemInput from './AppTableItemInput.vue'

    const props = defineProps({value: {required: true, type: Number}})

    const emit = defineEmits('plus')
    const item = reactive({quantite: props.value})
    const fields = inject('fields', [])

    function input(payload) {
        item[payload.name] = payload.value
    }

    function plus() {
        emit('plus', item)
    }
    watch(
        () => props.value,
        newValue => {
            item.quantite = newValue
        }
    )
</script>

<template>
    <tr>
        <td class="text-center">
            <AppBtn icon="plus" variant="success" @click="plus"/>
        </td>
        <AppTableItemInput
            v-for="field in fields"
            :key="field.name"
            :field="field"
            :item="item"
            @input="input"/>
    </tr>
</template>
