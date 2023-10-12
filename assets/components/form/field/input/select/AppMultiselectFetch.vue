<script setup>
    /**
     * Ce composant permet l'utilisation d'un système d'auto-complétion pour un simple select ou un multiselect à partir
     * de données chargées depuis une api.
     *
     * Les paramètres essentiels pour qu'il faut avoir défini pour un fonctionnement correct sont:
     * - `props.field.api` : défini la base de l'url de l'api qui sera utilisé
     * - `props.field.filteredProperty` : défini le nom de la propriété de l'object qui sera utilisé pour le filtre
     * - `props.field.max` : défini le nombre max d'élément sélectionnable. Si cette valeur vaut 1, alors c'est un simple select.
     */
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
    const options = computed(() => items.value.map(item => ({text: `${item[props.field.filteredProperty]}`, value: item['@id']})))
    const multiselectField = computed(() => ({
        label: props.field.label,
        name: props.field.name,
        options: {label: value => options.value.find(option => option.value === value)?.text ?? null, options: options.value}
    }))
    if (Array.isArray(props.modelValue) && props.modelValue.length > 0) {
        console.log('modelValue is an array with at least one element', props.modelValue)
        // on charge les données de l'api et on les mets dans items pour qu'options soit mis à jour
        props.modelValue.forEach(value => {
            api(value, 'GET').then(response => items.value.push(response))
        })
        console.log('items', items.value)
        console.log('options', options.value)
    }
    async function updateItems() {
        try {
            const response = await api(`${props.field.api}${fetchCriteria.getFetchCriteria}`, 'GET')
            items.value = response['hydra:member']
        } catch (e) {
            console.debug(e)
        }
    }
    async function onSearchChange(data) {
        if (data !== '') {
            emit('searchChange', data)
            fetchCriteria.addFilter(props.field.filteredProperty, data)
        }
        await updateItems()
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
