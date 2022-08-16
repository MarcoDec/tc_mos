<script setup>
    import {computed, defineProps} from 'vue'
    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        item: {required: true, type: Object},
        title: {required: true, type: String}
    })
    const value = computed(() => props.item[props.field.name])
</script>

<template>
    <template v-if="title === 'collapse new Ofs'">
        <td v-if="field.type === 'date' || field.type === 'select' || field.type === 'boolean'">
            <AppInputGuesser :id="id" :field="field" :model-value="value" no-label :form="form"/>
        </td>
        <td v-else>
            {{ value }}
        </td>
    </template>
    <template v-else-if="title === 'collapse ofs ToConfirm'">
        <td v-if="field.type === 'boolean'">
            <AppInputGuesser :id="id" :field="field" :model-value="value" no-label :form="form"/>
        </td>
        <td v-else>
            {{ value }}
        </td>
    </template>
    <template v-else>
        <td>
            {{ value }}
        </td>
    </template>
</template>
