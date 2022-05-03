<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        violations: {default: () => [], type: Array}
    })
    const inputId = computed(() => `${props.form}-${props.field.name}`)
    const violation = computed(() => props.violations.find(({propertyPath}) => propertyPath === props.field.name) ?? null)
    const isInvalid = computed(() => ({'is-invalid': violation.value !== null}))
</script>

<template>
    <td>
        <AppInputGuesser
            v-if="field.create"
            :id="inputId"
            :class="isInvalid"
            :field="field"
            :form="form"
            no-label/>
        <AppInvalidFeedback v-if="violation !== null">
            {{ violation.message }}
        </AppInvalidFeedback>
    </td>
</template>
