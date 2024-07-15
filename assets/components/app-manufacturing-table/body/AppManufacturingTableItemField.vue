<script setup>
    import {defineProps, ref} from 'vue'

    const emits = defineEmits(['update:modelValue'])
    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        item: {required: true, type: Object},
        title: {required: true, type: String}
    })
    const localItemValue = ref(null)
    localItemValue.value = props.item[props.field.name]
    const isBlocked = props.item.Blocker === 'blocked'
    function onSelectChange(value) {
        localItemValue.value = value
        emits('update:modelValue', localItemValue.value)
    }
</script>

<template>
    <template v-if="title === 'collapse new Ofs'">
        <td v-if="field.type === 'date' || field.type === 'select' || field.type === 'boolean'">
            <AppInputGuesser :id="id" :field="field" :model-value="localItemValue" no-label :form="form"/>
        </td>
        <td v-else>
            {{ localItemValue }}
        </td>
    </template>
    <template v-else-if="title === 'collapse ofs ToConfirm'">
        <td v-if="field.type === 'boolean'" :class="{'bg-warning': isBlocked}">
            <AppInputGuesser :id="id" :field="field" :model-value="localItemValue" no-label :form="form" @update:model-value="onSelectChange"/>
        </td>
        <td v-else :class="{'bg-warning': isBlocked, 'bg-info': !isBlocked}">
            {{ localItemValue }}
        </td>
    </template>
    <template v-else>
        <td :class="{'bg-warning': isBlocked, 'bg-success': !isBlocked}">
            {{ localItemValue }}
        </td>
    </template>
</template>
