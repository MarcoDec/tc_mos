<script setup>
    import Multiselect from '@vueform/multiselect'
    import {readonly} from 'vue'

    defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        mode: {default: 'tags', type: String},
        modelValue: {default: null, type: [Array, String]}
    })
    const emit = defineEmits(['update:modelValue'])
    const css = readonly({search: 'form-control form-control-sm'})

    function input(value) {
        //console.log('multi', value)
        emit('update:modelValue', value)
    }
</script>

<template>
    <Multiselect
        :id="id"
        :classes="css"
        :data-mode="mode"
        :disabled="disabled"
        :form="form"
        :mode="mode"
        :model-value="modelValue"
        :options="field.optionsList"
        :value-prop="field.options.valueProp"
        class="text-dark"
        label="text"
        searchable
        @update:model-value="input">
        <template #afterlist>
            <input :name="field.name" :value="modelValue" type="hidden"/>
        </template>
    </Multiselect>
</template>

<style src="@vueform/multiselect/themes/default.css"/>
