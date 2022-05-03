<script setup>
    import {computed, ref} from 'vue'
    import AppFormGroup from './AppFormGroup.vue'
    import {FiniteStateMachineRepository} from '../../../store/modules'
    import {set} from 'lodash'
    import {useRepo} from '../../../composition'

    const emit = defineEmits(['submit', 'update:modelValue'])
    const form = ref()
    const props = defineProps({
        dInline: {type: Boolean},
        fields: {required: true, type: Array},
        id: {required: true, type: String},
        inline: {type: Boolean},
        modelValue: {default: () => ({}), type: Object},
        multipart: {type: Boolean},
        stateMachine: {default: null, type: String}
    })
    const displayInline = computed(() => ({'d-inline': props.inline, 'm-0': props.inline, 'p-0': props.inline}))
    const repo = useRepo(FiniteStateMachineRepository)
    const state = computed(() => (props.inline ? null : repo.find(props.stateMachine)))
    const error = computed(() => state.value?.error ?? null)
    const loading = computed(() => state.value?.loading ?? false)
    const status = computed(() => state.value?.status ?? 200)
    const dInlineCss = computed(() => ({'d-flex': props.dInline}))
    const dInlineCssGroup = computed(() => ({'flex-fill': props.dInline, 'm-1': props.dInline}))

    function input(value) {
        emit('update:modelValue', {...props.modelValue, [value.name]: value.value})
    }

    function submit() {
        const data = new FormData(form.value)
        if (props.multipart) {
            emit('submit', data)
            return
        }

        const json = {}
        for (const [key, value] of data.entries()) {
            const normalizedValue = props.fields.find(field => field.name === key)?.type === 'number' ? parseFloat(value) : value
            if (normalizedValue === null || typeof normalizedValue === 'undefined')
                continue
            if (typeof normalizedValue === 'number' && isNaN(normalizedValue))
                continue
            if (typeof normalizedValue === 'string' && normalizedValue.length === 0)
                continue
            set(json, key, normalizedValue)
        }
        emit('submit', json)
    }
</script>

<template>
    <form v-if="inline" :id="id" ref="form" :class="displayInline" autocomplete="off" @submit.prevent="submit">
        <slot/>
    </form>
    <AppOverlay v-else :loading="loading">
        <AppAlert v-if="error !== null">
            <AppBadge>{{ status }}</AppBadge>
            {{ error }}
        </AppAlert>
        <form :id="id" ref="form" :class="dInlineCss" autocomplete="off" @submit.prevent="submit">
            <AppFormGroup
                v-for="field in fields"
                :key="field.name"
                :class="dInlineCssGroup"
                :field="field"
                :form="id"
                :model-value="modelValue[field.name]"
                :state-machine="stateMachine"
                @input="input"/>
            <div class="d-flex justify-content-between">
                <div>
                    <slot name="start"/>
                </div>
                <div>
                    <slot/>
                </div>
            </div>
        </form>
    </AppOverlay>
</template>
