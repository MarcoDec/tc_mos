<script setup>
    import {computed, onMounted, ref, watch} from 'vue'
    import {get} from 'lodash'
    import api from '../../../../api'

    const props = defineProps({
        field: {required: true, type: Object, validator: field => field !== null && 'name' in field},
        item: {required: true, type: Object},
        initialField: {default: null, required: false, type: Object},
        row: {required: true, type: String}
    })
    // console.info(props)
    const keySelect = ref(0)
    const itemMultiSelectFetchLoaded = ref([])
    /*
        * Récupère la valeur d'un champ en fonction de son type
     */
    const getLabelValue = thevalue => {
        if (!props.field || !thevalue) return ''
        // console.log(props.field.type)
        switch (props.field.type) {
            case 'address':
                return theValue.address || ''
            case 'text':
            case 'textarea':
            case 'link':
                return thevalue || ''
            case 'boolean':
                return thevalue ? 'Oui' : 'Non'
            case 'color': {
                if (props.field.optionsList?.length > 0) return props.field.optionsList.find(element => element.value === thevalue).text
                return thevalue || '#000000'
            }
            case 'date':
            case 'datetime-local':
                return new Date(thevalue).toLocaleString()
            case 'time':
                return thevalue || ''

            case 'file':
                return thevalue.name || ''

            case 'grpbutton':
            case 'rating':
            case 'trafficLight': {
                // TODO: implémenter la logique pour ces types
                return ''
            }

            case 'int':
            case 'number':
                return Number(thevalue) || 0

            case 'measure': {
                if (!thevalue.value || !thevalue.code) return ''
                return `${thevalue.value} ${thevalue.code}`
            }

            case 'measureSelect':
                // TODO: implémenter la logique pour ce type
                return ''

            case 'multiselect':
                return thevalue.map(v => v.text).join(', ') || ''

            case 'multiselect-fetch': {
                // console.log('multiselect-fetch', thevalue, itemMultiSelectFetchLoaded.value)
                if (typeof thevalue === 'string') {
                    api(thevalue, 'GET').then(res => {
                        itemMultiSelectFetchLoaded.value.push(res[props.initialField.filteredProperty])
                        return res[props.initialField.filteredProperty] || ''
                    })
                } else {
                    const allPromises = []
                    thevalue.forEach(item => {
                        allPromises.push(api(item['@id'], 'GET').then(res => {
                            itemMultiSelectFetchLoaded.value.push(res[props.initialField.filteredProperty])
                        }))
                        Promise.all(allPromises).then(() => itemMultiSelectFetchLoaded.value.join(', ') || '')
                    })
                }
                return ''
            }
            case 'password':
                return '******'

            case 'select': {
                if (typeof thevalue === 'object') return props.field.options.options.find(e => e.value === thevalue['@id']).text
                const selectedOption = props.field.options.options.find(e => e.value === thevalue)
                return selectedOption ? selectedOption.text : ''
            }

            default:
                return thevalue || ''
        }
    }

    const bool = computed(() => props.field.type === 'boolean')
    const color = computed(() => props.field.type === 'color')
    const date = computed(() => ['date', 'datetime-local', 'time'].includes(props.field.type))
    const shortDate = computed(() => {
        const aValue = getLabelValue(get(props.item, props.field.name))
        if (typeof aValue !== 'undefined' && String(aValue).length > 10) return String(aValue).slice(0, 10)
        return aValue
    })
    const id = computed(() => `${props.row}-${props.field.name}`)
    const value = computed(() => get(props.item, props.field.name))
    const label = computed(() => getLabelValue(value.value))
    const input = computed(() => `${id.value}-input`)
    const array = computed(() => Array.isArray(label.value))
    const select = computed(() => props.field.type === 'select')
    const multiselectFetch = computed(() => props.field.type === 'multiselect-fetch')
    const downloadText = computed(() => props.field.type === 'downloadText')

    // const fetchMultiSelectData = () => {
    //     if (multiselectFetch.value && props.item[props.field.name]) {
    //         // console.log('item', props.item, props.item[props.field.name])
    //         if (!typeof props.item[props.field.name] === 'string') {
    //             // props.item[props.field.name].forEach(item => {
    //             //     if (!itemMultiSelectFetchLoaded.value.includes(item['@id'])) {
    //             //         api(item['@id'], 'GET').then(res => {
    //             //             itemMultiSelectFetchLoaded.value.push(res[props.initialField.filteredProperty])
    //             //             keySelect.value++
    //             //         })
    //             //     }
    //             // })
    //         } else {
    //             //api(props.item[props.field.name], 'GET').then(res => {
    //             //    itemMultiSelectFetchLoaded.value.push(res[props.initialField.filteredProperty])
    //             //    keySelect.value++
    //             //})
    //         }
    //     }
    // }

    watch(() => props.item, () => {
        itemMultiSelectFetchLoaded.value = []
        // fetchMultiSelectData()
    })

    onMounted(() => {
        // fetchMultiSelectData()
    })
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
            <li v-for="(v, i) in label" :key="`array-${i}`">
                <span v-if="field.type === 'multiselect-fetch'">
                    {{ v['@id'] }} api:{{ initialField.api }} filteredProperty:{{ initialField.filteredProperty }}
                </span>
                <span v-else>{{ v }}</span>
            </li>
        </ul>
        <div v-else-if="select" :key="keySelect">
            {{ label }}
        </div>
        <ul v-else-if="multiselectFetch" :key="keySelect">
            <li v-for="(v, i) in itemMultiSelectFetchLoaded" :key="`multiselect-${i}`">
                {{ v }}
            </li>
        </ul>
        <div v-else-if="downloadText">
            <a :href="`data:text/plain;charset=utf-8,${encodeURIComponent(label)}`" target="_blank" download="zpl.txt">Télécharger</a>
        </div>
        <template v-else-if="date">
            {{ shortDate }}
        </template>
        <template v-else>
            {{ label }}
        </template>
    </td>
</template>
