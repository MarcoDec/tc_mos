<script setup>
    import {computed, ref} from 'vue'
    import {get} from 'lodash'
    import api from '../../../../api'

    const props = defineProps({
        field: {required: true, type: Object},
        item: {required: true, type: Object},
        initialField: {default: null, required: false, type: Object},
        row: {required: true, type: String}
    })
    const keySelect = ref(0)
    const itemMultiSelectFetchLoaded = []

    function labelValue(thevalue) {
        if (props.field.type === 'measure'){
            if (thevalue.value.value === 'undefined' || thevalue.value.code === 'undefined') return thevalue.value
            return `${thevalue.value.value} ${thevalue.value.code}`
        }
        if (props.field.type === 'select') {
            const res = props.field.options.options.find(e => e.value === thevalue.value)
            if (typeof res === 'undefined') return thevalue.value
            return res.text
        }
        if (props.field.type === 'multiselect-fetch') {
            // On regarde si on a déjà chargé les données
            //console.log('multi select fetch', props.initialField.filteredProperty, thevalue.value)
            if (typeof thevalue.value[props.initialField.filteredProperty] === 'undefined') {
                //on charge l'élément pour récupérer les données
                thevalue.value.forEach(item => {
                    //console.log('Chargement de l\'élément', item['@id'])
                    api(item['@id'], 'GET').then(res => {
                        itemMultiSelectFetchLoaded.push(res[props.initialField.filteredProperty])
                        //console.log('Fin de chargement de l\'élément', item['@id'], itemMultiSelectFetchLoaded)
                        keySelect.value++
                    })
                })
            }
            return thevalue[props.initialField.filteredProperty]
        }
        if (typeof props.field.labelValue === 'function') {
            return props.field.labelValue(value.value)
        }
        return thevalue.value
    }
    const bool = computed(() => props.field.type === 'boolean')
    const color = computed(() => props.field.type === 'color')
    const id = computed(() => `${props.row}-${props.field.name}`)
    const value = computed(() => {
        //console.log('props.item', props.item)
        //console.log('props.field.name', props.field.name, props.field.type)
        //console.log('get(props.item, props.field.name)', get(props.item, props.field.name))
        return get(props.item, props.field.name)
    })
    const label = computed(() => labelValue(value))
    const input = computed(() => `${id.value}-input`)
    const array = computed(() => Array.isArray(label.value))
    const select = computed(() => props.field.type === 'select')
    const multiselectFetch = computed(() => props.field.type === 'multiselect-fetch')
</script>

<template>
    <td :id="id">
        <AppInputGuesser v-if="bool" :id="input" :field="field" :model-value="label" disabled form="none"/>
        <div v-else-if="color" class="row">
            <div v-if="!field.hideLabelValue" class="col-2">
                {{ label }}
            </div>
            <div class="col">
                <AppInputGuesser :id="input" :field="field" :model-value="label" disabled form="none"/>
            </div>
        </div>
        <ul v-else-if="array">
            <li v-for="(v, i) in label" :key="i">
                <span v-if="field.type === 'multiselect-fetch'">
                    test {{ v['@id'] }} api:{{ initialField.api }} filteredProperty:{{ initialField.filteredProperty }}
                </span>
                <span v-else>{{ v }}</span>
            </li>
        </ul>
        <div v-else-if="select" :key="keySelect">
            {{ label }}
        </div>
        <ul v-else-if="multiselectFetch" :key="keySelect">
            <li v-for="(v, i) in itemMultiSelectFetchLoaded" :key="i">
                {{ v }}
            </li>
        </ul>
        <template v-else>
            {{ label }}
        </template>
    </td>
</template>
