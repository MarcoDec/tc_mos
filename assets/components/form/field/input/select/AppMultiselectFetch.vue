<script setup>
    import {computed, ref} from 'vue'
    import api from '../../../../../api'
    import AppMultiselect from './AppMultiselect.vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'

    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        mode: {default: 'tags', type: String},
        modelValue: {default: null, type: [Array, String]}
    })
    const emit = defineEmits(['searchChange', 'update:modelValue'])
    const fetchCriteria = useFetchCriteria(`${props.form}FetchCriteria`)
    const items = ref([])
    async function updateItems() {
        const response = await api(`${props.field.api}${fetchCriteria.getFetchCriteria}`, 'GET')
        items.value = response['hydra:member']
    }
    const options = computed(() => items.value.map(item => ({text: `${item[props.field.filteredProperty]}`, value: item['@id']})))
    const multiselectField = computed(() => ({
        label: props.field.label,
        name: props.field.name,
        options: {label: value => options.value.find(option => option.value === value)?.text ?? null, options: options.value}
    }))
    async function onSearchChange(data) {
        console.log('onSearchChange', data)
        emit('searchChange', data)
        if (data === '') fetchCriteria.resetAllFilter()
        else fetchCriteria.addFilter(props.field.filteredProperty, data)
        await updateItems()
        console.log('update multiselectField.options', multiselectField.value.options)
    }
    function onUpdateModelValue(value) {
        emit('update:modelValue', value)
    }
</script>

<template>
    <AppMultiselect
        :id="id"
        :form="form"
        :field="multiselectField"
        :data-mode="mode"
        :disabled="disabled"
        :max="field.max ?? -1"
        :mode="mode"
        :model-value="modelValue"
        :options="multiselectField.options && multiselectField.options.options "
        :value-prop="multiselectField.options && multiselectField.options.valueProp "
        class="text-dark"
        label="text"
        :searchable="true"
        @search-change="onSearchChange"
        @update:model-value="onUpdateModelValue"/>
</template>

<style scoped>

</style>
