<script setup>
    import {defineEmits, defineProps} from 'vue'
    import AppCardableTableSortableField from './AppCardableTableSortableField.vue';
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
        <template v-for="field in fields">
            <AppCardableTableSortableField :field="field" @trierAlphabet="trierAlphabet"/>
        </template>
</template>
