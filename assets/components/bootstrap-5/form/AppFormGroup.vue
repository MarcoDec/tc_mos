<script setup>
    import AppLabel from './AppLabel'
    import {FiniteStateMachineRepository} from '../../../store/modules'
    import {computed} from 'vue'
    import {useRepo} from '../../../composition'

    const emit = defineEmits(['input', 'update:modelValue'])
    const repo = useRepo(FiniteStateMachineRepository)
    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        modelValue: {default: null},
        stateMachine: {required: true, type: String}
    })
    const inputId = computed(() => `${props.form}-${props.field.name}`)
    const state = computed(() => repo.find(props.stateMachine))
    const violation = computed(() => state.value?.findViolation(props.field) ?? null)
    const isInvalid = computed(() => ({'is-invalid': violation.value !== null}))

    function input(value) {
        emit('update:modelValue', value)
        emit('input', {name: props.field.name, value})
    }
</script>

<template>
    <AppRow class="mb-3">
        <AppLabel>
            {{ field.label }}
        </AppLabel>
        <AppCol>
            <AppInputGuesser
                :id="inputId"
                :class="isInvalid"
                :field="field"
                :form="form"
                :model-value="modelValue"
                @update:model-value="input"/>
            <AppInvalidFeedback v-if="violation !== null">
                {{ violation.message }}
            </AppInvalidFeedback>
        </AppCol>
    </AppRow>
</template>
