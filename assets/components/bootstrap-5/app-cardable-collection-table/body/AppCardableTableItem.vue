<script setup>
    import {BImg} from 'bootstrap-vue-next'
    import AppSwitch from '../../../form-cardable/fieldCardable/input/AppSwitch.vue'
    import {computed} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Array},
        item: {required: true, type: Object},
        shouldDelete: {required: false, default: true},
        shouldSee: {required: false, default: true}
    })
    const id = computed(() => Number(props.item['@id'].match(/\d+/)[0]))
    const emit = defineEmits(['deleted', 'update'])
    function update(){
        emit('update', props.item)
    }
    function deleted(){
        emit('deleted', id.value)
    }
    function isObject(val) {
        if (val === null) {
            return false
        }
        return typeof val === 'function' || typeof val === 'object'
    }
</script>

<template>
    <td>
        <button v-if="shouldSee" class="btn btn-icon btn-secondary btn-sm mx-2" :title="item.id" @click="update">
            <Fa icon="eye"/>
        </button>
        <template v-if="shouldDelete">
            <button class="btn btn-danger btn-icon btn-sm mx-2" @click="deleted">
                <Fa icon="trash"/>
            </button>
        </template>
    </td>
    <td v-for="field in fields" :key="field.name" :style="{width: field.width ? `${field.width}px` : null}">
        <template v-if="item[field.name] !== null">
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
                <div class="text-center">
                    {{ item[field.name].value }} {{ item[field.name].code }}
                </div>
            </div>
            <div v-else-if="field.type === 'date'">
                {{ item[field.name].substring(0, 10) }}
            </div>
            <div v-else-if="field.type === 'boolean'">
                <AppSwitch :id="`${field.name}_${id}`" :disabled="true" :field="field" form="" :model-value="item[field.name]"/>
            </div>
            <div v-else-if="field.type === 'multiselect-fetch'">
                {{ item[field.name][field.filteredProperty] }}
            </div>
            <div v-else-if="field.type === 'link'">
                <a v-if="item[field.name] !== null && item[field.name] !== ''" :href="item[field.name]" target="_blank">Download file</a>
            </div>
            <div v-else-if="field.type === 'img'" class="text-center">
                <BImg v-if="item[field.name].length > 0" thumbnail fluid :src="item[field.name]" alt="Image 1"/>
                <span v-else class="font-xsmall text-secondary">Image non disponible</span>
            </div>
            <div v-else>
                <span v-if="isObject(item[field.name])" class="bg-danger text-white">Object given for field '{{ field.name }}' - {{ item[field.name] }}</span>
                <span v-else>{{ item[field.name] }}</span>
            </div>
        </template>
    </td>
</template>
