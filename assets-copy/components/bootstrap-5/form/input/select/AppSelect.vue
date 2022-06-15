<script setup>
    import AppSelectContent from './AppSelectContent.vue'
    import {computed} from 'vue'
    import {useRepo} from '../../../../../composition'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        field: {required: true, type: Object},
        id: {required: true, type: String},
        method: {default: null, type: String},
        modelValue: {default: null},
        size: {default: 'sm', type: String}
    })
    const repo = typeof props.field.repo === 'function' ? useRepo(props.field.repo) : null
    const options = computed(() => props.field.options ?? repo[props.method ?? 'options'])
    const sizeClass = computed(() => `form-select-${props.size}`)
    const value = computed(() => (props.modelValue !== null && typeof props.modelValue === 'object'
        ? props.modelValue['@id']
        : props.modelValue
    ))

    function input(e) {
        emit('update:modelValue', e.target.value)
    }
</script>

<template>
    <select
        :id="id"
        :class="sizeClass"
        :name="field.name"
        :value="value"
        class="form-select"
        @input="input">
        <AppSelectContent v-for="option in options" :key="option.value" :option="option"/>
    </select>
</template>
