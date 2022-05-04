<script setup>
    import {computed, ref} from 'vue'
    import AppCollectionTableItemField from './AppCollectionTableItemField.vue'

    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        item: {required: true, type: Object},
        violations: {default: () => [], type: Array}
    })
    const inputId = computed(() => `${props.form}-${props.field.name}`)
    const violation = computed(() => props.violations.find(({propertyPath}) => propertyPath === props.field.name) ?? null)
    const isInvalid = computed(() => ({'is-invalid': violation.value !== null}))
    const value = ref(props.item[props.field.name])
</script>

<template>
    <td v-if="field.update">
        <AppInputGuesser :id="inputId" v-model="value" :class="isInvalid" :field="field" :form="form" no-label/>
        <AppInvalidFeedback v-if="violation !== null">
            {{ violation.message }}
        </AppInvalidFeedback>
    </td>
    <AppCollectionTableItemField v-else :field="field" :item="item"/>
</template>
