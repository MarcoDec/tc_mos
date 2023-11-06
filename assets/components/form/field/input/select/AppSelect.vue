<script setup>
    import {ref} from 'vue'
    import useOptions from '../../../../../stores/option/options'
    import AppOptionGroups from './AppOptionGroups.vue'
    import AppOptions from './AppOptions.vue'

    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: null, type: String}
    })
    const optionsTransfered = ref({})
    function getOptions() {
        if (typeof props.field.options.base === 'undefined') {
            if (typeof props.field.optionsList === 'undefined') {
                optionsTransfered.value = props.field.options.options
            }
            optionsTransfered.value = props.field.optionsList
        }
        const options = useOptions(props.field.options.base)
        // eslint-disable-next-line no-return-assign
        options.fetchOp().then(() => optionsTransfered.value = options.items)
    }
    getOptions()
    const emit = defineEmits(['update:modelValue'])

    function update(v) {
        emit('update:modelValue', v)
    }

    function input(e) {
        update(e.target.value)
    }
</script>

<template>
    <AppMultiselect
        v-if="field.big"
        :id="id"
        :disabled="disabled"
        :field="field"
        :form="form"
        :options="field.options && field.options.options "
        :value-prop="field.options && field.options.valueProp "
        :model-value="modelValue"
        mode="single"
        @update:model-value="update"/>
    <select
        v-else
        :id="id"
        :disabled="disabled"
        :form="form"
        :name="field.name"
        :value="modelValue"
        class="form-select form-select-sm"
        @input="input"
        @update:model-value="update">
        <AppOptionGroups v-if="field.hasGroups" :groups="field.groups"/>
        <AppOptions v-else :options="optionsTransfered"/>
    </select>
</template>
