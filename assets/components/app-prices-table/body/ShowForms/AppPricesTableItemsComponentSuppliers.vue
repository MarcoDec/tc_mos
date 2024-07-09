<script setup>
import {computed, defineProps, ref} from 'vue'
import {isObject} from '@vueuse/core'
import AppSwitch from '../../../form-cardable/fieldCardable/input/AppSwitch.vue'
import api from "../../../../api";

    const props = defineProps({
        item: {required: true, type: Object},
        field: {required: true, type: Object},
        index: {required: true, type: Number}
    })
    // console.log('props', props.item[props.field.name])
    const multiSelectResults = ref([])
    if (props.field.type === 'multiselect-fetch') {
        if (typeof props.item[props.field.name] === 'object') {
            multiSelectResults.value = await api(props.item[props.field.name]['@id'], 'GET')
        } else {
            multiSelectResults.value =await api(props.item[props.field.name], 'GET')
        }
    }
    const id = computed(() => Number(props.item['@id'].match(/\d+/)[0]))
</script>

<template>
    <td v-if="field.name !== 'prices'">
        <template v-if="item[field.name] !== null">
            <div v-if="field.name !== 'prices'">
                <div v-if="field.type === 'select'">
                    <template v-if="isObject(item[field.name])">
                        <span v-if="field.options.label(item[field.name]['@id']) !== null">{{ field.options.label(item[field.name]['@id']) }}</span>
                        <span v-else>{{ item[field.name] }}</span>
                    </template>
                    <template v-else>
                        <span v-if="field.options.label(item[field.name]) !== null">{{ field.options.label(item[field.name]) }}</span>
                        <span v-else>{{ item[field.name] }}</span>
                    </template>
                </div>
                <div v-else-if="field.type === 'measure'">
                    <div v-if="field.measure.code.type === 'select'" class="text-center">
                        <span> {{ item[field.name].value }} </span>
                        <span v-if="field.measure.code.options.label(item[field.name].code) !== null">{{ field.measure.code.options.label(item[field.name].code) }}</span>
                        <span v-else>{{ item[field.name].code }}</span>
                    </div>
                </div>
                <div v-else-if="field.type === 'date'">
                    {{ item[field.name].substring(0, 10) }}
                </div>
                <div v-else-if="field.type === 'boolean'">
                    <AppSwitch :id="`${field.name}_${id}`" :disabled="true" :field="field" form="" :model-value="item[field.name]"/>
                </div>
                <div v-else-if="field.type === 'multiselect-fetch'">
                    {{ multiSelectResults[field.filteredProperty] }}
                </div>
                <div v-else-if="field.type === 'link'">
                    <a v-if="item[field.name] !== null && item[field.name] !== ''" :href="item[field.name]" target="_blank">Download file</a>
                </div>
                <div v-else>
                    <span v-if="isObject(item[field.name])" class="bg-danger text-white">Object given for field '{{ field.name }}' - {{ item[field.name] }}</span>
                    <span v-else>{{ item[field.name] }}</span>
                </div>
            </div>
        </template>
    </td>
</template>
