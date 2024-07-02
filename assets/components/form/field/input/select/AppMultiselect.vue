<script setup>
    import Multiselect from '@vueform/multiselect'
    import {readonly, onErrorCaptured, ref} from 'vue'

    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        mode: {default: 'tags', type: String},
        modelValue: {default: null, type: [Array, String]}
    })
    const localModelValue = ref(props.modelValue)
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
    function deselect(data) {
        console.log('deselect', data)
        // On retire l'élément du tableau
        localModelValue.value = localModelValue.value.filter(item => item !== data)
        emit('searchChange', localModelValue.value)
    }
    onErrorCaptured((err, compInst, errorInfo) => {
        console.log(props, err, compInst, errorInfo)
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
        class="text-dark font-xsmall"
        label="text"
        :searchable="true"
        @search-change="updateSearch"
        @deselect="deselect"
        @update:model-value="input">
        <template #afterlist>
            <input :name="field.name" :value="localModelValue" type="hidden"/>
        </template>
    </Multiselect>
</template>

<style src="@vueform/multiselect/themes/default.css"/>
