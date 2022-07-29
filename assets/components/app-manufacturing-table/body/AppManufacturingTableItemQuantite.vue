<script setup>
import { defineProps, ref } from 'vue'

    const props = defineProps({
        field: { required: true, type: Object },
        item: { required: true, type: Object },
    })
    const value = ref(props.item[props.field.name])
    const e = `La quantité minimale de production est ${props.item.quantiteMin} !`

</script>

<template>
    <td v-if="value >= item.quantiteMin">
        <AppInputGuesser v-model="value" :field="field" no-label />
    </td>
    <td v-else class="bg-warn">
        <span>
            <Fa icon="exclamation-triangle" :title="e" class="style-icon" />
        </span>
        <span>
            <AppInputGuesser v-model="value" :field="field" no-label />
        </span>
    </td>
</template>
