<script setup>
    defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        item: {required: true, type: Object},
        index: {required: true, type: Number}
    })
</script>

<template>
    <td>
        <AppInputGuesser
            :id="field.name"
            v-model="item.prices[index][field.name]"
            :field="field"
            :form="form"
            :item="item"/>
    </td>
</template>
