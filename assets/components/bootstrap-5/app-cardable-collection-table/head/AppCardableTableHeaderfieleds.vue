<script setup>
    import {defineEmits, defineProps} from 'vue'
    import AppCardableTableSortableField from './AppCardableTableSortableField.vue'
    const emit = defineEmits(['trierAlphabet'])
    defineProps({
        fields: {required: true, type: Array}
        // trier: {required: true, type: String}
    })

    function trierAlphabet(payload) {
        emit('trierAlphabet', payload)
    }
</script>

<template>
    <template v-for="field in fields" :key="field.name">
        <AppCardableTableSortableField :field="field" @trier-alphabet="trierAlphabet"/>
    </template>
</template>
