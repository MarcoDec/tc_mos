<script setup>
    import clone from 'clone'
    import {computed, ref} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Array},
        item: {required: true, type: Object},
        modelValue: {default: null, type: [Array, Boolean, Number, String, Object]}
    })
    const emit = defineEmits(['annuleAjout', 'update', 'update:modelValue'])
    const tabFields = computed(() =>
        props.fields.map(element => {
            const cloned = clone(element)

            if (cloned.type === 'boolean') {
                cloned.type = 'grpbutton'
            }
            return cloned
        }))

    function AnnuleUpdate() {
        emit('annuleAjout')
    }
    async function update() {
        // on ajoute à localItem, la clé de l'item
        if (props.item['@id']) localItem.value['@id'] = props.item['@id']
        if (props.item['$id']) localItem.value['$id'] = props.item['$id']
        if (props.item.id) localItem.value.id = props.item.id
        emit('annuleAjout')
        emit('update', localItem.value)
    }
    const localItem = ref({})
    localItem.value = tabFields.value.reduce((acc, field) => {
            if (field.name.includes('.')) {
                const [relation, name] = field.name.split('.')
                acc[field.name] = props.item[relation][name]
            } else {
                acc[field.name] = props.item[field.name]
            }
            return acc
        }, {})
</script>

<template>
    <td>
        <button class="btn btn-icon btn-primary btn-sm mx-2">
            <Fa icon="check" @click="update"/>
        </button>
        <button class="btn btn-danger btn-icon btn-sm" @click="AnnuleUpdate">
            <Fa icon="times"/>
        </button>
    </td>
    <td v-for="field in tabFields" :key="field.name">
        <AppInputGuesser
            id=""
            form=""
            v-model="localItem[field.name]"
            :field="field"
            :item="localItem"
            @update:model-value="modelValue"/>
    </td>
</template>
