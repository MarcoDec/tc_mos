<script  setup>
    import {defineProps} from 'vue'
    defineProps({
        fields: {required: true, type: Array},
        form: {required: true, type: String},
        id: {required: true, type: String},
        item: {required: true, type: Object},
        title: {required: true, type: String}
    })
</script>

<template>
    <tr>
        <component
            :is="field.name === 'quantite' && title === 'collapse new Ofs' ? 'AppManufacturingTableItemQuantite' : 'AppManufacturingTableItemField'"
            v-for="field in fields" :id="id" :key="field.name" :field="field" :item="item" :title="title" :form="form"/>
    </tr>
</template>
