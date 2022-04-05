<script setup>
    import AppCollectionTableItemField from './AppCollectionTableItemField.vue'
    import {computed} from 'vue'

    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        item: {required: true, type: Object},
        violations: {default: () => [], type: Array}
    })
    const violation = computed(() => props.violations.find(({propertyPath}) => propertyPath === props.field.name) ?? null)
    const isInvalid = computed(() => ({'is-invalid': violation.value !== null}))
    const value = computed(() => props.item[props.field.name])
</script>

<template>
    <td v-if="field.update">
        <AppInputGuesser :class="isInvalid" :field="field" :form="form" :model-value="value" no-label/>
        <AppInvalidFeedback v-if="violation !== null">
            {{ violation.message }}
        </AppInvalidFeedback>
    </td>
    <AppCollectionTableItemField v-else :field="field" :item="item"/>
</template>
