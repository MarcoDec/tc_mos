<script setup>
    import clone from 'clone'
    import {computed, ref} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Array},
        form: {required: true, type: String},
        item: {required: true, type: Object}
    })
    const emit = defineEmits(['annuleUpdate', 'updateItems', 'update:modelValue'])
    console.log('props', props)
    const localItem = ref({})
    // localItem.value = Object.assign({}, props.item)
    localItem.value = {...props.item}
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
    async function updateItems() {
        emit('updateItems', localItem.value)
        emit('annuleUpdate')
    }
</script>

<template>
    <td>
        <button class="btn btn-icon btn-primary btn-sm mx-2">
            <Fa icon="check" @click="updateItems"/>
        </button>
        <button class="btn btn-danger btn-icon btn-sm" @click="annuleUpdated">
            <Fa icon="times"/>
        </button>
    </td>
    <template v-for="field in tabFields" :key="field.name">
        <td v-if="field.name !== 'prices'">
            <AppInputGuesser
                :id="field.name"
                v-model="localItem[field.name]"
                :field="field"
                :form="form"
                :item="localItem"/>
        </td>
    </template>
</template>
