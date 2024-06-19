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
    import {computed, onBeforeMount, onBeforeUpdate, ref} from 'vue'
    import api from '../../../../../api'
    import AppMultiselect from './AppMultiselect.vue'
    import useFetchCriteria from '../../../../../stores/fetch-criteria/fetchCriteria'

    const key = ref(0)
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        mode: {default: 'tags', type: String},
        modelValue: {default: null, type: [Array, String]}
    })
    const localModelValue = ref({})
    // Initialisation de localModelValue
    if (props.field.max === 1) {
        localModelValue.value = [props.modelValue]
    } else {
        localModelValue.value = props.modelValue
    }
    const emit = defineEmits(['searchChange', 'update:modelValue'])
    const fetchCriteria = useFetchCriteria(`${props.form}FetchCriteria${props.field.name}`)
    const items = ref([])
    const options = computed(() => items.value.map(item => ({text: `${item[props.field.filteredProperty]}`, value: item['@id']})))
    const multiselectField = computed(() => ({
        label: props.field.label,
        name: props.field.name,
        options: {label: value => options.value.find(option => option.value === value)?.text ?? null, options: options.value}
    }))
    const newModelValue = ref([])
    if (Array.isArray(localModelValue.value) && localModelValue.value.length > 0) {
        // on charge les données de l'api et on les met dans items pour que la variable 'options' soit mise à jour
        localModelValue.value.forEach(value => {
            //console.log('value', value)
            if (value !== null) {
                if (typeof value['@id'] === 'undefined') api(value, 'GET').then(response => {
                    items.value.push(response)
                    newModelValue.value.push(response['@id'])
                    key.value++
                })
                else api(value['@id'], 'GET').then(response => {
                    items.value.push(response)
                    newModelValue.value.push(response['@id'])
                    key.value++
                })
            }
        })
    }
    async function updateItems() {
        // console.info('updateItems')
        try {
            const response = await api(`${props.field.api}${fetchCriteria.getFetchCriteria}`, 'GET')
            items.value = response['hydra:member']
        } catch (e) {
            console.debug(e)
        }
    }
    async function onSearchChange(data) {
        // console.info('onSearchChange')
        if (data !== '') {
            emit('searchChange', data)
            fetchCriteria.addFilter(props.field.filteredProperty, data)
        }
        await updateItems()
    }
    async function onUpdateModelValue(value) {
        // console.info('onUpdateModelValue', value, props.field.max === 1)
        if (props.field.max === 1) emit('update:modelValue', value[0] ?? null)
        else emit('update:modelValue', value)
        if (value.length > 0) await updateItems()
    }
    onBeforeMount(() => {
        // console.info('onBeforeMount')
        if (props.field.permanentFilters) {
            props.field.permanentFilters.forEach(filter => {
                // console.log('filter', filter)
                if (filter !== null) fetchCriteria.addFilter(filter.field, filter.value)
            })
        }
    })
    onBeforeUpdate(() => {
        // console.info('onBeforeUpdate')
        if (props.field.permanentFilters) {
            props.field.permanentFilters.forEach(filter => {
                // console.log('filter', filter)
                if (filter !== null) fetchCriteria.addFilter(filter.field, filter.value)
            })
        }
    })
</script>

<template>
    <AppMultiselect
        :id="id"
        :key="key"
        :form="form"
        :field="multiselectField"
        :data-mode="mode"
        :disabled="disabled"
        :max="field.max ?? -1"
        :mode="mode"
        :model-value="newModelValue"
        :options="multiselectField.options && multiselectField.options.options "
        :value-prop="multiselectField.options && multiselectField.options.valueProp "
        class="text-dark"
        label="text"
        :searchable="true"
        @search-change="onSearchChange"
        @update:model-value="onUpdateModelValue"/>
</template>
