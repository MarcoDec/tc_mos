<script setup>
    import {defineProps, ref} from 'vue'

    const props = defineProps({
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        item: {required: true, type: Object}
    })
    const value = ref(props.item[props.field.name])
    const e = `La quantité minimale de production est ${props.item.quantiteMin} !`
</script>

<template>
    <td v-if="value >= item.quantiteMin">
        <AppInputGuesser :id="id" v-model="value" :field="field" :form="form" no-label/>
    </td>
    <td v-else class="bg-warning">
        <span>
            <Fa icon="exclamation-triangle" :title="e" class="style-icon"/>
        </span>
        <span>
            <AppInputGuesser :id="id" v-model="value" :field="field" :form="form" no-label/>
        </span>
    </td>
</template>
