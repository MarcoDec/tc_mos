<script setup>
    import {computed, defineProps} from 'vue'
    import AppRowsTableItemField from "./AppRowsTableItemField.vue";

    const props = defineProps({field, item})
    const value = computed(() => props.item[props.field.name])
</script>

<template>
    <td v-if="field.update">
        <AppInputGuesser :field="field" :model-value="value"/>
    </td>
    <AppRowsTableItemField v-else :field="field" :item="item"/>
</template>
