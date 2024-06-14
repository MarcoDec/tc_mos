<script setup>
    import {computed, defineProps, ref} from 'vue'
    import {isObject} from '@vueuse/core'
    import AppSwitch from '../../form-cardable/fieldCardable/input/AppSwitch.vue'

    const props = defineProps({
        item: {required: true, type: Object},
        field: {required: true, type: Object},
        index: {required: true, type: Number}
    })
    const multiSelectResults = ref([])
    const id = computed(() => Number(props.item['@id'].match(/\d+/)[0]))
</script>

<template v-if="(item.prices[index][field.name] !== null)">
    <div v-if="field.type === 'select'">
        <template v-if="isObject(item.prices[index][field.name])">
            <span v-if="field.options.label(item.prices[index][field.name]['@id']) !== null">{{ field.options.label(item.prices[index][field.name]['@id']) }}</span>
            <span v-else>{{ item.prices[index][field.name] }}</span>
        </template>
        <template v-else>
            <span v-if="field.options.label(item.prices[index][field.name]) !== null">{{ field.options.label(item.prices[index][field.name]) }}</span>
            <span v-else>{{ item.prices[index][field.name] }}</span>
        </template>
    </div>
    <div v-else-if="field.type === 'measure'">
        <div v-if="field.measure.code.type === 'select'" class="text-center">
            <span> {{ item.prices[index][field.name].value }} </span>
            <span v-if="field.measure.code.options.label(item.prices[index][field.name].code) !== null">{{ field.measure.code.options.label(item.prices[index][field.name].code) }}</span>
            <span v-else>{{ item.prices[index][field.name].code }}</span>
        </div>
    </div>
    <div v-else-if="field.type === 'date'">
        {{ item.prices[index][field.name].substring(0, 10) }}
    </div>
    <div v-else-if="field.type === 'boolean'">
        <AppSwitch :id="`${field.name}_${id}`" :disabled="true" :field="field" form="" :model-value="item.prices[index][field.name]"/>
    </div>
    <div v-else-if="field.type === 'multiselect-fetch'">
        {{ multiSelectResults[field.name] }}
    </div>
    <div v-else-if="field.type === 'link'">
        <a v-if="item.prices[index][field.name] !== null && item.prices[index][field.name] !== ''" :href="item.prices[index][field.name]" target="_blank">Download file</a>
    </div>
    <div v-else>
        <span v-if="isObject(item.prices[index][field.name])" class="bg-danger text-white">Object given for field '{{ field.name }}' - {{ item.prices[index][field.name] }}</span>
        <span v-else>{{ item.prices[index][field.name] }}</span>
    </div>
</template>

