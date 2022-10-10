<script setup>
    import AppTableItemField from './AppTableItemField.vue'
    import {computed} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Array},
        index: {required: true, type: Number},
        item: {required: true, type: Object}
    })
    const normalizedIndex = computed(() => props.index + 1)
</script>

<template>
    <tr>
        <td>{{ normalizedIndex }}</td>
        <AppTableItemField v-for="field in fields" :key="field.name" :field="field" :item="item"/>
    </tr>
</template>
