<script setup>
    import AppFormGroup from './AppFormGroup.vue'
    import clone from 'clone'
    import {ref} from 'vue'

    const form = ref()
    const emit = defineEmits(['submit', 'update:modelValue'])
    const props = defineProps({
        fields: {required: true, type: Array},
        id: {required: true, type: String},
        modelValue: {default: () => ({}), type: Object}
    })

    function input(value) {
        const cloned = clone(props.modelValue)
        cloned[value.name] = value.value
        emit('update:modelValue', cloned)
    }

    function submit() {
        if (typeof form.value !== 'undefined')
            emit('submit', new FormData(form.value))
    }
</script>

<template>
    <form :id="id" ref="form" autocomplete="off" @submit.prevent="submit">
        <AppFormGroup
            v-for="field in fields"
            :key="field.name"
            :field="field"
            :form="id"
            :model-value="modelValue[field.name]"
            @input="input"/>
        <div class="float-start">
            <slot name="start"/>
        </div>
        <div class="float-end">
            <slot/>
        </div>
    </form>
</template>
