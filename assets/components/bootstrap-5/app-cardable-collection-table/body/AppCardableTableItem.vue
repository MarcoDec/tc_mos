<script setup>
    import {BImg} from 'bootstrap-vue-next'
    import AppSwitch from '../../../form-cardable/fieldCardable/input/AppSwitch.vue'
    import {computed, onBeforeMount, onBeforeUpdate, ref} from 'vue'
    import api from '../../../../api'

    const props = defineProps({
        fields: {required: true, type: Array},
        item: {required: true, type: Object},
        shouldDelete: {required: false, default: true},
        shouldSee: {required: false, default: true}
    })
    const id = computed(() => Number(props.item['@id'].match(/\d+/)[0]))
    const emit = defineEmits(['deleted', 'update'])
    const isImageEnlarged = ref(false)
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
    const toggleImageSize = () => {
        isImageEnlarged.value = !isImageEnlarged.value
    }
    const multiSelectResults = ref([])
    async function updateFields() {
        // console.log('item', props.item)
        props.fields.forEach(field => {
            // console.log('field', field)
            if (field.type === 'multiselect-fetch') {
                // console.log('is multiselect', field.name)
                if (field.isGetter && field.isGetter === true) {
                    // console.log('isGetter => target =', field.target)
                    if (props.item[field.target] !== null) {
                        let url = ''
                        if (isObject(props.item[field.target])) {
                            // console.log('target is object')
                            url = props.item[field.target]['@id']
                        } else {
                            // console.log('target is not object')
                            url = props.item[field.target]
                        }
                        api(url, 'GET').then(
                            response => {
                                multiSelectResults.value[field.name] = response[field.filteredProperty]
                            }
                        )
                    }
                } else if (props.item[field.name] !== null) {
                    let url = ''
                    if (isObject(props.item[field.name])) {
                        // console.log('name is object')
                        url = props.item[field.name]['@id']
                    } else {
                        // console.log('name is not object')
                        url = props.item[field.name]
                    }
                    api(url, 'GET').then(
                        response => {
                            multiSelectResults.value[field.name] = response[field.filteredProperty]
                        }
                    )
                }
            }
        })
    }
    onBeforeMount(() => {
        updateFields()
    })
    onBeforeUpdate(() => {
        updateFields()
    })
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
                {{ multiSelectResults[field.name] }}
            </div>
            <div v-else-if="field.type === 'link'">
                <a v-if="item[field.name] !== null && item[field.name] !== ''" :href="item[field.name]" target="_blank">Download file</a>
            </div>
            <div v-else-if="field.type === 'img'" class="text-center">
                <div v-if="item[field.name].length > 0">
                    <BImg class="img-base" thumbnail fluid :src="item[field.name]" alt="Image 1" @click="toggleImageSize"/>
                    <BImg v-if="isImageEnlarged" class="image-enlarged" thumbnail fluid :src="item[field.name]" alt="Image 1" @click="toggleImageSize"/>
                </div>
                <span v-else class="font-xsmall text-secondary">Image non disponible</span>
            </div>
            <div v-else>
                <span v-if="isObject(item[field.name])" class="bg-danger text-white">Object given for field '{{ field.name }}' - {{ item[field.name] }}</span>
                <span v-else>{{ item[field.name] }}</span>
            </div>
        </template>
    </td>
</template>

<style scoped>
    .img-base {
        cursor: zoom-in;
    }
</style>
