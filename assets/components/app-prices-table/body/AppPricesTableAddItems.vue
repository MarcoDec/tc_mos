<script setup>
    import {defineProps, defineEmits, reactive} from 'vue'

    defineProps({
        fields: {required: true, type: Array},
        form: {required: true, type: String}
    })

    const emit = defineEmits(['addItem'])

    const formData = reactive({})

    function onUpdateModelValue(event, fieldName) {
        if (typeof event === 'object' && event !== null) {
            formData[fieldName] = {...formData[fieldName], ...event}
        } else {
            formData[fieldName] = event
        }
    }
    function addItem() {
        emit('addItem', formData)
    }
</script>

<template>
    <tr class="text-center">
        <td>
            <button class="btn btn-icon btn-success btn-sm mx-2" @click="addItem">
                <Fa icon="plus"/>
            </button>
        </td>
        <template v-for="field in fields" :key="field.name">
            <td v-if="field.type !== null && field.name !== 'prices' ">
                <AppInputGuesser
                    :id="field.name"
                    :form="form"
                    :field="field"
                    no-label
                    @update:model-value="onUpdateModelValue($event, field.name)"/>
            </td>
            <td v-else colspan="4"/>
        </template>
    </tr>
</template>

<style scoped>
    td {
        font-size: xx-small !important;
    }
</style>