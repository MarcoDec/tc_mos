<script  setup>
import { defineProps } from 'vue'

const props = defineProps({
    fields: { required: true, type: Array },
    item: { required: true, type: Object },
    title: { required: true, type: String }
})
</script>

<template>
    <tr>
        <component
            :is="field.name === 'quantite' && title === 'collapse new Ofs' ? 'AppManufacturingTableItemQuantite' : 'AppManufacturingTableItemField'"
            v-for="field in fields" :key="field.name" :field="field" :item="item" :title="title" />
    </tr>
</template>
