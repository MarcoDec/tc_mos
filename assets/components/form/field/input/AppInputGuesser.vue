<script setup>
    /* eslint-disable vue/no-unused-properties */
    import AppInput from './AppInput.vue'
    import AppSelect from './select/AppSelect.vue'
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: null, type: String}
    })
    const type = computed(() => {
        switch (props.field.type) {
            case 'select':
                return AppSelect
            default:
                return AppInput
        }
    })

    function input(v) {
        emit('update:modelValue', v)
    }
</script>

<template>
    <component :is="type" v-bind="$props" @update:model-value="input"/>
</template>
