<script setup>
    import clone from 'clone'
    import {computed} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Array},
        form: {required: true, type: String},
        item: {required: true, type: Object},
        rowspan: {default: null, type: Number},
        index: {default: null, type: Number}
    })
    const emit = defineEmits(['annuleUpdate', 'updateItems', 'update:modelValue'])
    const tabFields = computed(() =>
        props.fields.map(element => {
            const cloned = clone(element)

            if (cloned.type === 'boolean') {
                cloned.type = 'grpbutton'
            }
            return cloned
        }))
    function annuleUpdated() {
        emit('annuleUpdate')
    }
    async function updateItems(item) {
        emit('updateItems', item)
        emit('annuleUpdate')
    }
</script>

<template>
    <td v-if="(index === 0)" :rowspan="rowspan">
        <button class="btn btn-icon btn-primary btn-sm mx-2">
            <Fa icon="check" @click="updateItems(item)"/>
        </button>
        <button class="btn btn-danger btn-icon btn-sm" @click="annuleUpdated">
            <Fa icon="times"/>
        </button>
    </td>
    <template v-for="field in tabFields" :key="field.name">
        <td v-if="index === 0 && field.name !== 'prices'" :rowspan="rowspan">
            <AppInputGuesser
                :id="field.name"
                v-model="item[field.name]"
                :field="field"
                :form="form"
                :item="item"/>
        </td>
    </template>
</template>
