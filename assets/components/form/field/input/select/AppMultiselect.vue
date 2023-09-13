<script setup>
    import Multiselect from '@vueform/multiselect'
    import {readonly} from 'vue'

    /*const props = */defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        mode: {default: 'tags', type: String},
        modelValue: {default: null, type: [Array, String]}
    })
    const emit = defineEmits(['update:modelValue', 'searchChange'])
    const css = readonly({search: 'form-control form-control-sm'})
    function input(value) {
        emit('update:modelValue', value)
    }
    function updateSearch(data) {
        emit('searchChange', data)
    }
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
        :model-value="modelValue"
        :options="field.options && field.options.options "
        :value-prop="field.options && field.options.valueProp "
        class="text-dark"
        label="text"
        :searchable="true"
        @search-change="updateSearch"
        @update:model-value="input">
        <template #afterlist>
            <input :name="field.name" :value="modelValue" type="hidden"/>
        </template>
    </Multiselect>
</template>

<style src="@vueform/multiselect/themes/default.css"/>
