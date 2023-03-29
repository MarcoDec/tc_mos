<script setup>
    import AppInputGuesserJS from '../../../form/field/input/AppInputGuesserJS'
    import {computed} from 'vue'
    import {get} from 'lodash'

    const props = defineProps({
        field: {required: true, type: Object},
        item: {required: true, type: Object},
        row: {required: true, type: String}
    })
    function labelValue(thevalue) {
        if (props.field.type === 'select') {
            const res = props.field.options.options.find(e => e.value === thevalue.value)
            if (typeof res === 'undefined') {
                console.debug('AppTableItemField.vue res undefined', props.field, thevalue.value)
                return thevalue.value
            }
            return res.text
        }
        if (props.field.type === 'measure') {
            const res = thevalue.value.value + ' '+ thevalue.value.code
            if (typeof res === 'undefined') {
                console.debug('AppTableItemField.vue res undefined', props.field, thevalue.value)
                return thevalue.value
            }
            return res
        }
        //TODO: gérer Multiselect et measures

        return thevalue.value
    }
    const bool = computed(() => props.field.type === 'boolean')
    const color = computed(() => props.field.type === 'color')
    const id = computed(() => `${props.row}-${props.field.name}`)
    const value = computed(() => get(props.item, props.field.name))
    const label = computed(() => labelValue(value)) //computed(() => props.field.label) labelValue(value.value)
    const input = computed(() => `${id.value}-input`)
    const array = computed(() => Array.isArray(label.value))
</script>

<template>
    <td :id="id">
        <AppInputGuesserJS v-if="bool" :id="input" :field="field" :model-value="label" disabled form="none"/>
        <div v-else-if="color" class="row">
            <div v-if="!field.hideLabelValue" class="col-2">
                {{ label }}
            </div>
            <div class="col">
                <AppInputGuesserJS :id="input" :field="field" :model-value="label" disabled form="none"/>
            </div>
        </div>
        <ul v-else-if="array">
            <li v-for="(v, i) in label" :key="i">
                {{ v }}
            </li>
        </ul>
        <template v-else>
            {{ label }}
        </template>
    </td>
</template>
