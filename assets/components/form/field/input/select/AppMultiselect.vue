<script setup>
    import Multiselect from '@vueform/multiselect'
    import {readonly, onErrorCaptured, ref, toRefs} from 'vue'

    const {disabled, field, form, id, mode, modelValue} = toRefs(defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        mode: {default: 'tags', type: String},
        modelValue: {default: null, type: [Array, String]}
    }))
    const localModelValue = ref(modelValue.value)
    if (typeof localModelValue.value === 'string') {
        localModelValue.value = [localModelValue.value]
    }
    const emit = defineEmits(['update:modelValue', 'searchChange'])
    const css = readonly({search: 'form-control form-control-sm'})
    function input(value) {
        emit('update:modelValue', value)
    }
    function updateSearch(data) {
        if (data !== '') {
            emit('searchChange', data)
        }
    }
    onErrorCaptured((err, compInst, errorInfo) => {
        console.log(err, compInst, errorInfo)
        return false
    })
</script>

<template>
    <Multiselect
        :id="id"
        :classes="css"
        :data-mode="mode"
        :disabled="disabled"
        :form="form"
        :max="field.max ?? -1"
        :mode="mode"
        :model-value="localModelValue"
        :options="field.options && field.options.options "
        :value-prop="field.options && field.options.valueProp "
        class="text-dark"
        label="text"
        :searchable="true"
        @search-change="updateSearch"
        @update:model-value="input">
        <template #afterlist>
            <input :name="field.name" :value="localModelValue" type="hidden"/>
        </template>
    </Multiselect>
</template>

<style src="@vueform/multiselect/themes/default.css"/>
