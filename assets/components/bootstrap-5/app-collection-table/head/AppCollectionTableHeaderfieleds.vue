<script setup>
    import AppCollectionTableSortableField from './AppCollectionTableSortableField.vue'
    const emit = defineEmits(['trierAlphabet'])
    const props = defineProps({
        fields: {required: true, type: Array}
        // trier: {required: true, type: String}
    })
    console.log(props.fields)
    function trierAlphabet(payload) {
        emit('trierAlphabet', payload)
    }
</script>

<template>
    <template v-for="field in fields" :key="field.name">
        <AppCollectionTableSortableField :field="field" @trier-alphabet="trierAlphabet"/>
    </template>
</template>
