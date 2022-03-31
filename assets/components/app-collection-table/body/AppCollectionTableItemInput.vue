<script setup>
    import AppCollectionTableItemField from './AppCollectionTableItemField.vue'
    import {computed} from 'vue'

    const props = defineProps({
        field: {required: true, type: Object},
        item: {required: true, type: Object}
    })
    const value = computed(() => props.item[props.field.name])
</script>

<template>
    <td v-if="field.update">
        <AppInputGuesser :field="field" :model-value="value"/>
    </td>
    <AppCollectionTableItemField v-else :field="field" :item="item"/>
</template>
