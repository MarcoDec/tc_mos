<script setup>
    import AppSelectOption from './AppSelectOption'
    import {computed} from 'vue'
    import {useRepo} from '../../../../composition'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        field: {required: true, type: Object},
        modelValue: {default: null},
        size: {default: 'sm', type: String}
    })
    const repo = useRepo(props.field.repo)
    const options = computed(() => repo.options)
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
        :id="field.id"
        :class="sizeClass"
        :name="field.name"
        :value="value"
        class="form-select"
        @input="input">
        <AppSelectOption v-for="option in options" :key="option.value" :option="option"/>
    </select>
</template>
