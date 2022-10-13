<script setup>
    defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        item: {required: true, type: Object},
        violations: {default: () => [], type: Array}
    })
</script>

<template>
    <AppTableFormField v-if="field.update" :field="field" :form="form" :violations="violations"/>
    <AppTableItemField v-else :field="field" :item="item"/>
</template>
