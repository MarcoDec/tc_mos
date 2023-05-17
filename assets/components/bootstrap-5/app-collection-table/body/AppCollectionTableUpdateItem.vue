<script setup>
    import {computed, defineEmits, defineProps} from 'vue'
    import clone from 'clone'

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
        emit('annuleAjout')
        emit('update', props.item)
    }
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
            v-model="item[field.name]"
            :field="field"
            :item="item"
            @update:model-value="modelValue"/>
    </td>
</template>
