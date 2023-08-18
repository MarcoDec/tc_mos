<script setup>
    import {defineEmits, defineProps} from 'vue'
    import AppTableItemField from '../../../table/body/read/AppTableItemField.vue'
    const props = defineProps({
        fields: {required: true, type: Array},
        item: {required: true, type: Object},
        indice: {required: true, type: Number},
        shouldDelete: {required: false, default: true}
    })

    const emit = defineEmits(['deleted', 'update'])
    function update(){
        emit('update', props.item)
    }
    function deleted(){
        const id = Number(props.item['@id'].match(/\d+/)[0])
        emit('deleted', id)
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
        <button class="btn btn-icon btn-secondary btn-sm mx-2" @click="update">
            <Fa icon="eye"/>
        </button>
        <template v-if="shouldDelete">
            <button class="btn btn-danger btn-icon btn-sm mx-2" @click="deleted">
                <Fa icon="trash"/>
            </button>
        </template>
    </td>
    <AppTableItemField v-for="field in fields" :key="field.name" :field="field" :item="item" :row="indice + field.name"/>
</template>
